<?php

$pageTitle="Dashboard";
$assetPath = "";

include 'includes/header.php';
require_once __DIR__ . "/auth.php";

?>

<div class="admin-layout">

<?php include 'includes/sidebar.php'; ?>

    <!-- =====================
    MAIN
    ====================== -->
    <main class="main-content">

    <header class="dashboard-header">

        <div>
            <h1>Dashboard</h1>
            <p>
                Welcome back, Admin.
            </p>
        </div>

        <div class="dashboard-user">
            <img src="assets/icon/JFC Logo 2BW.png">
            <div>
                <strong>Administrator</strong>
                <span>Super Admin</span>
            </div>
        </div>

    </header>

    <section class="dashboard-stats">

        <div class="stat-card orange">
            <h4>Total Articles</h4>
            <h2 id="totalArticles">0</h2>
            <span>Press Release</span>
        </div>

        <div class="stat-card green">
            <h4>Events</h4>
            <h2 id="totalEvents">0</h2>
            <span>Published</span>
        </div>

        <div class="stat-card yellow">
            <h4>Members</h4>
            <h2>128</h2>
            <span>Approved</span>
        </div>

        <div class="stat-card red">
            <h4>Contacts</h4>
            <h2>16</h2>
            <span>Waiting</span>
        </div>

    </section>

    <!-- =========================================
        EVENT DASHBOARD
    ========================================= -->

    <section class="dashboard-event-grid">

        <!-- LATEST EVENTS -->
        <div class="dashboard-card">

            <div class="card-header">

                <div>
                    <h2>Latest Events</h2>
                    <p class="dashboard-section-description">
                        Recently added events
                    </p>
                </div>

                <a
                    href="events/index.php"
                    class="dashboard-view-all">

                    View All

                </a>

            </div>

            <div
                id="latestEvents"
                class="latest-events">

                <div class="dashboard-loading">
                    Loading events...
                </div>

            </div>

        </div>


        <!-- FEATURED EVENT -->
        <div class="dashboard-card">

            <div class="card-header">

                <div>
                    <h2>Featured Event</h2>
                    <p class="dashboard-section-description">
                        Currently featured event
                    </p>
                </div>

            </div>

            <div
                id="featuredEvent"
                class="featured-event">

                <div class="dashboard-loading">
                    Loading featured event...
                </div>

            </div>

        </div>

    </section>

    <section class="dashboard-card">

        <div class="card-header">
            <h2>Latest Press Release</h2>
        </div>

        <table class="admin-table">

            <thead>

                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th></th>
                </tr>

            </thead>

            <tbody id="recentArticles"></tbody>

        </table>

    </section>

</main>

</div>
<script src="../assets/data/press-release-data.js"></script>
<script src="../assets/js/press-release.js"></script>
<script src="assets/js/dashboard.js"></script>


<?php include 'includes/footer.php';?>
