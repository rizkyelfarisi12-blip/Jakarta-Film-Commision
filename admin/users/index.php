<?php

require_once __DIR__ . "/../includes/auth.php";
requireRole(["super_admin"]);

$pageTitle = "User Management";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>


    <main class="main-content">

        <header class="dashboard-header">

            <div>
                <h1>User Management</h1>
                <p>Manage admin accounts and access levels</p>
            </div>

            <a href="form.php" class="btn btn-primary">
                <i class="ri-add-line"></i>
                New User
            </a>

        </header>


        <section class="dashboard-stats">

            <div class="stat-card">
                <div class="stat-card-content">
                    <span class="stat-label">Total Users</span>
                    <strong class="stat-value" id="totalUsers">0</strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-content">
                    <span class="stat-label">Active</span>
                    <strong class="stat-value" id="activeUsers">0</strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-content">
                    <span class="stat-label">Inactive</span>
                    <strong class="stat-value" id="inactiveUsers">0</strong>
                </div>
            </div>

        </section>


        <section class="dashboard-card">

            <div class="card-header">

                <div class="event-table-tools">

                    <input
                        type="text"
                        id="searchUser"
                        placeholder="Search Name, Username or Email..."
                        class="table-search"
                        autocomplete="off">

                    <select id="roleFilter" class="table-filter">

                        <option value="">All Role</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="content_admin">Content Admin</option>
                        <option value="communication_admin">Communication Admin</option>
                        <option value="membership_admin">Membership Admin</option>

                    </select>

                    <select id="statusFilter" class="table-filter">

                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>

                    </select>

                    <select id="sortFilter" class="table-filter">

                        <option value="updated_desc">Sort: Last Updated (Newest)</option>
                        <option value="updated_asc">Sort: Last Updated (Oldest)</option>
                        <option value="login_desc">Sort: Last Login (Newest)</option>
                        <option value="name_asc">Sort: Name (A-Z)</option>
                        <option value="name_desc">Sort: Name (Z-A)</option>

                    </select>

                </div>

            </div>


            <div class="table-responsive">

                <table class="admin-table">

                    <thead>

                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th width="160">Action</th>
                        </tr>

                    </thead>

                    <tbody id="userTable">

                        <tr>
                            <td colspan="5" style="text-align:center;">
                                Loading Users...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="../assets/js/users.js"></script>


<?php include "../includes/footer.php"; ?>