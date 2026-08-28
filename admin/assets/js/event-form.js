function formatDateTimeLocal(value) {

        if (!value) return "";

        return value
            .replace(" ", "T")
            .slice(0, 16);

    }

    document
        .getElementById("featured")
        .addEventListener("change", function () {

            const options =
                document.getElementById("featuredOptions");

            if (this.checked) {

                options.style.display = "block";

            } else {

                options.style.display = "none";

            }

        });

    function fillForm(event) {

        if (!event) {
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

        if (event.category === "Others") {

            customCategoryGroup.style.display = "block";

        } else {

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

        if (event.cover_image) {

            document.getElementById("imagePreview").src =
                ROOT_PATH + "/" + event.cover_image;

            document.getElementById("imagePreview").style.display =
                "block";

        } else {

            const imagePreview =
                document.getElementById("imagePreview");

            imagePreview.src = "";

            imagePreview.style.display =
                "none";

            document.getElementById("imagePreview").style.display =
                "none";
        }

        /* =========================
        ARTICLE CONTENT
        ========================= */

        const paragraphContainer =
            document.getElementById("paragraphContainer");

        paragraphContainer.innerHTML = "";

        try {

            const content =
                event.content
                    ? JSON.parse(event.content)
                    : [];


            if (
                Array.isArray(content) &&
                content.length
            ) {

                content.forEach(block => {

                    /* =========================
                    PARAGRAPH
                    ========================= */

                    if (
                        block &&
                        block.type === "paragraph"
                    ) {

                        addParagraph(
                            block.text || ""
                        );

                    }

                    /* =========================
                    IMAGE
                    ========================= */

                    if (
                        block &&
                        block.type === "image"
                    ) {

                        addArticleImage({
                            src:
                                block.src || "",

                            caption:
                                block.caption || "",

                            alt:
                                block.alt || ""
                        });

                    }

                });

            } else {

                addParagraph();

            }


        } catch (error) {

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

        try {

            const schedules =
                event.schedule
                    ? JSON.parse(event.schedule)
                    : [];

            if (Array.isArray(schedules) && schedules.length) {

                schedules.forEach(item => {

                    addScheduleRow(
                        item.time ?? "",
                        item.title ?? "",
                        item.description ?? ""
                    );

                });

            } else {

                addScheduleRow();

            }

        } catch (error) {

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

    async function saveEvent() {

        try {

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

                    if (time || title || description) {

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
                    if (type === "paragraph") {

                        const text =
                            block
                                .querySelector(".paragraph-text")
                                ?.value
                                .trim();

                        if (text) {

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
                    if (type === "image") {

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

                        if (src) {

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
            REQUIRED VALIDATION
            ========================= */

            if (!payload.title) {

                alert("Title wajib diisi.");

                document
                    .getElementById("title")
                    .focus();

                return;

            }

            if (!payload.category) {

                alert("Category wajib dipilih.");

                document
                    .getElementById("category")
                    .focus();

                return;

            }

            if (!payload.cover_image) {

                alert("Cover Image wajib diupload.");

                document
                    .getElementById("imageFile")
                    .focus();

                return;

            }

            if (!payload.description) {

                alert("Description wajib diisi.");

                document
                    .getElementById("description")
                    .focus();

                return;

            }

            if (
                payload.category === "Others" &&
                !payload.category_name
            ) {

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

            try {

                result =
                    JSON.parse(raw);

            } catch (error) {

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
            if (result.success) {

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

        } catch (error) {

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

    function resetForm() {

        document.getElementById("eventId").value = "";

        document.getElementById("title").value = "";

        document.getElementById("slug").value = "";

        document.getElementById("category").value = "";

        document.getElementById("category_name").value = "";

        document.getElementById("customCategoryGroup").style.display = "none";

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

    async function deleteEvent(id) {

        if (!confirm("Delete event?"))
            return;

        await fetch(
            "../api/delete-event.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id })
            });
    }

    function addScheduleRow(

        time = "",
        title = "",
        description = ""

    ) {
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

    function addParagraph(text = "") {

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

    function addArticleImage(data = {}) {

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

            <!-- FILE INPUT -->

            <input
                type="file"
                class="article-image-file"
                accept="image/jpeg,image/png,image/webp,image/avif"
                hidden
            >


            <!-- UPLOAD AREA -->

            <div
                class="article-image-upload-area"
                onclick="
                    this
                    .closest('.image-item')
                    .querySelector('.article-image-file')
                    .click()
                "
            >

                <img
                    src="${ROOT_PATH}/admin/assets/icon/image-upload.png"
                    class="article-upload-icon"
                    alt="Upload Image"
                >


                <strong>
                    Upload Article Image
                </strong>


                <span>
                    Click to browse or select an image
                </span>


            </div>


            <!-- PREVIEW -->

            <div class="article-image-preview-wrapper" style=" display:none;">

                <img class="article-image-preview" alt="Article Image Preview">


                <div class="article-image-preview-footer">

                    <span class="article-image-file-name">
                        Image selected
                    </span>


                    <button type="button" class="article-image-change">
                        Change Image
                    </button>

                </div>

            </div>

        </div>


        <!-- CAPTION -->

        <div class="form-group">

            <label>
                Caption
            </label>


            <input
                type="text"
                class="article-image-caption"
                placeholder="Image caption..."
                value="${escapeHtml(data.caption || "")}"
            >

        </div>

        <!-- ALT TEXT -->

        <div class="form-group">

            <label>
                Alt Text
            </label>


            <input
                type="text"
                class="article-image-alt"
                placeholder="Describe this image..."
                value="${escapeHtml(data.alt || "")}"
            >

        </div>


        <!-- IMAGE PATH -->

        <input
            type="hidden"
            class="article-image-src"
            value="${escapeHtml(data.src || "")}"
        >

    `;

        container.appendChild(item);

        /*
        |--------------------------------------------------------------------------
        | EXISTING IMAGE
        |--------------------------------------------------------------------------
        */

        if (data.src) {

            const previewWrapper =
                item.querySelector(
                    ".article-image-preview-wrapper"
                );

            const preview =
                item.querySelector(
                    ".article-image-preview"
                );

            const uploadArea =
                item.querySelector(
                    ".article-image-upload-area"
                );

            preview.src =
                ROOT_PATH + "/" + data.src;

            previewWrapper.style.display =
                "block";

            uploadArea.style.display =
                "none";

            const fileName =
                item.querySelector(
                    ".article-image-file-name"
                );

            fileName.textContent =
                data.src.split("/").pop();

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
            function () {

                uploadArticleImage(
                    this,
                    item
                );

            }
        );

        const changeButton =
            item.querySelector(
                ".article-image-change"
            );

        changeButton.addEventListener(
            "click",
            function (event) {

                event.stopPropagation();

                fileInput.click();

            }
        );

    }

    async function uploadArticleImage(
        input,
        item
    ) {

        const file =
            input.files[0];

        if (!file) {
            return;
        }

        const formData =
            new FormData();

        formData.append(
            "image",
            file
        );

        try {

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

            if (!result.success) {

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
            const previewWrapper =
                item.querySelector(
                    ".article-image-preview-wrapper"
                );

            const preview =
                item.querySelector(
                    ".article-image-preview"
                );

            const uploadArea =
                item.querySelector(
                    ".article-image-upload-area"
                );

            const fileName =
                item.querySelector(
                    ".article-image-file-name"
                );

            preview.src =
                ROOT_PATH +
                "/" +
                result.path;

            previewWrapper.style.display =
                "block";

            uploadArea.style.display =
                "none";

            fileName.textContent =
                file.name;

        } catch (error) {

            console.error(
                "ARTICLE IMAGE UPLOAD ERROR:",
                error
            );

            alert(
                "Terjadi error saat upload gambar."
            );

        }

    }

    function searchEvent() {

        const keyword =
            document
                .getElementById("searchEvent")
                .value
                .toLowerCase();

        const rows =
            document.querySelectorAll(
                "#eventTable tr"
            );

        rows.forEach(row => {

            row.style.display =
                row.innerText
                    .toLowerCase()
                    .includes(keyword)
                    ? ""
                    : "none";

        });
    }

    function escapeHtml(value = "") {

        return String(value)

            .replace(/&/g, "&amp;")

            .replace(/</g, "&lt;")

            .replace(/>/g, "&gt;")

            .replace(/"/g, "&quot;")

            .replace(/'/g, "&#039;");

    }

    async function initForm() {

        const params = new URLSearchParams(window.location.search);

        const id = params.get("id");

        if (!id) {

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
        .addEventListener("change", async function () {

            const file = this.files[0];

            if (!file) return;

            /* =========================
               LOCAL PREVIEW
               ========================= */

            const preview =
                document.getElementById("imagePreview");

            const localUrl =
                URL.createObjectURL(file);

            preview.src =
                localUrl;

            preview.style.display =
                "block";

            /* =========================
               UPLOAD
               ========================= */

            const formData =
                new FormData();

            formData.append(
                "image",
                file
            );

            try {

                const response =
                    await fetch(
                        API_URL + "/events/upload-image.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );

                const result =
                    await response.json();

                console.log(
                    "COVER IMAGE UPLOAD:",
                    result
                );

                if (result.success) {

                    /*
                    |----------------------------------------------------------
                    | SAVE IMAGE PATH
                    |----------------------------------------------------------
                    */

                    document
                        .getElementById("image")
                        .value =
                        result.path;

                    /*
                    |----------------------------------------------------------
                    | USE SERVER IMAGE PATH
                    |----------------------------------------------------------
                    */

                    preview.src =
                        ROOT_PATH +
                        "/" +
                        result.path;

                    preview.style.display =
                        "block";

                } else {

                    /*
                    |----------------------------------------------------------
                    | REMOVE PREVIEW IF UPLOAD FAILED
                    |----------------------------------------------------------
                    */

                    preview.src = "";

                    preview.style.display =
                        "none";

                    document
                        .getElementById("image")
                        .value = "";

                    alert(
                        result.message ||
                        "Upload gambar gagal."
                    );

                }

            } catch (error) {

                console.error(
                    "COVER IMAGE UPLOAD ERROR:",
                    error
                );

                preview.src = "";

                preview.style.display =
                    "none";

                document
                    .getElementById("image")
                    .value = "";

                alert(
                    "Terjadi error saat upload gambar."
                );

            }

        });

    document
        .getElementById("title")
        .addEventListener("keyup", function () {

            let slug =
                this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, "-")
                    .replace(/^-|-$/g, "");

            document
                .getElementById("slug")
                .value = slug;

        });

    document
        .getElementById("featured")
        .addEventListener("change", function () {

            const fields =
                document.getElementById("featuredDateFields");

            if (this.checked) {

                fields.style.display = "block";

            } else {

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
        .addEventListener("change", function () {

            const customGroup =
                document.getElementById("customCategoryGroup");

            const customInput =
                document.getElementById("category_name");

            const isOthers =
                this.value === "Others";

            if (isOthers) {

                customGroup.style.display = "block";

                customInput.focus();

            } else {

                customGroup.style.display = "none";

                customInput.value = "";

            }

        });