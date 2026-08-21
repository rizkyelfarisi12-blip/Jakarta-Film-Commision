<?php

$current = basename($_SERVER['PHP_SELF']);

?>

<aside class="sidebar">

    <div class="sidebar-logo">

        <img
            src="<?= $assetPath ?>assets/icon/JakartaFilmCommissionLogo-9.png"
            alt="JFC Logo"
        >

        <div class="sidebar-brand">

            <strong>JFC Admin</strong>

            <span>Jakarta Film Commission</span>

        </div>

    </div>


    <nav class="sidebar-menu">


        <!-- =========================
             MAIN
        ========================== -->

        <a
            class="<?= $current == 'dashboard.php' ? 'active' : '' ?>"
            href="<?= $assetPath ?>dashboard.php"
        >
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>


        <!-- =========================
             CONTENT
        ========================== -->

        <span class="menu-title">
            Content
        </span>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], 'press-release') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>press-release/index.php"
        >
            <i class="ri-article-line"></i>
            <span>Press Release</span>
        </a>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/events/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>events/index.php"
        >
            <i class="ri-calendar-event-line"></i>
            <span>Events</span>
        </a>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], 'supported-film') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>supported-film/index.php"
        >
            <i class="ri-movie-2-line"></i>
            <span>Supported Film</span>
        </a>


        <!-- =========================
             MEMBERSHIP
        ========================== -->

        <span class="menu-title">
            Membership
        </span>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/individual/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>individual/index.php"
        >
            <i class="ri-user-line"></i>
            <span>Individual</span>
        </a>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/company/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>company/index.php"
        >
            <i class="ri-building-line"></i>
            <span>Company</span>
        </a>


        <!-- =========================
             SERVICES
        ========================== -->

        <span class="menu-title">
            Services
        </span>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/contact/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>contact/index.php"
        >
            <i class="ri-mail-line"></i>
            <span>Contact</span>
        </a>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/permit/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>permit/index.php"
        >
            <i class="ri-file-list-3-line"></i>
            <span>Permit</span>
        </a>


        <!-- =========================
             SETTINGS
        ========================== -->

        <span class="menu-title">
            Settings
        </span>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/users/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>users/index.php"
        >
            <i class="ri-user-settings-line"></i>
            <span>Users</span>
        </a>


        <a
            class="<?= strpos($_SERVER['PHP_SELF'], '/website/') !== false ? 'active' : '' ?>"
            href="<?= $assetPath ?>website/index.php"
        >
            <i class="ri-global-line"></i>
            <span>Website</span>
        </a>


        <!-- =========================
             ACCOUNT
        ========================== -->

        <span class="menu-title">
            Account
        </span>


        <a href="<?= $assetPath ?>logout.php">
            <i class="ri-logout-box-r-line"></i>
            <span>Logout</span>
        </a>


    </nav>

</aside>