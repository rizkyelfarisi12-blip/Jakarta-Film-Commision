function formatDateTimeLocal(value) {

    if (!value) return "";

    return value
        .replace(" ", "T")
        .slice(0, 16);

}

/* =========================================================
   RICH TEXT STATE
========================================================= */
let currentRichTextEditor = null;
let currentRichTextRange = null;

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

    /* trigger toggle so featuredDateFields visibility matches state */
    document.getElementById("featured")
        .dispatchEvent(new Event("change"));


    /* =========================
       COVER IMAGE
    ========================= */
    document.getElementById("image").value =
        event.cover_image ?? "";

    if (event.cover_image) {

        document.getElementById("imagePreview").src =
            resolveImagePath(event.cover_image);

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
                | PARAGRAPH (rich text)
                |--------------------------------------------------------------------------
                */
                if (type === "paragraph") {

                    const editor =
                        block.querySelector(".article-richtext-input");

                    const html =
                        editor ? editor.innerHTML.trim() : "";

                    const plain =
                        stripHtmlText(html).trim();

                    if (plain) {

                        articleContent.push({

                            type: "paragraph",

                            text: html

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

    document.getElementById("featuredDateFields").style.display = "none";

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

/* =========================================================
   PARAGRAPH (RICH TEXT)
========================================================= */
function addParagraph(text = "") {

    const container =
        document.getElementById("paragraphContainer");

    const item =
        document.createElement("div");

    item.className =
        "article-block article-paragraph-block";

    item.dataset.type =
        "paragraph";

    item.innerHTML = `

    <div class="article-block-header">

        <div class="article-block-title">
            <i class="ri-text"></i>
            <span>Paragraph</span>
        </div>

        <div class="article-block-actions">

            <button
                type="button"
                class="article-block-remove"
                title="Remove paragraph"
                onclick="this.closest('.article-block').remove()"
            >
                <i class="ri-delete-bin-line"></i>
            </button>

        </div>

    </div>

    <div class="article-block-body">

        <div class="article-richtext-editor">

            <div class="article-richtext-toolbar">

                <button type="button" class="richtext-btn" data-command="bold" title="Bold">
                    <i class="ri-bold"></i>
                </button>

                <button type="button" class="richtext-btn" data-command="italic" title="Italic">
                    <i class="ri-italic"></i>
                </button>

                <button type="button" class="richtext-btn" data-command="underline" title="Underline">
                    <i class="ri-underline"></i>
                </button>

                <span class="richtext-toolbar-divider"></span>

                <button type="button" class="richtext-btn" data-command="createLink" title="Insert Link">
                    <i class="ri-link"></i>
                </button>

                <button type="button" class="richtext-btn" data-command="unlink" title="Remove Link">
                    <i class="ri-link-unlink"></i>
                </button>

            </div>

            <div
                class="article-richtext-input"
                contenteditable="true"
                data-placeholder="Write your paragraph..."
            ></div>

        </div>

    </div>

    `;

    container.appendChild(item);

    const editor =
        item.querySelector(".article-richtext-input");

    editor.innerHTML = text || "";

    ["focus", "click"].forEach(ev =>
        editor.addEventListener(ev, () => {
            currentRichTextEditor = editor;
            updateRichTextToolbarState(editor);
        })
    );

    ["keyup", "mouseup"].forEach(ev =>
        editor.addEventListener(ev, () => updateRichTextToolbarState(editor))
    );

    editor.addEventListener("paste", handleRichTextPaste);

    item.querySelectorAll(".richtext-btn").forEach(btn => {
        btn.addEventListener("mousedown", e => e.preventDefault());
        btn.addEventListener("click", () =>
            handleRichTextCommand(btn.dataset.command, editor)
        );
    });

    setTimeout(() => {
        currentRichTextEditor = editor;
        editor.focus();
    }, 50);

}

/* =========================================================
   RICH TEXT COMMANDS
========================================================= */
function handleRichTextCommand(cmd, editor) {

    if (!editor) return;

    currentRichTextEditor = editor;

    saveRichTextSelection(editor);

    if (cmd === "createLink") {
        openRichTextLinkModal(editor);
        return;
    }

    editor.focus();

    document.execCommand(cmd, false, null);

    updateRichTextToolbarState(editor);

}

function saveRichTextSelection(editor) {

    const selection = getSelection();

    if (
        selection?.rangeCount &&
        editor.contains(selection.getRangeAt(0).commonAncestorContainer)
    ) {
        currentRichTextRange = selection.getRangeAt(0).cloneRange();
    }

}

function restoreRichTextSelection(editor) {

    if (!currentRichTextRange) {
        editor.focus();
        return;
    }

    const selection = getSelection();

    selection.removeAllRanges();
    selection.addRange(currentRichTextRange);

    editor.focus();

}

function updateRichTextToolbarState(editor) {

    const wrapper =
        editor?.closest(".article-richtext-editor");

    if (!wrapper) return;

    wrapper.querySelectorAll(".richtext-btn[data-command]").forEach(btn => {

        let active = false;
        const command = btn.dataset.command;

        if (["bold", "italic", "underline"].includes(command)) {
            try {
                active = document.queryCommandState(command);
            } catch (error) {}
        }

        if (command === "createLink") {
            active = isSelectionInsideLink(editor);
        }

        btn.classList.toggle("active", active);

    });

}

function isSelectionInsideLink(editor) {

    const selection = getSelection();

    if (!selection?.rangeCount) return false;

    let node = selection.anchorNode;

    while (node && node !== editor) {

        if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "A") {
            return true;
        }

        node = node.parentNode;

    }

    return false;

}

/* =========================================================
   RICH TEXT LINK MODAL
========================================================= */
function openRichTextLinkModal(editor) {

    currentRichTextEditor = editor;

    saveRichTextSelection(editor);

    document.getElementById("richTextLinkModalOverlay")?.remove();

    const selection = getSelection();

    const selectedText = selection?.toString().trim() || "";

    let url = "",
        openInNewTab = true,
        node = selection?.anchorNode;

    while (node && node !== editor) {

        if (node.nodeType === Node.ELEMENT_NODE && node.tagName === "A") {
            url = node.getAttribute("href") || "";
            openInNewTab = node.getAttribute("target") === "_blank";
            break;
        }

        node = node.parentNode;

    }

    const overlay =
        document.createElement("div");

    overlay.id = "richTextLinkModalOverlay";
    overlay.className = "richtext-link-modal-overlay";

    overlay.innerHTML = `
        <div class="richtext-link-modal" role="dialog" aria-modal="true">

            <div class="richtext-link-modal-header">

                <div class="richtext-link-modal-icon">
                    <i class="ri-link"></i>
                </div>

                <div class="richtext-link-modal-heading">
                    <h3>Insert Link</h3>
                    <p>Add a link to the selected text.</p>
                </div>

                <button type="button" class="richtext-link-modal-close" id="richTextLinkClose">
                    <i class="ri-close-line"></i>
                </button>

            </div>

            <div class="richtext-link-modal-body">

                <div class="form-group">
                    <label>Selected Text</label>
                    <input type="text" id="richTextLinkText" value="${escapeAttribute(selectedText)}" readonly>
                </div>

                <div class="form-group">
                    <label for="richTextLinkUrl">URL</label>
                    <input type="url" id="richTextLinkUrl" placeholder="https://example.com" value="${escapeAttribute(url)}" autocomplete="off">
                </div>

                <label class="richtext-link-checkbox">
                    <input type="checkbox" id="richTextLinkNewTab" ${openInNewTab ? "checked" : ""}>
                    <span class="richtext-link-checkbox-box"><i class="ri-check-line"></i></span>
                    Open link in a new tab
                </label>

            </div>

            <div class="richtext-link-modal-footer">
                <button type="button" class="btn btn-secondary" id="richTextLinkCancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="richTextLinkApply"><i class="ri-link"></i> Apply Link</button>
            </div>

        </div>
    `;

    document.body.appendChild(overlay);

    requestAnimationFrame(() => overlay.classList.add("show"));

    const getEl = id => document.getElementById(id),
        urlInput = getEl("richTextLinkUrl"),
        newTabCheckbox = getEl("richTextLinkNewTab");

    const close = () => {
        overlay.classList.remove("show");
        setTimeout(() => overlay.remove(), 180);
        document.removeEventListener("keydown", onEscape);
    };

    const onEscape = e => {
        if (e.key === "Escape") close();
    };

    getEl("richTextLinkClose").onclick = close;
    getEl("richTextLinkCancel").onclick = close;

    overlay.onclick = e => {
        if (e.target === overlay) close();
    };

    document.addEventListener("keydown", onEscape);

    getEl("richTextLinkApply").onclick = () => {

        let link = urlInput.value.trim();

        if (!link) {
            alert("Please enter a URL.");
            return;
        }

        if (!/^https?:\/\//i.test(link)) {
            link = "https://" + link;
        }

        try {
            new URL(link);
        } catch (error) {
            alert("Please enter a valid URL.");
            return;
        }

        restoreRichTextSelection(editor);

        const currentSelection = getSelection();

        if (!currentSelection?.toString().trim()) {
            alert("Please select text in the paragraph first.");
            return;
        }

        document.execCommand("createLink", false, link);

        const links = editor.querySelectorAll("a"),
            lastLink = links[links.length - 1];

        if (lastLink) {
            if (newTabCheckbox.checked) {
                lastLink.target = "_blank";
                lastLink.rel = "noopener noreferrer";
            } else {
                lastLink.removeAttribute("target");
                lastLink.removeAttribute("rel");
            }
        }

        updateRichTextToolbarState(editor);

        close();

    };

    setTimeout(() => {
        urlInput.focus();
        if (url) urlInput.select();
    }, 100);

}

/* =========================================================
   IMAGE BLOCK (seragam dengan sistem Press Release)
========================================================= */
let articleImageInputCounter = 0;

function addArticleImage(data = {}) {

    const container =
        document.getElementById("paragraphContainer");

    const item =
        document.createElement("div");

    item.className =
        "article-block article-image-block";

    item.dataset.type =
        "image";

    const inputId =
        "article-image-input-" + Date.now() + "-" + (++articleImageInputCounter);

    item.innerHTML = `

    <div class="article-block-header">

        <div class="article-block-title">
            <i class="ri-image-line"></i>
            <span>Image</span>
        </div>

        <div class="article-block-actions">

            <button
                type="button"
                class="article-block-remove"
                title="Remove image"
                onclick="this.closest('.article-block').remove()"
            >
                <i class="ri-delete-bin-line"></i>
            </button>

        </div>

    </div>

    <div class="article-block-body">

        <div class="article-image-editor">

            <div class="article-image-upload">

                <label class="article-upload-area" for="${inputId}">

                    <div class="article-upload-placeholder">
                        <i class="ri-image-add-line"></i>
                        <strong>Upload Article Image</strong>
                        <span>JPG, PNG or WEBP</span>
                        <small>Maximum 5 MB &middot; Recommended 16:9</small>
                    </div>

                    <img class="article-image-preview" src="" alt="Article Image Preview">

                </label>

                <input
                    type="file"
                    id="${inputId}"
                    class="article-image-file"
                    accept="image/jpeg,image/png,image/webp,image/avif"
                    hidden
                >

                <button type="button" class="btn btn-secondary article-change-image-btn">
                    <i class="ri-image-edit-line"></i>
                    Choose Image
                </button>

            </div>

            <div class="article-image-fields">

                <div class="form-group">
                    <label>Caption</label>
                    <input
                        type="text"
                        class="article-image-caption"
                        placeholder="Enter image caption..."
                        value="${escapeAttribute(data.caption || "")}"
                    >
                    <small>Optional caption displayed below the image.</small>
                </div>

                <div class="form-group">
                    <label>Alternative Text</label>
                    <input
                        type="text"
                        class="article-image-alt"
                        placeholder="Describe the image..."
                        value="${escapeAttribute(data.alt || "")}"
                    >
                    <small>Used for accessibility and SEO.</small>
                </div>

            </div>

            <!-- IMAGE PATH -->

            <input
                type="hidden"
                class="article-image-src"
                value="${escapeAttribute(data.src || "")}"
            >

        </div>

    </div>

    `;

    container.appendChild(item);

    const preview =
        item.querySelector(".article-image-preview");

    const placeholder =
        item.querySelector(".article-upload-placeholder");

    const fileInput =
        item.querySelector(".article-image-file");

    const changeButton =
        item.querySelector(".article-change-image-btn");

    /*
    |--------------------------------------------------------------------------
    | EXISTING IMAGE
    |--------------------------------------------------------------------------
    */

    if (data.src) {

        preview.src =
            resolveImagePath(data.src);

        preview.style.display =
            "block";

        placeholder.style.display =
            "none";

    } else {

        preview.style.display =
            "none";

        placeholder.style.display =
            "flex";

    }

    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD
    |--------------------------------------------------------------------------
    */

    fileInput.addEventListener(
        "change",
        function () {

            uploadArticleImage(
                this,
                item
            );

        }
    );

    changeButton.addEventListener(
        "click",
        function (event) {

            event.preventDefault();

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

    formData.append(
        "type",
        "article"
    );

    const changeButton =
        item.querySelector(".article-change-image-btn");

    const originalButtonHtml =
        changeButton ? changeButton.innerHTML : "";

    if (changeButton) {
        changeButton.disabled = true;
        changeButton.innerHTML = "Uploading...";
    }

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
        const preview =
            item.querySelector(".article-image-preview");

        const placeholder =
            item.querySelector(".article-upload-placeholder");

        preview.src =
            resolveImagePath(result.path);

        preview.style.display =
            "block";

        if (placeholder) {
            placeholder.style.display = "none";
        }

    } catch (error) {

        console.error(
            "ARTICLE IMAGE UPLOAD ERROR:",
            error
        );

        alert(
            "Terjadi error saat upload gambar."
        );

    } finally {

        if (changeButton) {
            changeButton.disabled = false;
            changeButton.innerHTML = originalButtonHtml;
        }

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

function escapeAttribute(value = "") {
    return escapeHtml(value);
}

function stripHtmlText(html) {
    const div = document.createElement("div");
    div.innerHTML = html || "";
    return div.textContent || div.innerText || "";
}

/* =========================================================
   IMAGE PATH RESOLVER
   (mencegah "//" protocol-relative URL yang membuat
   gambar rusak saat path dari API sudah absolut)
========================================================= */
function resolveImagePath(path) {
    if (!path) return "";
    let v = String(path).trim().replace(/\\/g, "/");
    if (/^https?:\/\//i.test(v) || v.startsWith("/jfc/")) return v;
    if (v.startsWith("jfc/")) return "/" + v;
    if (v.startsWith("uploads/")) return "/jfc/" + v;
    if (v.startsWith("/uploads/")) return "/jfc" + v;
    if (v.startsWith("assets/")) return "/jfc/" + v;
    if (v.startsWith("/assets/")) return "/jfc" + v;
    return (ROOT_PATH || "") + "/" + v.replace(/^\/+/, "");
}

/* =========================================================
   PASTE SANITIZER
   (memaksa paste sebagai plain text agar style asing
   dari Word/Google Docs/halaman lain tidak ikut masuk)
========================================================= */
function handleRichTextPaste(event) {
    event.preventDefault();
    const clipboardData = event.clipboardData || window.clipboardData;
    const text = clipboardData ? clipboardData.getData("text/plain") : "";
    document.execCommand("insertText", false, text);
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

        formData.append(
            "type",
            "cover"
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
                    resolveImagePath(result.path);

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