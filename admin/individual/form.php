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
                                    <label for="phone">No. HP (WhatsApp)</label>
                                    <input type="text" id="phone" placeholder="e.g. 081234567890" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" placeholder="name@example.com" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label for="submitted_at">Submission Date</label>
                                    <input type="date" id="submitted_at">
                                    <small>
                                        The original registration date. Defaults to today for a new registrant.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="role_in_industry">Role in Film Ecosystem</label>
                                    <input type="text" id="role_in_industry"
                                        placeholder="e.g. Director, Producer, Editor">
                                    <small>
                                        Separate multiple roles with a comma ( , ).
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="interest">Interest in Jakarta Film Commission</label>
                                    <input type="text" id="interest"
                                        placeholder="e.g. Workshop, Networking, Funding Access">
                                    <small>
                                        Separate multiple interests with a comma ( , ).
                                    </small>
                                </div>

                                <div class="form-group" style="grid-column:1 / -1;">
                                    <label for="portfolio_link">Portfolio Link</label>
                                    <input type="text" id="portfolio_link"
                                        placeholder="IMDb / Vimeo / YouTube / Instagram / Website link">
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

                        </section>

                        <section class="admin-card">

                            <h3>Profile Photo</h3>

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

<script src="../assets/js/individual-form.js"></script>

<?php include "../includes/footer.php"; ?>
