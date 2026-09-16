<?php

require_once __DIR__ . "/../includes/auth.php";
// requireRole(["super_admin", "service_admin"]); // aktifkan & sesuaikan kalau sudah menentukan role-nya

$pageTitle = "Contact Messages";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">

        <header class="dashboard-header">

            <div>
                <h1>Contact Messages</h1>
                <p>View messages submitted through the contact form</p>
            </div>

        </header>

        <section class="dashboard-card">

            <div class="coming-soon">

                <div class="coming-soon-icon">
                    <i class="ri-mail-line"></i>
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
