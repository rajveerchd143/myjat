<?php

require_once __DIR__ . '/../setting/module-loader.php';
require_once __DIR__ . '/../setting/asset-loader.php';

$slug = $_GET['slug'] ?? '';

$function = 'myjat_' . $slug . '_home';

if (!function_exists($function)) {
    http_response_code(404);
    exit('Great page not found.');
}

?>
<!DOCTYPE html>
<html lang="hi">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/setting/style.css">

    <title>MYJAT</title>

</head>

<body>

<?php echo $function(); ?>

<script type="module" src="/setting/script.js"></script>

</body>
</html>