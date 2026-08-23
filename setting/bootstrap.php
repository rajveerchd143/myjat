<?php

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/module-loader.php';
require_once __DIR__ . '/asset-loader.php';
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

<?php echo myjat_home(); ?>

<script type="module" src="/setting/script.js"></script>

</body>
</html>