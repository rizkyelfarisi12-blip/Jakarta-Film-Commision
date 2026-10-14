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

        <div class="admin-form-page">

            <!-- =====================================================
                 PAGE HEADER
            ====================================================== -->
            <header class="page-header">

                <div>

                    <a href="index.php" class="back-link">
                        <i class="ri-arrow-left-line"></i>
                        Back to Entities Membership
                    </a>

                    <h1 id="pageTitle">
                        New Entity
                    </h1>

                </div>

                <div class="page-actions">

                    <a href="index.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="button" class="btn btn-primary" id="saveEntityBtn">
                        <i class="ri-save-line"></i>
                        Save
                    </button>

                </div>

            </header>

            <!-- =====================================================
                 NOTICE
            ====================================================== -->
            <div class="admin-card" style="margin-bottom:24px;background:#fff8f4;border-color:#ffd9c2;">
                <p style="margin:0;color:#8a4a22;font-size:13px;">
                    <i class="ri-information-line"></i>
                    There is currently no self-service login for registrants.
                    If an entity wants to change their data, they need to
                    contact the admin, and the admin updates it here on
                    their behalf.
                </p>
            </div>

            <!-- =====================================================
                 STEP TABS
            ====================================================== -->
            <div class="form-step-tabs">

                <button type="button" class="form-step-tab active" data-step="company">
                    <span class="form-step-index">1</span>
                    Company Data
                </button>

                <button type="button" class="form-step-tab" data-step="pic">
                    <span class="form-step-index">2</span>
                    PIC / Contact Person
                </button>

            </div>

            <!-- =====================================================
                 FORM
            ====================================================== -->
            <form id="entityForm">

                <input type="hidden" id="entityId">

                <!-- =================================================
                     STEP 1 - COMPANY DATA
                ================================================== -->
                <div class="form-step" data-step-panel="company">

                    <div class="form-layout">

                        <div class="form-main">

                            <section class="admin-card">

                                <h2>Company Information</h2>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label for="company_name">Company Name *</label>
                                        <input type="text" id="company_name" placeholder="Enter company name" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label for="entity_type">Entity Type</label>
                                        <select id="entity_type">
                                            <option value="">Select Entity Type</option>
                                            <option value="Rumah Produksi">Rumah Produksi</option>
                                            <option value="Institusi Pendidikan">Institusi Pendidikan</option>
                                            <option value="Venture Capital">Venture Capital</option>
                                            <option value="Perusahaan Pendukung Industri">Perusahaan Pendukung Industri</option>
                                            <option value="Pengelola Properti">Pengelola Properti</option>
                                            <option value="UMKM">UMKM</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="customEntityTypeGroup" style="display:none;">
                                        <label for="entity_type_name">Custom Entity Type</label>
                                        <input type="text" id="entity_type_name" placeholder="Enter entity type">
                                    </div>

                                    <div class="form-group">
                                        <label for="submitted_at">Submission Date</label>
                                        <input type="date" id="submitted_at">
                                        <small>
                                            The original registration date. Defaults to today for a new entity.
                                        </small>
                                    </div>

                                    <div class="form-group" style="grid-column:1 / -1;">
                                        <label for="description">Short Company Description</label>
                                        <textarea id="description" rows="4"
                                            placeholder="Brief description of the company..."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="production_type">Production Type</label>
                                        <input type="text" id="production_type"
                                            placeholder="e.g. Feature Film, TV Series, Commercial, Documentary">
                                        <small>
                                            Separate multiple types with a comma ( , ).
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="nib">Company NIB</label>
                                        <input type="text" id="nib" placeholder="Nomor Induk Berusaha">
                                    </div>

                                    <div class="form-group">
                                        <label for="social_media">Instagram / Social Media</label>
                                        <input type="text" id="social_media" placeholder="@company or profile link">
                                    </div>

                                    <div class="form-group">
                                        <label for="interest">Interest in Jakarta Film Commission</label>
                                        <input type="text" id="interest"
                                            placeholder="e.g. Co-Production, Funding Access, Location Permit">
                                        <small>
                                            Separate multiple interests with a comma ( , ).
                                        </small>
                                    </div>

                                </div>

                            </section>

                        </div>

                        <aside class="form-sidebar">

                            <section class="admin-card">

                                <h3>Status</h3>

                                <div class="form-group">
                                    <label for="status">Membership Status</label>
                                    <select id="status">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                            </section>

                            <section class="admin-card">

                                <h3>Company Logo</h3>

                                <div class="upload-box">

                                    <label for="logoFile" class="upload-area">
                                        <img src="<?= $assetPath ?>assets/icon/image-upload.png" class="upload-icon"
                                            alt="Upload" onerror="this.style.display='none';">
                                        <h4>Upload Logo</h4>
                                        <p>JPG, PNG or WEBP</p>
                                    </label>

                                    <input type="file" id="logoFile" accept="image/jpeg,image/png,image/webp" hidden>
                                    <input type="hidden" id="logo">

                                    <img id="logoPreview" src="" alt="Logo Preview">

                                </div>

                            </section>

                            <section class="admin-card">

                                <h3>Company Profile</h3>

                                <div class="upload-box">

                                    <label for="companyProfileFile" class="upload-area">
                                        <img src="<?= $assetPath ?>assets/icon/image-upload.png" class="upload-icon"
                                            alt="Upload" onerror="this.style.display='none';">
                                        <h4>Upload Company Profile</h4>
                                        <p>PDF, DOC or DOCX</p>
                                    </label>

                                    <input type="file" id="companyProfileFile"
                                        accept=".pdf,.doc,.docx" hidden>
                                    <input type="hidden" id="company_profile">

                                    <div id="companyProfileFileName" class="form-file-name"></div>

                                </div>

                            </section>

                        </aside>

                    </div>

                </div>

                <!-- =================================================
                     STEP 2 - PIC / CONTACT PERSON
                ================================================== -->
                <div class="form-step" data-step-panel="pic" style="display:none;">

                    <div class="form-layout">

                        <div class="form-main">

                            <section class="admin-card">

                                <h2>Person In Charge (PIC)</h2>

                                <p class="dashboard-section-description" style="margin-bottom:20px;">
                                    Contact person representing this company for Jakarta Film Commission
                                    correspondence.
                                </p>

                                <div class="form-grid">

                                    <div class="form-group">
                                        <label for="pic_name">PIC Name</label>
                                        <input type="text" id="pic_name" placeholder="Enter PIC full name" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label for="pic_position">PIC Position</label>
                                        <input type="text" id="pic_position" placeholder="e.g. Producer, Manager">
                                    </div>

                                    <div class="form-group">
                                        <label for="pic_email">Email</label>
                                        <input type="email" id="pic_email" placeholder="name@company.com" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label for="pic_phone">No. HP (WhatsApp)</label>
                                        <input type="text" id="pic_phone" placeholder="e.g. 081234567890" autocomplete="off">
                                    </div>

                                </div>

                            </section>

                        </div>

                        <aside class="form-sidebar">

                            <section class="admin-card">
                                <h3>Note</h3>
                                <p style="margin:0;color:#666b66;font-size:12.5px;line-height:1.6;">
                                    This form currently supports one PIC per company. If an entity
                                    needs multiple contact persons in the future, this section can be
                                    upgraded to a repeatable list.
                                </p>
                            </section>

                        </aside>

                    </div>

                </div>

                <!-- =================================================
                     STEP NAVIGATION
                ================================================== -->
                <div class="form-step-nav">

                    <button type="button" class="btn btn-secondary" id="stepPrevBtn" style="visibility:hidden;">
                        <i class="ri-arrow-left-line"></i>
                        Previous
                    </button>

                    <button type="button" class="btn btn-primary" id="stepNextBtn">
                        Next: PIC / Contact Person
                        <i class="ri-arrow-right-line"></i>
                    </button>

                </div>

            </form>

        </div>

    </main>

</div>

<!-- =========================================================
     PAGE-SPECIFIC STYLE
========================================================= -->
<style>

    .form-step-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 22px;
    }

    .form-step-tab {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 12px 18px;
        border: 1px solid var(--border-dark);
        border-radius: 12px;
        background: #fff;
        color: #777d77;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .form-step-tab:hover {
        border-color: #c9cdc9;
    }

    .form-step-tab.active {
        border-color: var(--primary);
        background: #fff8f4;
        color: var(--primary);
    }

    .form-step-index {
        width: 22px;
        height: 22px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #eceeec;
        color: #666b66;
        font-size: 11px;
        font-weight: 800;
    }

    .form-step-tab.active .form-step-index {
        background: var(--primary);
        color: #fff;
    }

    .form-step-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 8px;
    }

    .form-file-name {
        margin-top: 10px;
        font-size: 12px;
        color: #666b66;
        word-break: break-word;
    }

    #logoPreview {
        display: none;
        width: 100%;
        max-height: 180px;
        margin-top: 15px;
        border-radius: 13px;
        object-fit: contain;
        background: #fafbfa;
        border: 1px solid var(--border);
    }

</style>

<!-- =========================================================
     JAVASCRIPT (to be wired to the API next)
========================================================= -->
<script src="../assets/js/entities-form.js"></script>

<?php include "../includes/footer.php"; ?>
