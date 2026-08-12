<?php

include "../includes/header.php";
?>
<html>
<head>
<meta charset="utf-8">
<title>Admin Events</title>

 <link rel="stylesheet" href="../assets/css/admin.css">

</head>
<body>

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

                            <input
                            id="slug"
                            readonly>

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

                            <div
                                class="form-group"
                                id="customCategoryGroup"
                                style="display:none;"
                            >

                                <label>Custom Category</label>

                                <input
                                    type="text"
                                    id="category_name"
                                    placeholder="Example: Workshop"
                                >

                                <small>
                                    Enter the specific category name for this event.
                                </small>

                            </div>

                            <div class="form-group">
                                <label>Start Date</label>
                                <input
                                    id="start_date"
                                    type="date">
                            </div>

                            <div class="form-group">
                                <label>End Date</label>
                                <input
                                    id="end_date"
                                    type="date">
                            </div>

                            <div class="form-group">
                                <label>Start Time</label>
                                <input
                                    id="start_time"
                                    type="time">
                            </div>

                            <div class="form-group">
                                <label>End Time</label>
                                <input
                                    id="end_time"
                                    type="time">
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

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    onclick="addParagraph()">
                                    + Add Paragraph
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    onclick="addArticleImage()">
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
                            <button
                                class="btn btn-primary" type="button" onclick="addScheduleRow()">
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

                            <input
                                type="file"
                                id="imageFile"
                                hidden>

                            <div
                                class="upload-area"
                                onclick="imageFile.click()">

                                <div class="upload-icon">📷</div>

                                <h4>Upload Cover Image</h4>

                                <p>Click to browse</p>

                            </div>

                            <div class="form-group" style="margin-top:20px">

                                <label>Featured Event</label>

                                <label class="switch">
                                    <input
                                        type="checkbox"
                                        id="featured">

                                    <span class="slider"></span>
                                </label>

                            </div>

                            <div class="form-group">

                                <label>Featured Start</label>

                                <input
                                    type="date"
                                    id="featured_start">

                            </div>

                            <div class="form-group">

                                <label>Featured Until</label>

                                <input
                                    type="date"
                                    id="featured_until">

                            </div>

                            <div class="form-group">

                                <label>Status</label>

                                <select id="status">

                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <!-- <option value="archived">Archived</option> -->

                                </select>

                            </div>
                            <input
                                type="hidden"
                                id="image">

                            <img
                                id="imagePreview">

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

                            <textarea
                                id="meta_description"
                                rows="4"
                            ></textarea>

                        </div>

                    </div>

                </aside>

            </div>

        </div>
    </div>

<script>
function formatDateTimeLocal(value){

    if(!value) return "";

    return value
        .replace(" ", "T")
        .slice(0,16);

}

document
.getElementById("featured")
.addEventListener("change", function(){

    const options =
        document.getElementById("featuredOptions");

    if(this.checked){

        options.style.display = "block";

    }else{

        options.style.display = "none";

    }

});

function fillForm(event){

    if(!event){
        console.error("Event tidak ditemukan");
        return;
    }

    document.getElementById("eventId").value =
        event.id ?? "";

    document.getElementById("title").value =
        event.title ?? "";

    document.getElementById("slug").value =
        event.slug ?? "";

    /* =========================
    CATEGORY
    ========================= */

    const categorySelect =
        document.getElementById("category");

    const categoryNameInput =
        document.getElementById("category_name");

    const customCategoryGroup =
        document.getElementById("customCategoryGroup");

    categorySelect.value =
        event.category ?? "";

    categoryNameInput.value =
        event.category_name ?? "";

    if(event.category === "Others"){

        customCategoryGroup.style.display = "block";

    }else{

        customCategoryGroup.style.display = "none";

        categoryNameInput.value = "";

    }

    document.getElementById("start_date").value =
        event.start_date ?? "";

    document.getElementById("end_date").value =
        event.end_date ?? "";

    document.getElementById("start_time").value =
        event.start_time ?? "";

    document.getElementById("end_time").value =
        event.end_time ?? "";

    document.getElementById("location").value =
        event.location ?? "";

    document.getElementById("address").value =
        event.address ?? "";

    document.getElementById("description").value =
        event.description ?? "";

    document.getElementById("map_url").value =
        event.map_url ?? "";

    document.getElementById("meta_title").value =
        event.meta_title ?? "";

    document.getElementById("meta_description").value =
        event.meta_description ?? "";

    document.getElementById("featured").checked =
        Number(event.featured) === 1;

    document.getElementById("featured_start").value =
        event.featured_start ?? "";

    document.getElementById("featured_until").value =
        event.featured_until ?? "";

    document.getElementById("status").value =
        event.status ?? "draft";


    /* =========================
       COVER IMAGE
    ========================= */

    document.getElementById("image").value =
        event.cover_image ?? "";

    if(event.cover_image){

        document.getElementById("imagePreview").src =
            ROOT_PATH + "/" + event.cover_image;

        document.getElementById("imagePreview").style.display =
            "block";

    }else{

        document.getElementById("imagePreview").src = "";

        document.getElementById("imagePreview").style.display =
            "none";
    }


    /* =========================
       ARTICLE CONTENT
    ========================= */

    const paragraphContainer =
        document.getElementById("paragraphContainer");

    paragraphContainer.innerHTML = "";

    try{

        const paragraphs =
            event.content
            ? JSON.parse(event.content)
            : [];

        if(Array.isArray(paragraphs) && paragraphs.length){

            paragraphs.forEach(text => {
                addParagraph(text);
            });

        }else{

            addParagraph();

        }

    }catch(error){

        console.error(
            "Content JSON error:",
            error
        );

        addParagraph();
    }


    /* =========================
       SCHEDULE
    ========================= */

    const scheduleContainer =
        document.getElementById("scheduleContainer");

    scheduleContainer.innerHTML = "";

    try{

        const schedules =
            event.schedule
            ? JSON.parse(event.schedule)
            : [];

        if(Array.isArray(schedules) && schedules.length){

            schedules.forEach(item => {

                addScheduleRow(
                    item.time ?? "",
                    item.title ?? "",
                    item.description ?? ""
                );

            });

        }else{

            addScheduleRow();

        }

    }catch(error){

        console.error(
            "Schedule JSON error:",
            error
        );

        addScheduleRow();
    }


    /* =========================
       UI
    ========================= */

    document.getElementById("pageTitle").innerText =
        "Edit Event";

    document.getElementById("saveBtn").innerText =
        "Update Event";
}

async function saveEvent(){

    try{

        const id =
            document.getElementById("eventId").value.trim();


        /* =========================
           SCHEDULE
        ========================= */

        const schedules = [];

        document
            .querySelectorAll(".schedule-item")
            .forEach(row => {

                const time =
                    row.querySelector(".schedule-time")?.value ?? "";

                const title =
                    row.querySelector(".schedule-title")?.value ?? "";

                const description =
                    row.querySelector(".schedule-description")?.value ?? "";

                if(time || title || description){

                    schedules.push({
                        time: time,
                        title: title,
                        description: description
                    });

                }

            });


        /* =========================
           CONTENT
        ========================= */

        const articleContent = [];


        document
            .querySelectorAll("#paragraphContainer .article-block")
            .forEach(block => {


                const type =
                    block.dataset.type;


                /*
                |--------------------------------------------------------------------------
                | PARAGRAPH
                |--------------------------------------------------------------------------
                */

                if(type === "paragraph"){

                    const text =
                        block
                        .querySelector(".paragraph-text")
                        ?.value
                        .trim();


                    if(text){

                        articleContent.push({

                            type: "paragraph",

                            text: text

                        });

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                if(type === "image"){

                    const src =
                        block
                        .querySelector(".article-image-src")
                        ?.value
                        .trim();


                    const caption =
                        block
                        .querySelector(".article-image-caption")
                        ?.value
                        .trim();


                    const alt =
                        block
                        .querySelector(".article-image-alt")
                        ?.value
                        .trim();


                    if(src){

                        articleContent.push({

                            type: "image",

                            src: src,

                            caption: caption,

                            alt: alt

                        });

                    }

                }

            });


        /* =========================
           PAYLOAD
        ========================= */

        const payload = {

            id: id,

            title:
                document.getElementById("title").value.trim(),

            slug:
                document.getElementById("slug").value.trim(),

            category:
                document.getElementById("category").value,

            category_name:
                document.getElementById("category").value === "Others"
                    ? document.getElementById("category_name").value.trim()
                    : null,

            start_date:
                document.getElementById("start_date").value || null,

            end_date:
                document.getElementById("end_date").value || null,

            start_time:
                document.getElementById("start_time").value || null,

            end_time:
                document.getElementById("end_time").value || null,

            location:
                document.getElementById("location").value.trim(),

            address:
                document.getElementById("address").value.trim(),

            cover_image:
                document.getElementById("image").value.trim(),

            description:
                document.getElementById("description").value.trim(),

            content:
                JSON.stringify(articleContent),

            schedule:
                JSON.stringify(schedules),

            map_url:
                document.getElementById("map_url").value.trim(),

            featured:
                document.getElementById("featured").checked
                    ? 1
                    : 0,

            featured_start:
                document.getElementById("featured_start").value || null,

            featured_until:
                document.getElementById("featured_until").value || null,

            meta_title:
                document.getElementById("meta_title").value.trim(),

            meta_description:
                document.getElementById("meta_description").value.trim(),

            status:
                document.getElementById("status").value

        };


        console.log("PAYLOAD:", payload);


        /* =========================
           VALIDATION
        ========================= */

        if(!payload.title){

            alert("Title wajib diisi.");
            return;

        }

        if(!payload.category){

            alert("Category wajib dipilih.");
            return;

        }

        if(
            payload.category === "Others" &&
            !payload.category_name
        ){

            alert("Silakan masukkan nama category untuk Others.");
            document.getElementById("category_name").focus();

            return;

        }


        /* =========================
           API
        ========================= */

        const url =
            id
                ? API_URL + "/events/update-event.php"
                : API_URL + "/events/create-event.php";


        console.log("REQUEST URL:", url);


        const response =
            await fetch(url, {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(payload)

            });


        /* =========================
           DEBUG RAW RESPONSE
        ========================= */

        const raw =
            await response.text();

        console.log(
            "RAW API RESPONSE:",
            raw
        );


        let result;

        try{

            result =
                JSON.parse(raw);

        }catch(error){

            console.error(
                "API mengembalikan response bukan JSON:",
                raw
            );

            alert(
                "Server API mengalami error.\n\n" +
                "Buka Console untuk melihat detail."
            );

            return;
        }


        /* =========================
           RESULT
        ========================= */

        if(result.success){

            alert(
                id
                    ? "Event berhasil diupdate."
                    : "Event berhasil dibuat."
            );

            window.location.href =
                "index.php";

            return;

        }


        alert(
            result.message ||
            result.error ||
            "Gagal menyimpan event."
        );


    }catch(error){

        console.error(
            "SAVE EVENT ERROR:",
            error
        );

        alert(
            "Terjadi error saat menyimpan event.\n\n" +
            error.message
        );

    }

}

function resetForm(){

    document.getElementById("eventId").value = "";

    document.getElementById("title").value = "";

    document.getElementById("slug").value = "";

    document.getElementById("category").value = "";

    document.getElementById("category_name").value = "";

    document.getElementById("customCategoryGroup").style.display =
        "none";

    document.getElementById("start_date").value = "";

    document.getElementById("end_date").value = "";

    document.getElementById("start_time").value = "";

    document.getElementById("end_time").value = "";

    document.getElementById("featured").checked = false;

    document.getElementById("featured_start").value = "";

    document.getElementById("featured_until").value = "";

    document.getElementById("meta_title").value = "";

    document.getElementById("meta_description").value = "";

    document.getElementById("location").value = "";

    document.getElementById("address").value = "";

    document.getElementById("image").value = "";

    document.getElementById("description").value = "";

    document.getElementById("paragraphContainer").innerHTML = "";

    addParagraph();

    document.getElementById("map_url").value = "";

    document.getElementById("scheduleContainer").innerHTML = "";

    addScheduleRow();

    document.getElementById("saveBtn").innerText =
        "Save Event";

    document.getElementById("status").value =
        "draft";

    document.getElementById("imagePreview").src = "";

    document.getElementById("imageFile").value = "";

}

async function deleteEvent(id){

    if(!confirm("Delete event?"))
    return;

    await fetch(
    "../api/delete-event.php",
    {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({id})
    });
}

function addScheduleRow(

    time = "",
    title = "",
    description = ""

){
    const container =
        document.getElementById("scheduleContainer");

    const item =
        document.createElement("div");

    item.className =
        "schedule-item";

    item.innerHTML = `

    <div class="schedule-top">

        <input
            type="time"
            class="schedule-time"
            value="${time}">

        <input
            class="schedule-title"
            placeholder="Activity Title"
            value="${title}">

    </div>

    <textarea
        class="schedule-description"
        placeholder="Activity Description">${description}</textarea>

    <div class="schedule-footer">

        <button
            type="button"
            class="delete-schedule"
            onclick="this.closest('.schedule-item').remove()">

            Delete

        </button>

    </div>

    `;

    container.appendChild(item);
}

function addParagraph(text = ""){

    const container =
        document.getElementById("paragraphContainer");


    const item =
        document.createElement("div");


    item.className =
        "article-block paragraph-item";


    item.dataset.type =
        "paragraph";


    item.innerHTML = `

        <div class="article-block-header">

            <strong>
                Paragraph
            </strong>

            <button
                type="button"
                class="delete-schedule"
                onclick="
                    this.closest('.article-block').remove()
                ">

                Delete

            </button>

        </div>


        <textarea
            class="paragraph-text"
            rows="6"
            placeholder="Write paragraph...">${escapeHtml(text)}</textarea>

    `;


    container.appendChild(item);

}

function addArticleImage(data = {}){

    const container =
        document.getElementById("paragraphContainer");


    const item =
        document.createElement("div");


    item.className =
        "article-block image-item";


    item.dataset.type =
        "image";


    item.innerHTML = `

        <div class="article-block-header">

            <strong>
                Image
            </strong>

            <button
                type="button"
                class="delete-schedule"
                onclick="
                    this.closest('.article-block').remove()
                ">

                Delete

            </button>

        </div>


        <div class="article-image-upload">

            <input
                type="file"
                class="article-image-file"
                accept="image/*"
                hidden>


            <div
                class="article-image-upload-area"
                onclick="
                    this
                    .closest('.image-item')
                    .querySelector('.article-image-file')
                    .click()
                ">

                <div class="upload-icon">
                    📷
                </div>

                <strong>
                    Upload Article Image
                </strong>

                <span>
                    Click to browse
                </span>

            </div>


            <img
                class="article-image-preview"
                style="
                    display:none;
                    width:100%;
                    margin-top:15px;
                    border-radius:12px;
                ">

        </div>


        <div class="form-group">

            <label>
                Caption
            </label>

            <input
                type="text"
                class="article-image-caption"
                placeholder="Image caption..."
                value="${escapeHtml(data.caption || "")}">

        </div>


        <div class="form-group">

            <label>
                Alt Text
            </label>

            <input
                type="text"
                class="article-image-alt"
                placeholder="Describe this image..."
                value="${escapeHtml(data.alt || "")}">

        </div>


        <input
            type="hidden"
            class="article-image-src"
            value="${escapeHtml(data.src || "")}">

    `;


    container.appendChild(item);


    /*
    |--------------------------------------------------------------------------
    | EXISTING IMAGE
    |--------------------------------------------------------------------------
    */

    if(data.src){

        const preview =
            item.querySelector(
                ".article-image-preview"
            );


        preview.src =
            ROOT_PATH + "/" + data.src;


        preview.style.display =
            "block";

    }


    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD
    |--------------------------------------------------------------------------
    */

    const fileInput =
        item.querySelector(
            ".article-image-file"
        );


    fileInput.addEventListener(
        "change",
        function(){

            uploadArticleImage(
                this,
                item
            );

        }
    );

}

async function uploadArticleImage(
    input,
    item
){

    const file =
        input.files[0];


    if(!file){

        return;

    }


    const formData =
        new FormData();


    formData.append(
        "image",
        file
    );


    try{

        const response =
            await fetch(
                API_URL +
                "/events/upload-image.php",
                {
                    method: "POST",
                    body: formData
                }
            );


        const result =
            await response.json();


        console.log(
            "ARTICLE IMAGE:",
            result
        );


        if(!result.success){

            alert(
                result.message ||
                "Upload gambar gagal."
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE PATH
        |--------------------------------------------------------------------------
        */

        item.querySelector(
            ".article-image-src"
        ).value =
            result.path;


        /*
        |--------------------------------------------------------------------------
        | PREVIEW
        |--------------------------------------------------------------------------
        */

        const preview =
            item.querySelector(
                ".article-image-preview"
            );


        preview.src =
            ROOT_PATH +
            "/" +
            result.path;


        preview.style.display =
            "block";


    }catch(error){

        console.error(
            "ARTICLE IMAGE UPLOAD ERROR:",
            error
        );


        alert(
            "Terjadi error saat upload gambar."
        );

    }

}

function searchEvent(){

    const keyword =
    document
    .getElementById("searchEvent")
    .value
    .toLowerCase();

    const rows =
    document.querySelectorAll(
    "#eventTable tr"
    );

    rows.forEach(row=>{

        row.style.display =
        row.innerText
        .toLowerCase()
        .includes(keyword)
        ? ""
        : "none";

    });
}

function escapeHtml(value = ""){

    return String(value)

        .replace(/&/g, "&amp;")

        .replace(/</g, "&lt;")

        .replace(/>/g, "&gt;")

        .replace(/"/g, "&quot;")

        .replace(/'/g, "&#039;");

}

async function initForm(){

    const params = new URLSearchParams(window.location.search);

    const id = params.get("id");

    if(!id){

        addParagraph();
        addScheduleRow();
        return;

    }

    document.getElementById("pageTitle").innerText = "Edit Event";
    document.getElementById("saveBtn").innerText = "Update Event";

    const response = await fetch(
        API_URL + "/events/get-event-detail.php?id=" + id
    );

    const event = await response.json();

    console.log("EVENT DATA");
    console.log(event);

    fillForm(event);

}

initForm();

document
.getElementById("imageFile")
.addEventListener("change", async function(){

    const file = this.files[0];

    if(!file) return;

    const formData = new FormData();

    formData.append("image", file);

    try{

        const response =
        await fetch(API_URL + "/events/upload-image.php",{
            method:"POST",
            body:formData
        });

        const result =
        await response.json();

        console.log(result);

        if(result.success){

            document.getElementById("image").value =
            result.path;

            document.getElementById("imagePreview").src =
            ROOT_PATH + "/" + result.path;

        }else{

            alert("Upload gagal");
        }

    }catch(err){

        console.error(err);
        alert("Upload error");

    }
});

document
.getElementById("title")
.addEventListener("keyup", function(){

    let slug =
    this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g,"-")
        .replace(/^-|-$/g,"");

    document
    .getElementById("slug")
    .value = slug;

});

document
.getElementById("featured")
.addEventListener("change", function(){

    const fields =
        document.getElementById("featuredDateFields");

    if(this.checked){

        fields.style.display = "block";

    }else{

        fields.style.display = "none";

        document.getElementById("featured_start").value = "";
        document.getElementById("featured_until").value = "";

    }

});

/* =========================================================
   CATEGORY CUSTOM
========================================================= */

document
.getElementById("category")
.addEventListener("change", function(){

    const customGroup =
        document.getElementById("customCategoryGroup");

    const customInput =
        document.getElementById("category_name");

    const isOthers =
        this.value === "Others";


    if(isOthers){

        customGroup.style.display = "block";

        customInput.focus();

    }else{

        customGroup.style.display = "none";

        customInput.value = "";

    }

});

</script>

</body>
</html>