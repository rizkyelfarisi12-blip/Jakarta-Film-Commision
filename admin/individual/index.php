<?php

require_once __DIR__ . "/../includes/auth.php";
requireRole(["super_admin", "membership_admin"]);

$pageTitle = "Individual Membership";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="admin-layout">

    <?php include "../includes/sidebar.php"; ?>

    <main class="main-content">

        <header class="dashboard-header">

            <div>
                <h1>Individual Membership</h1>
                <p>Manage individual membership registrations</p>
            </div>

            <div class="page-actions">

                <a href="form.php" class="btn btn-primary">
                    <i class="ri-add-line"></i>
                    New Registrant
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
                    <span class="stat-label">Total Registrants</span>
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
                        id="searchIndividual"
                        placeholder="Search name, email, or phone..."
                        class="table-search"
                        autocomplete="off">

                    <select id="statusFilter" class="table-filter">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <select id="interestFilter" class="table-filter">
                        <option value="">All Interest</option>
                    </select>

                    <input type="date" id="dateFromFilter" class="table-filter" title="From Date">

                    <input type="date" id="dateToFilter" class="table-filter" title="To Date">

                    <select id="sortFilter" class="table-filter">
                        <option value="date_desc">Sort: Newest</option>
                        <option value="date_asc">Sort: Oldest</option>
                        <option value="name_asc">Sort: Name (A-Z)</option>
                        <option value="name_desc">Sort: Name (Z-A)</option>
                    </select>

                </div>
            </div>

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Interest</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>

                    <tbody id="individualTable">
                        <tr>
                            <td colspan="8" style="text-align:center;">
                                Loading Individual Membership...
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<!-- =========================================================
     DETAIL MODAL
========================================================= -->
<div id="individualModalOverlay" class="richtext-link-modal-overlay" style="display:none;">

    <div class="richtext-link-modal" role="dialog" aria-modal="true">

        <div class="richtext-link-modal-header">

            <div class="richtext-link-modal-icon">
                <i class="ri-user-3-line"></i>
            </div>

            <div class="richtext-link-modal-heading">
                <h3 id="individualModalName">Registrant Detail</h3>
                <p>Individual Membership registration detail.</p>
            </div>

            <button type="button" class="richtext-link-modal-close" id="individualModalClose">
                <i class="ri-close-line"></i>
            </button>

        </div>

        <div class="richtext-link-modal-body" id="individualModalBody">
            <!-- filled by JS -->
        </div>

        <div class="richtext-link-modal-footer" id="individualModalFooter">
            <!-- filled by JS -->
        </div>

    </div>

</div>

<!-- =========================================================
     PAGE-SPECIFIC STYLE
========================================================= -->
<style>

    /* Modal visibility toggle (see note above) */
    #individualModalOverlay { display: none; }
    #individualModalOverlay.show { display: flex; }

    .member-photo {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        background: #eee;
    }

    .member-photo-placeholder {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eceeec;
        color: #8e938e;
        font-size: 16px;
    }

    .chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 220px;
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

    #individualModalBody .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 10px 0;
        border-bottom: 1px solid #eceeec;
        font-size: 13px;
    }

    #individualModalBody .detail-row:last-child {
        border-bottom: none;
    }

    #individualModalBody .detail-row span:first-child {
        flex-shrink: 0;
        width: 140px;
        color: #777d77;
        font-weight: 650;
    }

    #individualModalBody .detail-row span:last-child,
    #individualModalBody .detail-row a {
        color: #222;
        text-align: right;
        word-break: break-word;
    }

    #individualModalBody img.detail-photo {
        display: block;
        width: 100%;
        max-width: 220px;
        margin: 0 auto 16px;
        border-radius: 14px;
        object-fit: cover;
    }

</style>

<!-- =========================================================
     LIBRARIES FOR EXPORT
========================================================= -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<!-- =========================================================
     JAVASCRIPT
========================================================= -->
<script src="../assets/js/individual.js"></script>

<?php include "../includes/footer.php"; ?>