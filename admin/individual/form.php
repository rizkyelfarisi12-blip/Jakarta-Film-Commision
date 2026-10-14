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

        <div class="admin-form-page">

            <!-- =====================================================
                 PAGE HEADER
            ====================================================== -->
            <header class="page-header">

                <div>

                    <a href="index.php" class="back-link">
                        <i class="ri-arrow-left-line"></i>
                        Back to Individual Membership
                    </a>

                    <h1 id="pageTitle">
                        New Registrant
                    </h1>

                </div>

                <div class="page-actions">

                    <a href="index.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="button" class="btn btn-primary" id="saveIndividualBtn">
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
                    If a member wants to change their data, they need to
                    contact the admin, and the admin updates it here on
                    their behalf.
                </p>
            </div>

            <!-- =====================================================
                 FORM
            ====================================================== -->
            <form id="individualForm">

                <input type="hidden" id="individualId">

                <div class="form-layout">

                    <!-- =================================================
                         MAIN
                    ================================================== -->
                    <div class="form-main">

                        <section class="admin-card">

                            <h2>Registrant Information</h2>

                            <div class="form-grid">

                                <div class="form-group">
                                    <label for="full_name">Full Name *</label>
                                    <input type="text" id="full_name" placeholder="Enter full name" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="whatsapp">No. HP (WhatsApp) *</label>
                                    <input type="text" id="whatsapp" placeholder="e.g. 081234567890" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" placeholder="name@example.com" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="role">Role in Film Ecosystem *</label>
                                    <select id="role">
                                        <option value="">Select Role</option>
                                        <option value="Praktisi Industri Aktif">Praktisi Industri Aktif</option>
                                        <option value="Filmmaker Independen">Filmmaker Independen</option>
                                        <option value="Pelajar">Pelajar</option>
                                        <option value="Penggiat Film">Penggiat Film</option>
                                        <option value="Komunitas">Komunitas</option>
                                        <option value="Pelaku Usaha">Pelaku Usaha</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <div class="form-group" id="customRoleGroup" style="display:none;">
                                    <label for="role_other">Custom Role</label>
                                    <input type="text" id="role_other" placeholder="Enter your role">
                                </div>

                                <div class="form-group" style="grid-column:1 / -1;">
                                    <label for="portfolio_link">Portfolio Link *</label>
                                    <input type="text" id="portfolio_link"
                                        placeholder="IMDb / Vimeo / YouTube / Instagram / Website link">
                                </div>

                                <div class="form-group" style="grid-column:1 / -1;">

                                    <label>Interest in Jakarta Film Commission</label>

                                    <div class="checkbox-grid" id="interestCheckboxGroup">

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="One-Stop Film Service">
                                            <span>One-Stop Film Service</span>
                                        </label>

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="Promosi & Diplomasi Budaya">
                                            <span>Promosi &amp; Diplomasi Budaya</span>
                                        </label>

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="Pelatihan & Pengembangan SDM Film">
                                            <span>Pelatihan &amp; Pengembangan SDM Film</span>
                                        </label>

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="Akses Pendanaan / Investasi">
                                            <span>Akses Pendanaan / Investasi</span>
                                        </label>

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="Jaringan / Networking Industri">
                                            <span>Jaringan / Networking Industri</span>
                                        </label>

                                        <label class="checkbox-item">
                                            <input type="checkbox" value="Lokasi Syuting & Perizinan">
                                            <span>Lokasi Syuting &amp; Perizinan</span>
                                        </label>

                                    </div>

                                    <small>
                                        Sesuaikan daftar pilihan ini dengan opsi asli di form pendaftaran kamu kalau
                                        berbeda.
                                    </small>

                                </div>

                            </div>

                        </section>

                    </div>

                    <!-- =================================================
                         SIDEBAR
                    ================================================== -->
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

                            <div class="form-group" id="registeredAtGroup" style="display:none;margin-top:16px;">
                                <label>Registered</label>
                                <div id="registeredAtValue" style="font-size:13px;color:#4e534e;font-weight:650;"></div>
                            </div>

                        </section>

                        <section class="admin-card">

                            <h3>Profile Photo *</h3>

                            <div class="upload-box">

                                <label for="photoFile" class="upload-area">

                                    <img src="<?= $assetPath ?>assets/icon/image-upload.png" class="upload-icon"
                                        alt="Upload" onerror="this.style.display='none';">
                                    <h4>Upload Profile Photo</h4>
                                    <p>JPG, PNG or WEBP</p>

                                </label>

                                <input type="file" id="photoFile" accept="image/jpeg,image/png,image/webp" hidden>

                                <input type="hidden" id="photo">

                                <img id="imagePreview" src="" alt="Photo Preview">

                            </div>

                        </section>

                    </aside>

                </div>

            </form>

        </div>

    </main>

</div>

<style>

    #interestCheckboxGroup {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 4px;
    }

    #interestCheckboxGroup .checkbox-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid var(--border-dark);
        border-radius: 10px;
        font-size: 13px;
        color: #3d423d;
        cursor: pointer;
        transition: 0.2s;
    }

    #interestCheckboxGroup .checkbox-item:hover {
        border-color: #c9cdc9;
        background: #fafbfa;
    }

    #interestCheckboxGroup .checkbox-item input[type="checkbox"] {
        -webkit-appearance: checkbox !important;
        appearance: checkbox !important;
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        max-width: 16px !important;
        flex: 0 0 16px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid #b9bdb9 !important;
        border-radius: 4px !important;
        background: #fff !important;
        box-shadow: none !important;
        outline: none !important;
        accent-color: var(--primary);
        cursor: pointer;
    }

    #interestCheckboxGroup .checkbox-item input[type="checkbox"]:focus {
        box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.15) !important;
    }

    #interestCheckboxGroup .checkbox-item span {
        flex: 1;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        #interestCheckboxGroup {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="../assets/js/individual-form.js"></script>

<?php include "../includes/footer.php"; ?>