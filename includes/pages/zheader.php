<?php

$current_page = basename($_SERVER['PHP_SELF']);

$myjat_user = $_SESSION['myjat_user'] ?? null;

?>

<header class="myjat-header myjat-glassclear">

    <div class="myjat-header-inner">

        <button
            class="myjat-menu-toggle"
            type="button"
            aria-label="मेन्यू खोलें"
            aria-expanded="false"
            aria-controls="myjat-header-menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <!-- BRAND -->

        <div class="myjat-header-left">

            <a
                href="/"
                class="myjat-brand"
                aria-label="merejat">

                <span class="myjat-brand-mere">
                    मेरे
                </span>

                <span class="myjat-brand-jat">
                    जाट
                </span>

            </a>

        </div>

        <div class="myjat-header-menu" id="myjat-header-menu">


            <!-- NAVIGATION -->

            <nav
                class="myjat-nav myjat glassclear"
                aria-label="menu">

                <a href="/">
                    होम
                </a>

                <a href="/estab/">
                    स्थापना
                </a>

                <a href="/kalamse/">
                    कलम से
                </a>

                <a href="/sinhavlocan/">
                    सिंहावलोकन
                </a>

                <a href="/about/">
                    हमारे बारे में
                </a>
                <a href="/register/">
                    सदस्य बनें
                </a>


                <a href="/contact/">
                    संपर्क
                </a>


            </nav>


            <!-- USER AREA -->

            <div class="myjat-header-right">

                <?php if ($myjat_user): ?>


                    <?php if (!empty($myjat_user['picture'])): ?>

                        <img
                            src="<?= htmlspecialchars(
                                        $myjat_user['picture'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                            alt="">

                    <?php endif; ?>


                    <span>

                        <?= htmlspecialchars(
                            $myjat_user['name'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                        जी,
                        आपका हार्दिक स्वागत है 🙏

                    </span>


                    <!-- MEMBER DASHBOARD -->

                    <?php if (!empty($myjat_user)): ?>

                        <?php if (($myjat_user['role'] ?? '') === 'admin'): ?>

                            <a href="/membership-admin/" class="myjat-dashboard-link">
                                Admin Dashboard
                            </a>

                        <?php else: ?>

                            <a href="/member-dashboard/" class="myjat-dashboard-link">
                                Member Dashboard
                            </a>

                        <?php endif; ?>

                    <?php endif; ?>

                    <!-- LOGOUT -->

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


    </div>

</header>