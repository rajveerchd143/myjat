<?php

/*
|--------------------------------------------------------------------------
| MYJAT Project Audit
|--------------------------------------------------------------------------
| TEMPORARY DEBUG TOOL
|
| Open:
| http://localhost/system/project-audit.php
|
| Delete this file after cleanup.
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', '1');

$ROOT = realpath(__DIR__ . '/..');

$SCAN_DIRS = array(
    'assets',
    'includes',
    'setting',
    'system',
);

$IGNORE_FILES = array(
    'project-audit.php',
);

$EXTENSIONS = array(
    'php',
    'css',
    'js',
);

$files = array();

foreach ($SCAN_DIRS as $dir) {

    $path = $ROOT . DIRECTORY_SEPARATOR . $dir;

    if (!is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $path,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $file) {

        if (!$file->isFile()) {
            continue;
        }

        $extension = strtolower($file->getExtension());

        if (!in_array($extension, $EXTENSIONS, true)) {
            continue;
        }

        $full = $file->getPathname();

        $relative = str_replace(
            $ROOT . DIRECTORY_SEPARATOR,
            '',
            $full
        );

        $relative = str_replace('\\', '/', $relative);

        if (in_array(basename($relative), $IGNORE_FILES, true)) {
            continue;
        }

        $files[$relative] = array(
            'path' => $full,
            'type' => $extension,
            'content' => @file_get_contents($full),
        );
    }
}

ksort($files);

$all_content = '';

foreach ($files as $file) {
    $all_content .= "\n\n";
    $all_content .= $file['content'];
}


/*
|--------------------------------------------------------------------------
| PHP require/include references
|--------------------------------------------------------------------------
*/

$php_references = array();

preg_match_all(
    '/(?:require_once|require|include_once|include)\s*\(?\s*[^;]*?[\'"]([^\'"]+)[\'"]\s*\)?\s*;/i',
    $all_content,
    $matches
);

if (!empty($matches[1])) {
    $php_references = $matches[1];
}


/*
|--------------------------------------------------------------------------
| CSS imports
|--------------------------------------------------------------------------
*/

$css_references = array();

preg_match_all(
    '/@import\s+[\'"]([^\'"]+)[\'"]\s*;/i',
    $all_content,
    $matches
);

if (!empty($matches[1])) {
    $css_references = $matches[1];
}


/*
|--------------------------------------------------------------------------
| JS imports
|--------------------------------------------------------------------------
*/

$js_references = array();

preg_match_all(
    '/(?:import\s+.*?\s+from\s+|import\s*)[\'"]([^\'"]+)[\'"]/i',
    $all_content,
    $matches
);

if (!empty($matches[1])) {
    $js_references = $matches[1];
}


/*
|--------------------------------------------------------------------------
| Direct URL references
|--------------------------------------------------------------------------
*/

$url_references = array();

preg_match_all(
    '/[\'"`]([^\'"`\s]+?\.(?:php|css|js|webp|png|jpg|jpeg|svg))[\'"`]/i',
    $all_content,
    $matches
);

if (!empty($matches[1])) {
    $url_references = $matches[1];
}


/*
|--------------------------------------------------------------------------
| Normalize references
|--------------------------------------------------------------------------
*/

function myjat_audit_normalize($value)
{
    $value = str_replace('\\', '/', trim($value));

    $value = preg_replace('/^\.\//', '', $value);

    $value = preg_replace('/^\//', '', $value);

    return $value;
}

$php_references = array_map(
    'myjat_audit_normalize',
    $php_references
);

$css_references = array_map(
    'myjat_audit_normalize',
    $css_references
);

$js_references = array_map(
    'myjat_audit_normalize',
    $js_references
);

$url_references = array_map(
    'myjat_audit_normalize',
    $url_references
);


/*
|--------------------------------------------------------------------------
| Find referenced files
|--------------------------------------------------------------------------
*/

$referenced_files = array();

foreach ($files as $relative => $file) {

    $normalized = myjat_audit_normalize($relative);

    foreach ($php_references as $reference) {

        if (
            strpos($normalized, $reference) !== false ||
            strpos($reference, basename($normalized)) !== false
        ) {
            $referenced_files[$relative] = true;
        }
    }

    foreach ($css_references as $reference) {

        if (
            strpos($normalized, $reference) !== false ||
            strpos($reference, basename($normalized)) !== false
        ) {
            $referenced_files[$relative] = true;
        }
    }

    foreach ($js_references as $reference) {

        if (
            strpos($normalized, $reference) !== false ||
            strpos($reference, basename($normalized)) !== false
        ) {
            $referenced_files[$relative] = true;
        }
    }

    foreach ($url_references as $reference) {

        if (
            strpos($normalized, $reference) !== false ||
            strpos($reference, basename($normalized)) !== false
        ) {
            $referenced_files[$relative] = true;
        }
    }
}


/*
|--------------------------------------------------------------------------
| WordPress remnants
|--------------------------------------------------------------------------
*/

$wp_patterns = array(
    'ABSPATH',
    'WP_',
    'wp_',
    'add_action',
    'add_filter',
    'add_shortcode',
    'do_action',
    'apply_filters',
    'get_option',
    'update_option',
    'delete_option',
    'get_user_meta',
    'update_user_meta',
    'get_current_user_id',
    'is_user_logged_in',
    'current_user_can',
    'wp_get_current_user',
    'wp_get_referer',
    'wp_safe_redirect',
    'wp_redirect',
    'wp_die',
    'wp_send_json',
    'wp_remote_get',
    'wp_remote_post',
    'wp_signon',
    'wp_set_auth_cookie',
    'wp_clear_auth_cookie',
    'wp_logout',
    'wp_create_user',
    'wp_generate_password',
    'wpdb',
    'WP_User',
    'WP_Error',
);

$wp_findings = array();

foreach ($files as $relative => $file) {

    if ($file['type'] !== 'php') {
        continue;
    }

    foreach ($wp_patterns as $pattern) {

        if (stripos($file['content'], $pattern) !== false) {

            $wp_findings[$relative][] = $pattern;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

$functions = array();

foreach ($files as $relative => $file) {

    if ($file['type'] !== 'php') {
        continue;
    }

    preg_match_all(
        '/function\s+([a-zA-Z0-9_]+)\s*\(/',
        $file['content'],
        $matches
    );

    if (!empty($matches[1])) {

        foreach ($matches[1] as $function) {

            $functions[$function] = $relative;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Function usages
|--------------------------------------------------------------------------
*/

$function_usage = array();

foreach ($functions as $function => $defined_in) {

    $count = 0;
    $used_in = array();

    foreach ($files as $relative => $file) {

        $matches = array();

        preg_match_all(
            '/\b' . preg_quote($function, '/') . '\s*\(/',
            $file['content'],
            $matches
        );

        if (!empty($matches[0])) {

            $count += count($matches[0]);

            $used_in[] = $relative;
        }
    }

    $function_usage[$function] = array(
        'defined_in' => $defined_in,
        'count' => $count,
        'used_in' => array_unique($used_in),
    );
}


/*
|--------------------------------------------------------------------------
| CSS selectors / JS files
|--------------------------------------------------------------------------
*/

$total_php = 0;
$total_css = 0;
$total_js = 0;

foreach ($files as $file) {

    if ($file['type'] === 'php') {
        $total_php++;
    }

    if ($file['type'] === 'css') {
        $total_css++;
    }

    if ($file['type'] === 'js') {
        $total_js++;
    }
}


/*
|--------------------------------------------------------------------------
| Output
|--------------------------------------------------------------------------
*/

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>MYJAT Project Audit</title>

<style>

body {
    font-family: Arial, sans-serif;
    background: #f5f6f8;
    color: #222;
    margin: 0;
    padding: 30px;
}

h1 {
    margin-top: 0;
}

h2 {
    margin-top: 35px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(160px,1fr));
    gap: 15px;
}

.stat {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
}

.stat strong {
    display: block;
    font-size: 30px;
    margin-top: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

th,
td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}

th {
    background: #f0f0f0;
}

.ok {
    color: #16803c;
    font-weight: bold;
}

.warning {
    color: #b56a00;
    font-weight: bold;
}

.danger {
    color: #c62828;
    font-weight: bold;
}

code {
    background: #f1f1f1;
    padding: 3px 6px;
    border-radius: 4px;
}

ul {
    margin-top: 8px;
}

.small {
    color: #666;
    font-size: 13px;
}

</style>

</head>

<body>

<h1>MYJAT Project Audit</h1>

<p class="small">
Temporary diagnostic tool. No files are modified or deleted.
</p>


<div class="stats">

    <div class="stat">
        PHP Files
        <strong><?= $total_php ?></strong>
    </div>

    <div class="stat">
        CSS Files
        <strong><?= $total_css ?></strong>
    </div>

    <div class="stat">
        JS Files
        <strong><?= $total_js ?></strong>
    </div>

    <div class="stat">
        Total
        <strong><?= count($files) ?></strong>
    </div>

</div>


<h2>1. All Project Files</h2>

<div class="card">

<table>

<tr>
    <th>File</th>
    <th>Type</th>
    <th>Referenced</th>
</tr>

<?php foreach ($files as $relative => $file): ?>

<tr>

<td>
    <code><?= htmlspecialchars($relative) ?></code>
</td>

<td>
    <?= strtoupper($file['type']) ?>
</td>

<td>

<?php if (isset($referenced_files[$relative])): ?>

<span class="ok">YES</span>

<?php else: ?>

<span class="warning">NOT FOUND</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

</div>


<h2>2. Possible Unreferenced Files</h2>

<div class="card">

<?php

$unused_count = 0;

foreach ($files as $relative => $file):

    if (!isset($referenced_files[$relative])):

        $unused_count++;

?>

<div class="warning">
    ⚠ <?= htmlspecialchars($relative) ?>
</div>

<?php

    endif;

endforeach;

if ($unused_count === 0):

?>

<p class="ok">
    ✓ No obviously unreferenced files found.
</p>

<?php endif; ?>

<p class="small">
    WARNING: This is only a static reference check.
    Dynamically loaded files may appear here even when they are actually required.
</p>

</div>


<h2>3. WordPress Dependencies / Remnants</h2>

<div class="card">

<?php if (empty($wp_findings)): ?>

<p class="ok">
    ✓ No obvious WordPress references found.
</p>

<?php else: ?>

<table>

<tr>
    <th>File</th>
    <th>Detected</th>
</tr>

<?php foreach ($wp_findings as $file => $patterns): ?>

<tr>

<td>
    <code><?= htmlspecialchars($file) ?></code>
</td>

<td>

<?php foreach (array_unique($patterns) as $pattern): ?>

<code><?= htmlspecialchars($pattern) ?></code>

<?php endforeach; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</div>


<h2>4. PHP Functions</h2>

<div class="card">

<table>

<tr>
    <th>Function</th>
    <th>Defined In</th>
    <th>Usage Count</th>
    <th>Files Using It</th>
</tr>

<?php foreach ($function_usage as $function => $info): ?>

<tr>

<td>
    <code><?= htmlspecialchars($function) ?></code>
</td>

<td>
    <?= htmlspecialchars($info['defined_in']) ?>
</td>

<td>

<?php if ($info['count'] > 1): ?>

<span class="ok">
    <?= $info['count'] ?>
</span>

<?php else: ?>

<span class="warning">
    <?= $info['count'] ?>
</span>

<?php endif; ?>

</td>

<td>

<?php foreach ($info['used_in'] as $used): ?>

<div>
    <?= htmlspecialchars($used) ?>
</div>

<?php endforeach; ?>

</td>

</tr>

<?php endforeach; ?>

</table>

</div>


<h2>5. CSS Imports</h2>

<div class="card">

<?php if (empty($css_references)): ?>

<p class="warning">
    No CSS @import references detected.
</p>

<?php else: ?>

<ul>

<?php foreach (array_unique($css_references) as $reference): ?>

<li>
    <code><?= htmlspecialchars($reference) ?></code>
</li>

<?php endforeach; ?>

</ul>

<?php endif; ?>

</div>


<h2>6. JavaScript Imports</h2>

<div class="card">

<?php if (empty($js_references)): ?>

<p class="warning">
    No JavaScript imports detected.
</p>

<?php else: ?>

<ul>

<?php foreach (array_unique($js_references) as $reference): ?>

<li>
    <code><?= htmlspecialchars($reference) ?></code>
</li>

<?php endforeach; ?>

</ul>

<?php endif; ?>

</div>


<h2>7. Direct Asset References</h2>

<div class="card">

<ul>

<?php foreach (array_unique($url_references) as $reference): ?>

<li>
    <code><?= htmlspecialchars($reference) ?></code>
</li>

<?php endforeach; ?>

</ul>

</div>


<h2>8. Audit Summary</h2>

<div class="card">

<p>
<strong>Project root:</strong>
<?= htmlspecialchars($ROOT) ?>
</p>

<p>
<strong>PHP:</strong>
<?= $total_php ?>
</p>

<p>
<strong>CSS:</strong>
<?= $total_css ?>
</p>

<p>
<strong>JS:</strong>
<?= $total_js ?>
</p>

<p>
<strong>Total scanned:</strong>
<?= count($files) ?>
</p>

<p>
<strong>Possible unreferenced:</strong>
<?= $unused_count ?>
</p>

<p>
<strong>Files containing WordPress references:</strong>
<?= count($wp_findings) ?>
</p>

</div>


<div class="card">

<strong>Important:</strong>

<p>
This report does not delete anything.
Review the results before removing files.
Dynamic function calls, AJAX handlers, manually opened PHP pages,
HTML references and server-side entry points can make a file appear
unused in a static scan.
</p>

</div>

</body>

</html>