<?php

$pageTitle = "Event Management";
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

                    <a
                        href="index.php"
                        class="back-link"
                    >
                        <i class="ri-arrow-left-line"></i>
                        Back to Events
                    </a>


                    <h1 id="pageTitle">
                        Create Event
                    </h1>

                </div>


                <div class="page-actions">

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="button"
                        class="btn btn-primary"
                        id="saveBtn"
                        onclick="saveEvent()"
                    >

                        <i class="ri-save-line"></i>

                        Save Event

                    </button>

                </div>

            </header>


            <!-- =====================================================
                 FORM
            ====================================================== -->

            <form id="eventForm">

                <!-- EVENT ID -->

                <input type="hidden" id="eventId">


                <div class="form-layout">


                    <!-- =================================================
                         MAIN CONTENT
                    ================================================== -->

                    <div class="form-main">


                        <!-- =============================================
                             EVENT INFORMATION
                        ============================================== -->

                        <section class="admin-card">

                            <h2>
                                Event Information
                            </h2>


                            <div class="form-grid">


                                <!-- TITLE -->

                                <div class="form-group">

                                    <label for="title">
                                        Title *
                                    </label>

                                    <input
                                        id="title"
                                        type="text"
                                        placeholder="Enter event title"
                                        autocomplete="off"
                                    >

                                </div>


                                <!-- SLUG -->

                                <div class="form-group">

                                    <label for="slug">
                                        Slug
                                    </label>

                                    <input
                                        id="slug"
                                        readonly
                                    >

                                    <small>
                                        Automatically generated from the title.
                                    </small>

                                </div>


                                <!-- CATEGORY -->

                                <div class="form-group">

                                    <label for="category">
                                        Category
                                    </label>

                                    <select id="category">

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option value="Nonton Di">
                                            Nonton Di
                                        </option>

                                        <option value="Nonton Bareng">
                                            Nonton Bareng
                                        </option>

                                        <option value="Jakarta Film Lab">
                                            Jakarta Film Lab
                                        </option>

                                        <option value="Others">
                                            Others
                                        </option>

                                    </select>

                                </div>


                                <!-- CUSTOM CATEGORY -->

                                <div
                                    class="form-group"
                                    id="customCategoryGroup"
                                    style="display:none;"
                                >

                                    <label for="category_name">
                                        Custom Category
                                    </label>

                                    <input
                                        type="text"
                                        id="category_name"
                                        placeholder="Example: Workshop"
                                    >

                                    <small>
                                        Enter the specific category name for this event.
                                    </small>

                                </div>


                                <!-- START DATE -->

                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input id="start_date" type="date">
                                </div>


                                <!-- END DATE -->

                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input id="end_date" type="date">
                                </div>


                                <!-- START TIME -->

                                <div class="form-group">
                                    <label for="start_time">Start Time</label>
                                    <input id="start_time" type="time">
                                </div>


                                <!-- END TIME -->

                                <div class="form-group">
                                    <label for="end_time">End Time</label>
                                    <input id="end_time" type="time">
                                </div>


                                <!-- LOCATION -->

                                <div class="form-group">
                                    <label for="location">Location</label>
                                    <input id="location" placeholder="Enter location">
                                </div>


                                <!-- ADDRESS -->

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input id="address" placeholder="Enter address">
                                </div>


                            </div>

                        </section>


                        <!-- =============================================
                             DESCRIPTION
                        ============================================== -->

                        <section class="admin-card">

                            <h2>
                                Description
                            </h2>

                            <div class="form-group">

                                <textarea
                                    id="description"
                                    rows="5"
                                    placeholder="Write a short event description..."
                                ></textarea>

                            </div>

                        </section>


                        <!-- =============================================
                             ARTICLE CONTENT
                        ============================================== -->

                        <section class="admin-card">

                            <div class="card-header">

                                <div>

                                    <h2>
                                        Article Content
                                    </h2>

                                    <p class="dashboard-section-description">
                                        Build the main content of the event using paragraphs and images.
                                    </p>

                                </div>


                                <div class="article-add-buttons">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        onclick="addParagraph()"
                                    >

                                        <i class="ri-text"></i>

                                        Paragraph

                                    </button>


                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        onclick="addArticleImage()"
                                    >

                                        <i class="ri-image-line"></i>

                                        Image

                                    </button>

                                </div>

                            </div>


                            <div id="paragraphContainer"></div>

                        </section>


                        <!-- =============================================
                             SCHEDULE
                        ============================================== -->

                        <section class="admin-card">

                            <div class="card-header">

                                <h2>
                                    Schedule Timeline
                                </h2>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    onclick="addScheduleRow()"
                                >

                                    <i class="ri-add-line"></i>

                                    Activity

                                </button>

                            </div>


                            <div id="scheduleContainer"></div>

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

                                <select id="status">

                                    <option value="draft">
                                        Draft
                                    </option>

                                    <option value="published">
                                        Published
                                    </option>

                                </select>

                            </div>


                            <div
                                class="form-group"
                                style="margin-top:18px;"
                            >

                                <label for="featured">
                                    Featured Event
                                </label>

                                <label class="switch">

                                    <input
                                        type="checkbox"
                                        id="featured"
                                    >

                                    <span class="slider"></span>

                                </label>

                            </div>


                            <div
                                id="featuredDateFields"
                                style="display:none;"
                            >

                                <div
                                    class="form-group"
                                    style="margin-top:18px;"
                                >
                                    <label for="featured_start">Featured Start</label>
                                    <input type="date" id="featured_start">
                                </div>

                                <div
                                    class="form-group"
                                    style="margin-top:18px;"
                                >
                                    <label for="featured_until">Featured Until</label>
                                    <input type="date" id="featured_until">
                                </div>

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
                                    for="imageFile"
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
                                    id="imageFile"
                                    accept="image/jpeg,image/png,image/webp"
                                    hidden
                                >


                                <input
                                    type="hidden"
                                    id="image"
                                >


                                <img
                                    id="imagePreview"
                                    src=""
                                    alt="Cover Preview"
                                >

                            </div>

                        </section>


                        <!-- =============================================
                             GOOGLE MAPS
                        ============================================== -->

                        <section class="admin-card">

                            <h3>
                                Google Maps
                            </h3>

                            <div class="form-group">

                                <label for="map_url">
                                    Map URL
                                </label>

                                <input
                                    id="map_url"
                                    placeholder="https://maps.google.com/..."
                                >

                            </div>

                        </section>


                        <!-- =============================================
                             SEO
                        ============================================== -->

                        <section class="admin-card">

                            <h3>
                                SEO Settings
                            </h3>


                            <div class="form-group">

                                <label for="meta_title">
                                    Meta Title
                                </label>

                                <input
                                    id="meta_title"
                                    placeholder="SEO title"
                                >

                            </div>


                            <div
                                class="form-group"
                                style="margin-top:20px;"
                            >

                                <label for="meta_description">
                                    Meta Description
                                </label>

                                <textarea
                                    id="meta_description"
                                    rows="4"
                                    placeholder="SEO description"
                                ></textarea>

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

<script src="../assets/js/event-form.js"></script>


<?php include "../includes/footer.php"; ?>