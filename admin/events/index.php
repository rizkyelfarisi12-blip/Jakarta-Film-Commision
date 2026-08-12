<?php

$pageTitle = "Event Management";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

<?php include "../includes/sidebar.php"; ?>

<main class="main-content">

    <header class="dashboard-header">

        <div>
            <h1>Event Management</h1>
            <p>Manage all Jakarta Film Commission Events</p>
        </div>

        <a href="form.php" class="btn btn-primary">
            + New Event
        </a>

    </header>


    <section class="dashboard-card">

        <div class="card-header">

            <div class="event-table-tools">

                <input
                    type="text"
                    id="searchEvent"
                    placeholder="Search Event..."
                    class="table-search"
                    onkeyup="searchEvent()">

                <select
                    id="statusFilter"
                    class="table-filter"
                    onchange="filterEvents()">

                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <!-- <option value="archived">Archived</option> -->

                </select>

            </div>

        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th width="180">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody id="eventTable"></tbody>
        </table>

    </section>

</main>

</div>

<script src="../assets/js/events.js"></script>

<?php include "../includes/footer.php"; ?>