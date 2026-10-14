<?php

require_once __DIR__ . "/../includes/auth.php";
requireRole(["super_admin", "membership_admin"]);

$pageTitle = "Entities Membership";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">

        <header class="dashboard-header">

            <div>
                <h1>Entities Membership</h1>
                <p>Manage company / entity membership registrations</p>
            </div>

            <div class="page-actions">

                <a href="form.php" class="btn btn-primary">
                    <i class="ri-add-line"></i>
                    New Entity
                </a>

                <button type="button" class="btn btn-secondary" id="exportExcelBtn">
                    <i class="ri-file-excel-2-line"></i>
                    Export Excel
                </button>

                <button type="button" class="btn btn-secondary" id="exportPdfBtn">
                    <i class="ri-file-pdf-2-line"></i>
                    Export PDF
                </button>

            </div>

        </header>

        <!-- =====================================================
             STATS
        ====================================================== -->
        <section class="dashboard-stats">

            <div class="stat-card">
                <div class="stat-card-content">
                    <span class="stat-label">Total Entities</span>
                    <strong class="stat-value" id="statTotal">0</strong>
                </div>
            </div>

            <div class="stat-card yellow">
                <div class="stat-card-content">
                    <span class="stat-label">Pending</span>
                    <strong class="stat-value" id="statPending">0</strong>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-card-content">
                    <span class="stat-label">Approved</span>
                    <strong class="stat-value" id="statApproved">0</strong>
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-card-content">
                    <span class="stat-label">Rejected</span>
                    <strong class="stat-value" id="statRejected">0</strong>
                </div>
            </div>

        </section>

        <!-- =====================================================
             TABLE
        ====================================================== -->
        <section class="dashboard-card">

            <div class="card-header">
                <div class="event-table-tools">

                    <input
                        type="text"
                        id="searchEntity"
                        placeholder="Search company name, PIC, or email..."
                        class="table-search"
                        autocomplete="off">

                    <select id="statusFilter" class="table-filter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <select id="entityTypeFilter" class="table-filter">
                        <option value="">All Entity Type</option>
                    </select>

                    <select id="interestFilter" class="table-filter">
                        <option value="">All Interest</option>
                    </select>

                    <input type="date" id="dateFromFilter" class="table-filter" title="From Date">

                    <input type="date" id="dateToFilter" class="table-filter" title="To Date">

                    <select id="sortFilter" class="table-filter">
                        <option value="date_desc">Sort: Newest</option>
                        <option value="date_asc">Sort: Oldest</option>
                        <option value="name_asc">Sort: Company (A-Z)</option>
                        <option value="name_desc">Sort: Company (Z-A)</option>
                    </select>

                </div>
            </div>

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Company</th>
                            <th>PIC</th>
                            <th>Contact</th>
                            <th>Production Type</th>
                            <th>Interest</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>

                    <tbody id="entityTable">
                        <tr><td colspan="9" style="text-align:center;">Loading Entities Membership...</td></tr>
                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<!-- =========================================================
     PAGE-SPECIFIC STYLE
========================================================= -->
<style>

    .entity-logo {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        background: #eee;
    }

    .entity-logo-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eceeec;
        color: #8e938e;
        font-size: 18px;
    }

    .chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 200px;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        background: #f0f3f0;
        color: #4e534e;
        font-size: 10px;
        font-weight: 650;
        white-space: nowrap;
    }

    .contact-cell strong {
        display: block;
        font-size: 12.5px;
    }

    .contact-cell span {
        display: block;
        font-size: 11.5px;
        color: #999e99;
    }

</style>

<!-- =========================================================
     JAVASCRIPT (to be wired to the API next)
========================================================= -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="../assets/js/entities.js"></script>

<?php include "../includes/footer.php"; ?>
