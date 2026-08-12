<?php

$pageTitle="Dashboard";
$assetPath = "";

include 'includes/header.php';

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
            <h2>12</h2>
            <span>Upcoming</span>
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

    <section class="dashboard-row">

        <div class="dashboard-card quick-action">
            <h2>Quick Actions</h2>
            <a href="#">+ New Press Release</a>
            <a href="#">+ New Event</a>
            <a href="#">+ New Gallery</a>
        </div>

        <div class="dashboard-card activity">

            <h2>Recent Activity</h2>

            <ul>
                <li>Published "Jakarta Film Summit"</li>
                <li>Edited Event "Festival Film"</li>
                <li>Gallery Updated</li>
            </ul>

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
