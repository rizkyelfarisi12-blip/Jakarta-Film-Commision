<?php

require_once __DIR__ . "/../includes/auth.php";
requireRole(["super_admin"]); // pengaturan situs sebaiknya khusus Super Admin

$pageTitle = "Website Settings";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">

        <header class="dashboard-header">

            <div>
                <h1>Website Settings</h1>
                <p>Manage general website configuration</p>
            </div>

        </header>

        <section class="dashboard-card">

            <div class="coming-soon">

                <div class="coming-soon-icon">
                    <i class="ri-global-line"></i>
                </div>

                <h2>Coming Soon</h2>

                <p>
                    This section is under development. Check back soon!
                </p>

            </div>

        </section>

    </main>

</div>

<?php include "../includes/footer.php"; ?>
