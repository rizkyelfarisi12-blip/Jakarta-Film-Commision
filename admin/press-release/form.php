<?php

$pageTitle = "Press Release";

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
                        Back to Press Releases
                    </a>

                    <h1 id="pageTitle">
                        New Press Release
                    </h1>

                </div>


                <div class="page-actions">

                    <a href="index.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="button" class="btn btn-primary" id="savePressReleaseBtn">
                        <i class="ri-save-line"></i>
                        Save Press Release
                    </button>

                </div>

            </header>


            <!-- =====================================================
                 FORM
            ====================================================== -->
            <form id="pressReleaseForm" enctype="multipart/form-data">

                <!-- PRESS RELEASE ID -->
                <input type="hidden" id="pressReleaseId" name="id" value="">

                <!-- EXISTING COVER IMAGE -->
                <input type="hidden" id="existingCoverImage" name="existing_cover_image" value="">

                <div class="form-layout">

                    <!-- =================================================
                         MAIN CONTENT
                    ================================================== -->
                    <div class="form-main">

                        <!-- =============================================
                             BASIC INFORMATION
                        ============================================== -->
                        <section class="admin-card">

                            <h2>
                                Press Release Information
                            </h2>

                            <div class="form-grid">

                                <!-- =====================================
                                     TITLE
                                ====================================== -->
                                <div class="form-group">

                                    <label for="title">
                                        Title
                                    </label>

                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        placeholder="Enter press release title"
                                        autocomplete="off"
                                        required>

                                </div>

                                <!-- =====================================
                                     SLUG
                                ====================================== -->
                                <div class="form-group">

                                    <label for="slug">
                                        Slug
                                    </label>

                                    <input
                                        type="text"
                                        id="slug"
                                        name="slug"
                                        placeholder="press-release-slug"
                                        readonly>

                                    <small>
                                        Automatically generated from the title.
                                    </small>

                                </div>

                                <!-- =====================================
                                     CATEGORY
                                ====================================== -->
                                <div class="form-group">

                                    <label for="category">
                                        Category
                                    </label>

                                    <select
                                        id="category"
                                        name="category"
                                        required>

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option value="Official Release">
                                            Official Release
                                        </option>

                                        <option value="Program Update">
                                            Program Update
                                        </option>

                                        <option value="Industry News">
                                            Industry News
                                        </option>

                                        <option value="Others">
                                            Others
                                        </option>

                                    </select>

                                </div>


                                <!-- =====================================
                                     CUSTOM CATEGORY
                                ====================================== -->

                                <div
                                    class="form-group"
                                    id="categoryNameGroup"
                                    style="display:none;">

                                    <label for="category_name">
                                        Category Name
                                    </label>

                                    <input
                                        type="text"
                                        id="category_name"
                                        name="category_name"
                                        placeholder="Enter custom category name">

                                    <small>
                                        Enter the category label that will be displayed to users.
                                    </small>

                                </div>


                                <!-- =====================================
                                     DATE
                                ====================================== -->
                                <div class="form-group">

                                    <label for="date">
                                        Date
                                    </label>

                                    <input type="date" id="date" name="date" required>

                                    <small>
                                        The publication date of this press release.
                                    </small>

                                </div>


                                <!-- =====================================
                                     LOCATION
                                ====================================== -->

                                <div class="form-group">

                                    <label for="location">
                                        Location
                                    </label>

                                    <input
                                        type="text"
                                        id="location"
                                        name="location"
                                        placeholder="Enter location"
                                        maxlength="255">

                                    <small>
                                        Example: Jakarta, Indonesia
                                    </small>

                                </div>

                                <!-- =====================================
                                     DESCRIPTION
                                ====================================== -->
                                <div class="form-group">

                                    <label for="description">
                                        Short Description
                                    </label>

                                    <textarea
                                        id="description"
                                        name="description"
                                        placeholder="Write a short description that will appear on the press release card..."
                                    ></textarea>

                                    <small>
                                        This description will be used as the main highlight on the press release list.
                                    </small>

                                </div>


                            </div>

                        </section>


                        <!-- =================================================
                             ARTICLE CONTENT
                        ================================================== -->

                        <section class="admin-card">

                            <div class="card-header">

                                <div>

                                    <h2>
                                        Article Content
                                    </h2>

                                    <p class="dashboard-section-description">
                                        Build the main content of the press release using paragraphs and images.
                                    </p>

                                </div>


                                <div class="article-add-buttons">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        id="addParagraphBtn"
                                    >

                                        <i class="ri-text"></i>

                                        Paragraph

                                    </button>


                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        id="addImageBtn"
                                    >

                                        <i class="ri-image-line"></i>

                                        Image

                                    </button>

                                </div>

                            </div>


                            <!-- ARTICLE BLOCKS -->

                            <div id="articleContent">

                                <!-- Dynamic article blocks -->

                            </div>


                            <!-- EMPTY STATE -->

                            <div
                                id="articleEmptyState"
                                class="dashboard-empty"
                            >

                                No article content yet.

                                <br>

                                Add a paragraph or image to start writing.

                            </div>

                        </section>


                        <!-- =================================================
                             SEO
                        ================================================== -->

                        <section class="admin-card">

                            <h2>
                                SEO Settings
                            </h2>


                            <!-- META TITLE -->

                            <div class="form-group">

                                <label for="meta_title">
                                    Meta Title
                                </label>

                                <input
                                    type="text"
                                    id="meta_title"
                                    name="meta_title"
                                    placeholder="SEO title"
                                    maxlength="255"
                                >

                                <small>
                                    Optional. If empty, the press release title can be used.
                                </small>

                            </div>


                            <!-- META DESCRIPTION -->

                            <div
                                class="form-group"
                                style="margin-top:20px;"
                            >

                                <label for="meta_description">
                                    Meta Description
                                </label>

                                <textarea
                                    id="meta_description"
                                    name="meta_description"
                                    placeholder="SEO description"
                                ></textarea>

                            </div>

                        </section>


                    </div>


                    <!-- =================================================
                         SIDEBAR
                    ================================================== -->

                    <aside class="form-sidebar">


                        <!-- =============================================
                             PUBLISH
                        ============================================== -->

                        <section class="admin-card">

                            <h3>
                                Publish
                            </h3>


                            <div class="form-group">

                                <label for="status">
                                    Status
                                </label>

                                <select
                                    id="status"
                                    name="status"
                                >

                                    <option value="draft">
                                        Draft
                                    </option>

                                    <option value="published">
                                        Published
                                    </option>

                                </select>

                            </div>

                        </section>


                        <!-- =============================================
                             COVER IMAGE
                        ============================================== -->

                        <section class="admin-card">

                            <h3>
                                Cover Image
                            </h3>


                            <div class="upload-box">

                                <label
                                    for="coverImage"
                                    class="upload-area"
                                >

                                    <img
                                        src="<?= $assetPath ?>assets/icon/image-upload.png"
                                        class="upload-icon"
                                        alt="Upload"
                                        onerror="this.style.display='none';"
                                    >


                                    <h4>
                                        Upload Cover Image
                                    </h4>


                                    <p>
                                        JPG, PNG or WEBP
                                    </p>


                                    <p>
                                        Recommended 16:9
                                    </p>

                                </label>


                                <input
                                    type="file"
                                    id="coverImage"
                                    name="cover_image"
                                    accept="image/jpeg,image/png,image/webp"
                                    hidden
                                >


                                <img
                                    id="imagePreview"
                                    src=""
                                    alt="Cover Preview"
                                >

                            </div>

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

<script src="../assets/js/press-release-form.js"></script>


<?php include "../includes/footer.php"; ?>