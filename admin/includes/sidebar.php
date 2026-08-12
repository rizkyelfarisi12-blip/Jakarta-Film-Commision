<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">

    <div class="sidebar-logo">

        <img src="assets/icon/logo-jfc-white.png" alt="JFC Logo">

        <h2>JFC Admin</h2>

    </div>

    <nav class="sidebar-menu">

        <a class="<?= $current=='dashboard.php'?'active':'' ?>" href="<?= $assetPath ?>dashboard.php">
            Dashboard
        </a>

        <span class="menu-title">
            CONTENT
        </span>

        <a class="<?= strpos($_SERVER['PHP_SELF'],'press-release')!==false?'active':'' ?>"
           href="press-release/index.php">
            Press Release
        </a>

        <a href="<?= $assetPath ?>events/index.php">
            Events
        </a>

        <a href="<?= $assetPath ?>supported-film/index.php">
            Supported Film
        </a>

        <span class="menu-title">
            MEMBERSHIP
        </span>

        <a href="<?= $assetPath ?>individual/index.php">
            Individual
        </a>

        <a href="<?= $assetPath ?>company/index.php">
            Company
        </a>

        <span class="menu-title">
            SERVICES
        </span>

        <a href="<?= $assetPath ?>contact/index.php">
            Contact
        </a>

        <a href="<?= $assetPath ?>permit/index.php">
            Permit
        </a>

        <span class="menu-title">
            SETTINGS
        </span>

        <a href="../users/index.php">
            Users
        </a>

        <a href="../website/index.php">
            Website
        </a>

        <span class="menu-title">
            ACCOUNT
        </span>

        <a href="../logout.php">
            Logout
        </a>

    </nav>

</aside>