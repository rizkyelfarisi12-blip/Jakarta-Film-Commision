<?php

$pageTitle = "Event Management";
$assetPath = "../";

include "../includes/header.php";

?>

<div class="container">
    <div class="admin-form-page">

        <div class="page-header">

            <div>

                <a href="index.php" class="back-link">
                    ← Back to Events
                </a>

                <h1 id="pageTitle">
                    Create Event
                </h1>

            </div>

            <div class="page-actions">

                <button class="btn btn-secondary" onclick="window.location='index.php'">
                    Cancel
                </button>

                <button class="btn btn-primary" id="saveBtn" onclick="saveEvent()">
                    Save Event
                </button>

            </div>

        </div>

        <div class="form-layout">

            <!-- LEFT -->
            <div class="form-main">

                <input type="hidden" id="eventId">

                <!-- Event Information -->
                <div class="admin-card">

                    <h2>Event Information</h2>

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Title *</label>
                            <input id="title" type="text">
                        </div>

                        <div class="form-group">

                            <label>Slug</label>

                            <input id="slug" readonly>

                        </div>

                        <div class="form-group">

                            <label>Category</label>

                            <select id="category">

                                <option value="">Select Category</option>

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

                        <div class="form-group" id="customCategoryGroup" style="display:none;">

                            <label>Custom Category</label>

                            <input type="text" id="category_name" placeholder="Example: Workshop">

                            <small>
                                Enter the specific category name for this event.
                            </small>

                        </div>

                        <div class="form-group">
                            <label>Start Date</label>
                            <input id="start_date" type="date">
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input id="end_date" type="date">
                        </div>

                        <div class="form-group">
                            <label>Start Time</label>
                            <input id="start_time" type="time">
                        </div>

                        <div class="form-group">
                            <label>End Time</label>
                            <input id="end_time" type="time">
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input id="location">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input id="address">
                        </div>

                    </div>

                </div>

                <!-- Description -->
                <div class="admin-card">
                    <h2>Description</h2>
                    <textarea id="description" rows="5"></textarea>
                </div>

                <!-- Content -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Article Content</h2>

                        <div class="article-add-buttons">

                            <button type="button" class="btn btn-primary" onclick="addParagraph()">
                                + Add Paragraph
                            </button>

                            <button type="button" class="btn btn-secondary" onclick="addArticleImage()">
                                + Add Image
                            </button>

                        </div>

                    </div>
                    <div id="paragraphContainer"></div>
                </div>

                <!-- Schedule -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2>Schedule Timeline</h2>
                        <button class="btn btn-primary" type="button" onclick="addScheduleRow()">
                            + Add Activity
                        </button>
                    </div>
                    <div id="scheduleContainer"></div>
                </div>

            </div>

            <!-- RIGHT -->
            <aside class="form-sidebar">

                <div class="admin-card">

                    <h2>Cover Image</h2>

                    <div class="upload-box">

                        <input type="file" id="imageFile" hidden>

                        <div class="upload-area" onclick="document.getElementById('imageFile').click()">

                            <img src="../assets/icon/image-upload.png" class="upload-icon" alt="Upload Image">

                            <h4>Upload Cover Image</h4>

                            <p>Click to browse</p>

                        </div>


                        <input type="hidden" id="image">

                        <img id="imagePreview">

                        <div class="form-group" style="margin-top:20px">

                            <label>Featured Event</label>

                            <label class="switch">
                                <input type="checkbox" id="featured">

                                <span class="slider"></span>
                            </label>

                        </div>

                        <div class="form-group">

                            <label>Featured Start</label>

                            <input type="date" id="featured_start">

                        </div>

                        <div class="form-group">

                            <label>Featured Until</label>

                            <input type="date" id="featured_until">

                        </div>

                        <div class="form-group">

                            <label>Status</label>

                            <select id="status">

                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <!-- <option value="archived">Archived</option> -->

                            </select>

                        </div>

                    </div>

                </div>

                <div class="admin-card">
                    <h2>Google Maps</h2>
                    <input id="map_url">
                </div>

                <div class="admin-card">

                    <h2>SEO</h2>

                    <div class="form-group">

                        <label>Meta Title</label>

                        <input id="meta_title">

                    </div>

                    <div class="form-group">

                        <label>Meta Description</label>

                        <textarea id="meta_description" rows="4"></textarea>

                    </div>

                </div>

            </aside>

        </div>

    </div>
</div>

<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="../assets/js/event-form.js"></script>


<?php include "../includes/footer.php"; ?>