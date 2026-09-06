<?php
declare(strict_types=1);

/**
 * MYJAT Project Audit v5
 *
 * READ-ONLY audit.
 * Goals:
 * - real PHP CLI syntax checking (avoids Apache AH02965 false failures)
 * - route-aware internal link checking
 * - asset/file checking
 * - stale WordPress/debug/domain reference detection
 * - duplicate function detection
 * - safe GET route smoke tests
 * - database connectivity/count checks
 *
 * IMPORTANT:
 * This file does not execute POST/state-changing actions.
 */

session_start();

$ROOT = dirname(__DIR__);
$SELF = str_replace('\\', '/', realpath(__FILE__) ?: '');
$BASE_URL = rtrim(
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
    '/'
);

$hardFailures = 0;
$warnings = 0;
$passes = 0;

function audit_result(string $status, string $message): void
{
    global $hardFailures, $warnings, $passes;
    if ($status === 'FAIL') $hardFailures++;
    elseif ($status === 'WARN') $warnings++;
    else $passes++;

    $class = $status === 'PASS' ? 'pass' : ($status === 'FAIL' ? 'fail' : 'warn');
    echo '<tr><td class="' . $class . '">' . htmlspecialchars($status) .
         '</td><td>' . htmlspecialchars($message) . '</td></tr>';
}

function audit_files(string $root): array
{
    $out = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($it as $file) {
        if (!$file->isFile()) continue;
        $path = str_replace('\\', '/', $file->getPathname());
        if (preg_match('#/(?:\.git|uploads)/#i', $path)) continue;
        if (preg_match('#/system/project-audit(?:-v\d+)?\.php$#i', $path)) continue;
        $out[] = $path;
    }
    sort($out);
    return $out;
}

function rel_path(string $path, string $root): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}

function find_php_cli(): ?string
{
    $candidates = [];

    if (defined('PHP_BINDIR')) {
        $candidates[] = rtrim(PHP_BINDIR, '/\\') . DIRECTORY_SEPARATOR . 'php.exe';
        $candidates[] = rtrim(PHP_BINDIR, '/\\') . DIRECTORY_SEPARATOR . 'php';
    }

    if (defined('PHP_BINARY')) {
        $dir = dirname(PHP_BINARY);
        $candidates[] = $dir . DIRECTORY_SEPARATOR . 'php.exe';
        $candidates[] = $dir . DIRECTORY_SEPARATOR . 'php';
    }

    $env = getenv('PHP_CLI');
    if ($env) $candidates[] = $env;

    foreach ($candidates as $candidate) {
        if (is_file($candidate) && is_executable($candidate)) return $candidate;
    }

    return null;
}

function php_lint(string $file, ?string $phpCli): array
{
    if (!$phpCli) {
        return ['WARN', 'PHP CLI not found; syntax check skipped'];
    }

    $cmd = escapeshellarg($phpCli) . ' -l ' . escapeshellarg($file) . ' 2>&1';
    $output = shell_exec($cmd);
    $text = trim((string)$output);

    if (stripos($text, 'No syntax errors detected') !== false) {
        return ['PASS', 'Syntax OK'];
    }

    if (preg_match('/Parse error|Fatal error|syntax error/i', $text)) {
        $oneLine = preg_replace('/\s+/', ' ', $text);
        return ['FAIL', mb_substr($oneLine, 0, 220)];
    }

    return ['WARN', mb_substr(preg_replace('/\s+/', ' ', $text), 0, 220)];
}

function extract_routes(string $root): array
{
    $routes = [];
    $file = $root . '/vip/routes.php';

    if (!is_file($file)) return $routes;

    $src = file_get_contents($file) ?: '';

    // Recognise common route declarations such as '/login/' => ...
    preg_match_all(
        '~[\'"](/[^\'"]*/)[\'"]\s*=>~',
        $src,
        $m
    );

    foreach ($m[1] ?? [] as $route) {
        $routes[rtrim($route, '/') . '/'] = true;
    }

    return $routes;
}

function known_public_routes(): array
{
    return [
        '/' => true,
        '/login/' => true,
        '/logout/' => true,
        '/register/' => true,
        '/member-dashboard/' => true,
        '/application-status/' => true,
        '/application-view/' => true,
        '/member-card/' => true,
        '/member-verification/' => true,
        '/membership-admin/' => true,
        '/about/' => true,
        '/contact/' => true,
        '/estab/' => true,
        '/kalamse/' => true,
    ];
}

function route_or_file_exists(string $target, string $root, array $routes): array
{
    $target = trim($target);
    if ($target === '' || $target[0] !== '/') return [true, 'external/relative'];

    $path = parse_url($target, PHP_URL_PATH) ?: '/';
    $path = '/' . ltrim($path, '/');
    $normal = rtrim($path, '/') . '/';

    if (isset($routes[$normal]) || isset(known_public_routes()[$normal])) {
        return [true, 'registered route'];
    }

    // Existing physical public file.
    $candidate = $root . '/' . ltrim($path, '/');
    if (is_file($candidate)) return [true, 'physical file'];

    // Directory index.
    if (is_dir($candidate) && is_file(rtrim($candidate, '/\\') . '/index.php')) {
        return [true, 'directory index'];
    }

    return [false, $path];
}

function extract_internal_urls(string $source): array
{
    $urls = [];

    // href/src/action strings, including PHP-templated values.
    preg_match_all(
        '~(?:href|src|action)\s*=\s*["\']([^"\']+)["\']~i',
        $source,
        $m
    );

    foreach ($m[1] ?? [] as $url) {
        $url = html_entity_decode(trim($url), ENT_QUOTES, 'UTF-8');
        if ($url === '' || str_starts_with($url, '#')) continue;
        if (preg_match('~^(?:https?:)?//|^(?:mailto|tel|javascript):~i', $url)) continue;

        // Ignore dynamic asset expressions where no route/path can be inferred.
        if (str_contains($url, '<?') && !preg_match('~^/[^<"\']+~', $url)) continue;

        if (str_starts_with($url, '/')) {
            $urls[] = $url;
        }
    }

    return array_values(array_unique($urls));
}

function scan_patterns(array $files, string $root, array $patterns): array
{
    $hits = [];

    foreach ($files as $file) {
        $rel = rel_path($file, $root);
        $src = @file_get_contents($file);
        if ($src === false) continue;

        foreach ($patterns as $label => $regex) {
            if (preg_match($regex, $src)) {
                $hits[] = [$rel, $label];
            }
        }
    }

    return $hits;
}

/**
 * For PHP files, remove comments and strings before checking legacy
 * executable APIs. This prevents documentation/comments from becoming
 * false positives.
 */
function php_executable_source(string $src): string
{
    $tokens = token_get_all($src);
    $out = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            [$id, $text] = $token;

            if (in_array($id, [T_COMMENT, T_DOC_COMMENT, T_OPEN_TAG, T_CLOSE_TAG], true)) {
                continue;
            }

            // Strings can legitimately mention old systems in text/content.
            if (in_array($id, [T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true)) {
                continue;
            }

            $out .= $text;
        } else {
            $out .= $token;
        }
    }

    return $out;
}

function scan_php_executable_patterns(array $files, string $root, array $patterns): array
{
    $hits = [];

    foreach ($files as $file) {
        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') continue;

        $src = @file_get_contents($file);
        if ($src === false) continue;

        $code = php_executable_source($src);
        $rel = rel_path($file, $root);

        foreach ($patterns as $label => $regex) {
            if (preg_match($regex, $code)) {
                $hits[] = [$rel, $label];
            }
        }
    }

    return $hits;
}

function http_status(string $url): ?int
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 8,
            'ignore_errors' => true,
            'header' => "User-Agent: MYJAT-Audit/3.0\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);

    if (!isset($http_response_header)) return null;

    foreach ($http_response_header as $header) {
        if (preg_match('~^HTTP/\S+\s+(\d{3})~i', $header, $m)) {
            return (int)$m[1];
        }
    }

    return $body === false ? null : 200;
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>MYJAT Project Audit v5</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f5f5;color:#222;margin:0;padding:24px}
.wrap{max-width:1100px;margin:auto}
h1{margin:0 0 6px}
p{color:#666}
.box{background:#fff;border:1px solid #ddd;border-radius:10px;padding:18px;margin:16px 0}
table{width:100%;border-collapse:collapse}
th,td{padding:9px;border-bottom:1px solid #eee;text-align:left;vertical-align:top}
th{background:#fafafa}
.pass{color:#18733a;font-weight:700}.fail{color:#b42318;font-weight:700}.warn{color:#856404;font-weight:700}
.summary{display:flex;gap:12px;flex-wrap:wrap}
.badge{padding:9px 13px;border:1px solid #ddd;border-radius:8px;background:#fff}
.badge strong{display:block;font-size:20px}
.small{font-size:13px;color:#777}
</style>
</head>
<body>
<div class="wrap">
<h1>MYJAT Project Audit v5</h1>
<p>Read-only audit. State-changing POST actions are never executed.</p>

<div class="box">
<h2>1. Project Files</h2>
<table><tr><th>Type</th><th>Count</th></tr>
<?php
$files = audit_files($ROOT);
$counts = ['PHP'=>0,'CSS'=>0,'JS'=>0,'Other'=>0];
foreach ($files as $f) {
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if ($ext === 'php') $counts['PHP']++;
    elseif ($ext === 'css') $counts['CSS']++;
    elseif ($ext === 'js') $counts['JS']++;
    else $counts['Other']++;
}
foreach ($counts as $k=>$v) echo '<tr><td>'.htmlspecialchars($k).'</td><td>'.$v.'</td></tr>';
?>
</table>
</div>

<div class="box">
<h2>2. Required Files</h2>
<table><tr><th>Status</th><th>File</th></tr>
<?php
$required = [
'index.php','.htaccess','.env.example',
'vip/bootstrap.php','vip/assets.php','vip/database.php','vip/environment.php','vip/modules.php','vip/renderer.php','vip/routes.php',
 'includes/core/membership-config.php', 'includes/core/membership-auth.php', 'includes/core/membership-database.php',
 'includes/core/membership-number.php', 'includes/core/membership-utils.php', 'includes/backend/membership-applications.php',
 'includes/backend/membership-handler.php', 'includes/backend/membership-upload.php', 'includes/backend/membership-roles.php',
 'includes/frontend/membership-status.php', 'includes/frontend/membership-member-dashboard.php', 'includes/frontend/membership-pvc.php',
 'includes/frontend/membership-verification.php', 'includes/frontend/membership-view.php',
'includes/pages/home.php','includes/pages/about.php','includes/pages/contact.php',
'includes/pages/estab.php','includes/pages/kalamse.php','includes/pages/slider.php',
'includes/services/location/ajax-location.php',
'includes/services/location/class-location-service.php',
'includes/services/location/class-location-importer.php',
'includes/services/location/location-loader.php',
'assets/script/core.js','assets/script/website.js','assets/script/header-footer.js',
'assets/script/home.js','assets/script/home-slider.js','assets/script/membership-registration.js','assets/script/location.js',
];
foreach ($required as $r) {
    if (is_file($ROOT.'/'.$r)) audit_result('PASS',$r);
    else audit_result('FAIL',$r.' — required file missing');
}
?>
</table>
</div>

<div class="box">
<h2>3. PHP Syntax</h2>
<table><tr><th>Status</th><th>File</th><th>Detail</th></tr>
<?php
$phpCli = find_php_cli();
if (!$phpCli) audit_result('WARN','PHP CLI — not found; syntax checks cannot run');

foreach ($files as $f) {
    if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) !== 'php') continue;
    [$status,$detail] = php_lint($f,$phpCli);
    $rel = rel_path($f,$ROOT);
    echo '<tr><td class="'.($status==='PASS'?'pass':($status==='FAIL'?'fail':'warn')).'">'.htmlspecialchars($status).
         '</td><td>'.htmlspecialchars($rel).'</td><td>'.htmlspecialchars($detail).'</td></tr>';
    if ($status==='PASS') $passes++; elseif ($status==='FAIL') $hardFailures++; else $warnings++;
}
?>
</table>
<p class="small">v5 uses the PHP CLI binary, not PHP_BINARY when that points to Apache/httpd. If PHP CLI is unavailable, syntax checks remain warnings rather than false failures.</p>
</div>

<div class="box">
<h2>4. Duplicate PHP Functions</h2>
<table><tr><th>Status</th><th>Details</th></tr>
<?php
$defs = [];
foreach ($files as $f) {
    if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) !== 'php') continue;
    $src = @file_get_contents($f) ?: '';
    preg_match_all('~\bfunction\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(~i',$src,$m);
    foreach ($m[1] ?? [] as $fn) $defs[strtolower($fn)][] = rel_path($f,$ROOT);
}
$dupes = array_filter($defs,fn($v)=>count($v)>1);
if (!$dupes) audit_result('PASS','No duplicate function definitions');
else foreach ($dupes as $fn=>$where) audit_result('FAIL',$fn.' defined in: '.implode(', ',$where));
?>
</table>
</div>

<div class="box">
<h2>5. Internal Links / Assets</h2>
<table><tr><th>Status</th><th>File</th><th>Target</th></tr>
<?php
$routes = extract_routes($ROOT);
$routes += known_public_routes();
$routes['/admin-member-card/'] = true;
$seen = [];
foreach ($files as $f) {
    $ext = strtolower(pathinfo($f,PATHINFO_EXTENSION));
    if (!in_array($ext,['php','html','js','css'],true)) continue;
    $src = @file_get_contents($f) ?: '';
    foreach (extract_internal_urls($src) as $url) {
        $key = rel_path($f,$ROOT).'|'.$url;
        if (isset($seen[$key])) continue;
        $seen[$key]=true;
        [$ok,$why] = route_or_file_exists($url,$ROOT,$routes);
        if ($ok) {
            echo '<tr><td class="pass">PASS</td><td>'.htmlspecialchars(rel_path($f,$ROOT)).
                 '</td><td>'.htmlspecialchars($url).' — '.htmlspecialchars($why).'</td></tr>';
            $passes++;
        } else {
            echo '<tr><td class="fail">FAIL</td><td>'.htmlspecialchars(rel_path($f,$ROOT)).
                 '</td><td>'.htmlspecialchars($url).' — target not found</td></tr>';
            $hardFailures++;
        }
    }
}
?>
</table>
<p class="small">Pretty URLs are validated as routes, so /register/, /member-card/?id=..., etc. are not incorrectly treated as physical files.</p>
</div>

<div class="box">
<h2>6. Stale / Old References</h2>
<table><tr><th>Status</th><th>File</th><th>Match</th></tr>
<?php
$stale = [
'membership-form.php' => '~membership-form\.php~i',
'membership-form.css' => '~membership-form\.css~i',
'membership-form.js' => '~membership-form\.js~i',
'membership-admin-emails.php' => '~membership-admin-emails\.php~i',
'myjat_is_admin_email' => '~myjat_is_admin_email~i',
'tom-select' => '~tom-select~i',
];
$sourceFiles = array_values(array_filter($files, function($f) {
    return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['php','css','js','html'], true);
}));
$hits = scan_patterns($sourceFiles,$ROOT,$stale);
if (!$hits) audit_result('PASS','No stale renamed-file / removed-system references');
else foreach($hits as [$file,$match]) audit_result('FAIL',$file.' contains stale reference: '.$match);
?>
</table>
</div>

<div class="box">
<h2>7. WordPress Remnants</h2>
<table><tr><th>Status</th><th>File</th><th>Match</th></tr>
<?php
$wp = [
'wp_query' => '~\bwp_query\b~i',
'wpdb' => '~\bwpdb\b~i',
'wp_insert' => '~\bwp_insert\b~i',
'wp_user' => '~\bwp_user\b~i',
'wp_strip_all_tags' => '~\bwp_strip_all_tags\s*\(~i',
'wp_trim_words' => '~\bwp_trim_words\s*\(~i',
'get_permalink' => '~\bget_permalink\s*\(~i',
'get_pages' => '~\bget_pages\s*\(~i',
'has_shortcode' => '~\bhas_shortcode\s*\(~i',
'esc_url' => '~\besc_url\s*\(~i',
'esc_html' => '~\besc_html\s*\(~i',
'esc_attr' => '~\besc_attr\s*\(~i',
];
$hits = scan_php_executable_patterns($files,$ROOT,$wp);
if (!$hits) audit_result('PASS','No executable WordPress remnants detected');
else foreach($hits as [$file,$match]) audit_result('FAIL',$file.' contains executable WordPress reference: '.$match);
?>
</table>
</div>

<div class="box">
<h2>8. Debug / Temporary Code</h2>
<table><tr><th>Status</th><th>File</th><th>Match</th></tr>
<?php
$debug = [
'var_dump' => '~\bvar_dump\s*\(~',
'print_r' => '~\bprint_r\s*\(~',
'dd()' => '~\bdd\s*\(~',
'debug_backtrace' => '~\bdebug_backtrace\s*\(~',
];
$hits = scan_patterns($files,$ROOT,$debug);
if (!$hits) audit_result('PASS','No obvious debug statements detected');
else foreach($hits as [$file,$match]) audit_result('FAIL',$file.' contains debug code: '.$match);
?>
</table>
</div>

<div class="box">
<h2>9. Hardcoded Domains</h2>
<table><tr><th>Status</th><th>File</th><th>Match</th></tr>
<?php
$domains = [
'http://localhost' => '~https?://localhost(?:[:/]|$)~i',
'https://myjat.in' => '~https?://(?:www\.)?myjat\.in(?:[:/]|$)~i',
];
$codeFiles = array_values(array_filter($files, function($f) {
    return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['php','css','js'], true);
}));
$hits = scan_patterns($codeFiles,$ROOT,$domains);
if (!$hits) audit_result('PASS','No hardcoded local/production domains in code files');
else foreach($hits as [$file,$match]) audit_result('FAIL',$file.' contains hardcoded domain: '.$match);
audit_result('PASS','.env is excluded from this check because OAuth/production URLs are configuration values');
?>
</table>
</div>

<div class="box">
<h2>10. Asset Registry</h2>
<table><tr><th>Status</th><th>Asset</th></tr>
<?php
$assetFile = $ROOT.'/vip/assets.php';
$assetSrc = is_file($assetFile) ? (file_get_contents($assetFile) ?: '') : '';
preg_match_all('~[\'"](/assets/[^\'"]+)[\'"]~',$assetSrc,$am);
$assets = array_values(array_unique($am[1] ?? []));
foreach ($assets as $asset) {
    if (is_file($ROOT . '/' . ltrim($asset,'/'))) audit_result('PASS',$asset);
    else audit_result('FAIL',$asset.' — registered asset missing');
}
if (!$assets) audit_result('WARN','No /assets entries found in vip/assets.php');
?>
</table>
</div>

<div class="box">
<h2>11. Safe Route Smoke Test</h2>
<table><tr><th>Route</th><th>Status</th><th>Result</th></tr>
<?php
$smoke = [
'/',
'/login/',
'/register/',
'/member-dashboard/',
'/application-status/',
'/about/',
'/contact/',
'/estab/',
'/kalamse/',
'/member-verification/?id=TEST-0003',
];
foreach ($smoke as $route) {
    $status = http_status($BASE_URL.$route);
    if ($status !== null && $status >= 200 && $status < 400) {
        echo '<tr><td>'.htmlspecialchars($route).'</td><td>'.$status.'</td><td class="pass">PASS</td></tr>';
        $passes++;
    } else {
        echo '<tr><td>'.htmlspecialchars($route).'</td><td>'.htmlspecialchars((string)$status).'</td><td class="fail">FAIL</td></tr>';
        $hardFailures++;
    }
}
?>
</table>
</div>

<div class="box">
<h2>12. Database</h2>
<table><tr><th>Status</th><th>Check</th><th>Value</th></tr>
<?php
try {
    require_once $ROOT.'/vip/bootstrap.php';
    global $pdo;
    if (!isset($pdo) || !($pdo instanceof PDO)) throw new RuntimeException('PDO not available');

    echo '<tr><td class="pass">PASS</td><td>PDO connected</td><td>OK</td></tr>';
    $passes++;

    foreach (['membership_users','membership_applications'] as $table) {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo '<tr><td class="pass">PASS</td><td>'.htmlspecialchars($table).'</td><td>'.$count.' rows</td></tr>';
        $passes++;
    }
} catch (Throwable $e) {
    echo '<tr><td class="fail">FAIL</td><td>Database</td><td>'.htmlspecialchars($e->getMessage()).'</td></tr>';
    $hardFailures++;
}
?>
</table>
</div>

<div class="box">
<h2>13. Final Result</h2>
<div class="summary">
<div class="badge"><strong><?php echo $hardFailures; ?></strong>Hard failures</div>
<div class="badge"><strong><?php echo $warnings; ?></strong>Warnings</div>
<div class="badge"><strong><?php echo $passes; ?></strong>Passes</div>
</div>
<p>
<?php
if ($hardFailures === 0) {
    echo '<span class="pass"><strong>FINAL RESULT: PASS</strong></span> — No real hard failures detected.';
} else {
    echo '<span class="fail"><strong>FINAL RESULT: FAIL</strong></span> — Fix the hard failures above.';
}
?>
</p>
<p class="small">
v5 intentionally does not auto-click Approve, Reject, Delete, Submit, Logout, Export or other state-changing actions.
Those remain manual functional tests.
</p>
</div>

<div class="box">
<h2>14. Manual Functional Checklist</h2>
<ul>
<li>Google Login → Callback → Session → Dashboard/Admin</li>
<li>Logout</li>
<li>Registration: all 4 steps + validation</li>
<li>DOB dd/mm/yyyy conversion</li>
<li>Location: State → District → Block → Village/Panchayat → PIN</li>
<li>Photo upload + preview + saved photo path</li>
<li>Registration submit + duplicate prevention</li>
<li>Member dashboard shows the correct member/application</li>
<li>Admin: View / Approve / Reject / Export</li>
<li>PVC: test different IDs, e.g. ?id=15, ?id=16, ?id=17</li>
<li>QR → Member Verification</li>
<li>Print layout</li>
<li>Mobile menu/layout</li>
<li>Browser console: zero red errors</li>
</ul>
</div>

</div>
</body>
</html>
