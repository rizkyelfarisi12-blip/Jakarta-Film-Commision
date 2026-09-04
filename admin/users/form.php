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

        <div class="admin-form-page">


            <header class="page-header">

                <div>

                    <a href="index.php" class="back-link">
                        <i class="ri-arrow-left-line"></i>
                        Back to Users
                    </a>

                    <h1 id="pageTitle">
                        New User
                    </h1>

                </div>


                <div class="page-actions">

                    <a href="index.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="saveUserBtn"
                        onclick="saveUser()">

                        <i class="ri-save-line"></i>
                        Save User

                    </button>

                </div>

            </header>


            <form id="userForm">

                <input type="hidden" id="userId">


                <div class="form-layout">


                    <div class="form-main">


                        <section class="admin-card">

                            <h2>
                                Account Information
                            </h2>

                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="username">Username *</label>
                                    <input
                                        type="text"
                                        id="username"
                                        placeholder="e.g. johndoe"
                                        autocomplete="off">
                                    <small>
                                        3-50 characters. Letters, numbers, dot and underscore only.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input
                                        type="text"
                                        id="name"
                                        placeholder="Enter full name"
                                        autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        placeholder="name@jakartafilm.id (optional)"
                                        autocomplete="off">
                                </div>

                            </div>

                        </section>


                        <section class="admin-card">

                            <h2>
                                Password
                            </h2>

                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input
                                        type="password"
                                        id="password"
                                        placeholder="Minimum 8 characters"
                                        autocomplete="new-password">
                                    <small id="passwordHint">
                                        Minimum 8 characters.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="passwordConfirm">Confirm Password *</label>
                                    <input
                                        type="password"
                                        id="passwordConfirm"
                                        placeholder="Repeat password"
                                        autocomplete="new-password">
                                </div>

                            </div>

                        </section>


                    </div>


                    <aside class="form-sidebar">


                        <section class="admin-card">

                            <h3>
                                Role & Access
                            </h3>

                            <div class="form-group">

                                <label for="role">Role</label>

                                <select id="role">

                                    <option value="content_admin">Content Admin</option>
                                    <option value="communication_admin">Communication Admin</option>
                                    <option value="membership_admin">Membership Admin</option>
                                    <option value="super_admin">Super Admin</option>

                                </select>

                                <small id="roleHint">
                                    Determines which admin sections this user can access.
                                </small>

                            </div>

                        </section>


                        <section class="admin-card">

                            <h3>
                                Status
                            </h3>

                            <div class="form-group" style="flex-direction:row; align-items:center; justify-content:space-between;">

                                <label for="status" style="margin:0;">
                                    Active Account
                                </label>

                                <label class="switch">
                                    <input type="checkbox" id="status" checked>
                                    <span class="slider"></span>
                                </label>

                            </div>

                            <small>
                                Inactive accounts cannot log in.
                            </small>

                        </section>


                    </aside>


                </div>

            </form>

        </div>

    </main>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="../assets/js/user-form.js"></script>


<?php include "../includes/footer.php"; ?>