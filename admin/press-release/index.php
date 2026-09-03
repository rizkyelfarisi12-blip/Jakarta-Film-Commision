<?php

$pageTitle = "Press Release Management";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>


    <main class="main-content">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <header class="dashboard-header">

            <div>

                <h1>
                    Press Release Management
                </h1>

                <p>
                    Manage all Jakarta Film Commission Press Releases
                </p>

            </div>


            <a href="form.php" class="btn btn-primary">
                <i class="ri-add-line"></i>
                New Press Release
            </a>

        </header>


        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="dashboard-stats">

            <!-- TOTAL -->
            <div class="stat-card">
                <div class="stat-card-content">

                    <span class="stat-label">
                        Total Press Release
                    </span>

                    <strong class="stat-value" id="totalPressRelease">
                        0
                    </strong>

                </div>
            </div>

            <!-- PUBLISHED -->
            <div class="stat-card">
                <div class="stat-card-content">

                    <span class="stat-label">
                        Published
                    </span>

                    <strong class="stat-value" id="publishedPressRelease">
                        0
                    </strong>

                </div>
            </div>

            <!-- DRAFT -->
            <div class="stat-card">
                <div class="stat-card-content">

                    <span class="stat-label">
                        Draft
                    </span>

                    <strong class="stat-value" id="draftPressRelease">
                        0
                    </strong>

                </div>
            </div>

        </section>


        <!-- =====================================================
             TABLE CARD
        ====================================================== -->

        <section class="dashboard-card">


            <!-- =================================================
                 CARD HEADER
            ================================================== -->

            <div class="card-header">

                <div class="event-table-tools">


                    <!-- SEARCH -->

                    <input type="text" id="searchPressRelease" placeholder="Search Press Release..."
                        class="table-search" autocomplete="off">


                    <!-- STATUS -->

                    <select id="statusFilter" class="table-filter">

                        <option value="">
                            All Status
                        </option>

                        <option value="published">
                            Published
                        </option>

                        <option value="draft">
                            Draft
                        </option>

                    </select>


                    <!-- CATEGORY -->

                    <select id="categoryFilter" class="table-filter">

                        <option value="">
                            All Category
                        </option>

                    </select>


                    <!-- DATE FROM -->

                    <input
                        type="date"
                        id="dateFromFilter"
                        class="table-filter"
                        title="From Date">


                    <!-- DATE TO -->

                    <input
                        type="date"
                        id="dateToFilter"
                        class="table-filter"
                        title="To Date">


                    <!-- SORT -->

                    <select id="sortFilter" class="table-filter">

                        <option value="updated_desc">
                            Sort: Last Updated (Newest)
                        </option>

                        <option value="updated_asc">
                            Sort: Last Updated (Oldest)
                        </option>

                        <option value="date_desc">
                            Sort: Published Date (Newest)
                        </option>

                        <option value="date_asc">
                            Sort: Published Date (Oldest)
                        </option>

                    </select>


                </div>

            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Image
                            </th>

                            <th>
                                Title
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="180">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody id="pressReleaseTable">

                        <tr>

                            <td colspan="6" style="text-align:center;">
                                Loading Press Releases...
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

<script src="../assets/js/press-release.js"></script>


<?php include "../includes/footer.php"; ?>