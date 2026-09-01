<?php
$current_page = basename($_SERVER['PHP_SELF']);
$myjat_user = $_SESSION['myjat_user'] ?? null;
?>

<header class="myjat-header myjat-glass-clear">
    <div class="myjat-header-inner">

        <div class="myjat-header-left myjat-glass">
            <a href="/" class="myjat-brand" aria-label="merejat">
                <span class="myjat-brand-mere">मेरे</span>
                <span class="myjat-brand-jat">जाट</span>
            </a>
        </div>

        <nav class="myjat-nav myjat-glass" aria-label="menu">

            <a href="/">होम</a>

            <a href="/estab/">स्थापना</a>

            <a href="/kalamse/">कलम से</a>

            <a href="/about/">हमारे बारे में</a>

            <a href="/contact/">संपर्क</a>

        </nav>

        <div class="myjat-header-right myjat-glass">

            <?php if ($myjat_user): ?>

                <?php if (!empty($myjat_user['picture'])): ?>
                    <img
                        src="<?= htmlspecialchars($myjat_user['picture'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="">
                <?php endif; ?>

                <span>
                    आपका हार्दिक स्वागत 🙏 <?= htmlspecialchars($myjat_user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>

                <a href="/logout/">
                    Logout
                </a>

            <?php else: ?>

                <a href="/login/">
                    Login
                </a>

            <?php endif; ?>

        </div>



    </div>


</header>