/* JAKARTA FILM COMMISSION - PRESS RELEASE FORM - FINAL VERSION */
window.JFC_PRESS_RELEASE_API = window.JFC_PRESS_RELEASE_API || "../../api";
window.JFC_PRESS_RELEASE_UPLOAD_API =
  window.JFC_PRESS_RELEASE_UPLOAD_API ||
  window.JFC_PRESS_RELEASE_API + "/press-release/upload-press-image.php";
window.JFC_PRESS_RELEASE_CREATE_API =
  window.JFC_PRESS_RELEASE_CREATE_API ||
  window.JFC_PRESS_RELEASE_API + "/press-release/create-press.php";
window.JFC_PRESS_RELEASE_UPDATE_API =
  window.JFC_PRESS_RELEASE_UPDATE_API ||
  window.JFC_PRESS_RELEASE_API + "/press-release/update-press.php";
window.JFC_PRESS_RELEASE_GET_API =
  window.JFC_PRESS_RELEASE_GET_API ||
  window.JFC_PRESS_RELEASE_API + "/press-release/get-press.php";
let articleBlocks = [],
  articleBlockCounter = 0,
  pressReleaseData = null,
  isEditMode = false,
  currentRichTextEditor = null,
  currentRichTextRange = null;
document.addEventListener("DOMContentLoaded", initializePressReleaseForm);
function initializePressReleaseForm() {
  const p = new URLSearchParams(location.search),
    id = p.get("id");
  isEditMode = !!id;
  setupTitleSlug();
  setupCategory();
  setupCoverImage();
  setupArticleButtons();
  setupSaveButton();
  setupRichTextModal();
  setupFormSubmitProtection();
  setupLocationDefault();
  if (isEditMode) loadPressRelease(id);
  else {
    setPageTitle("New Press Release");
    const l = document.getElementById("location");
    if (l && !l.value.trim()) l.value = "Jakarta";
  }
  renderArticleBlocks();
}
function getValue(id) {
  const e = document.getElementById(id);
  return e ? e.value || "" : "";
}
function setValue(id, v) {
  const e = document.getElementById(id);
  if (e) e.value = v ?? "";
}
function escapeHtml(v) {
  return String(v ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
function escapeAttribute(v) {
  return escapeHtml(v);
}
function resolveImagePath(path) {
  if (!path) return "";
  let v = String(path).trim().replace(/\\/g, "/");
  if (/^https?:\/\//i.test(v) || v.startsWith("/jfc/")) return v;
  if (v.startsWith("jfc/")) return "/" + v;
  if (v.startsWith("uploads/")) return "/jfc/" + v;
  if (v.startsWith("/uploads/")) return "/jfc" + v;
  if (v.startsWith("assets/")) return "/jfc/" + v;
  if (v.startsWith("/assets/")) return "/jfc" + v;
  return v;
}
function createBlockId() {
  return "article-block-" + Date.now() + "-" + ++articleBlockCounter;
}
function setPageTitle(t) {
  const e = document.getElementById("pageTitle");
  if (e) e.textContent = t;
}
function generateSlug(t) {
  return String(t || "")
    .toLowerCase()
    .trim()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/&/g, " and ")
    .replace(/[^a-z0-9\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .replace(/^-+|-+$/g, "");
}
function setupTitleSlug() {
  const t = document.getElementById("title"),
    s = document.getElementById("slug");
  if (!t) return;
  if (s) {
    s.readOnly = true;
    s.setAttribute("readonly", "readonly");
    s.setAttribute("tabindex", "-1");
    t.addEventListener("input", () => (s.value = generateSlug(t.value)));
    if (!isEditMode || !s.value) s.value = generateSlug(t.value);
  }
}
function setupCategory() {
  const c = document.getElementById("category"),
    g = document.getElementById("categoryNameGroup"),
    n = document.getElementById("category_name");
  if (!c) return;
  const u = () => {
    if (c.value === "Others") {
      if (g) g.style.display = "";
    } else {
      if (g) g.style.display = "none";
      if (n) n.value = "";
    }
  };
  c.addEventListener("change", u);
  u();
}
function setupLocationDefault() {
  const l = document.getElementById("location");
  if (l)
    l.addEventListener("blur", () => {
      if (!l.value.trim()) l.value = "Jakarta";
    });
}
function setupArticleButtons() {
  const p = document.getElementById("addParagraphBtn"),
    i = document.getElementById("addImageBtn");
  if (p) p.addEventListener("click", () => addParagraphBlock());
  if (i) i.addEventListener("click", () => addImageBlock());
}
/* =========================================================
   PASTE SANITIZER
   (memaksa paste sebagai plain text agar style asing
   dari Word/Google Docs/halaman lain tidak ikut masuk)
========================================================= */
function handleRichTextPaste(e) {
  e.preventDefault();
  const clipboardData = e.clipboardData || window.clipboardData;
  const text = clipboardData ? clipboardData.getData("text/plain") : "";
  document.execCommand("insertText", false, text);
}
function addParagraphBlock(value = "") {
  articleBlocks.push({
    id: createBlockId(),
    type: "paragraph",
    content: value,
  });
  renderArticleBlocks();
  focusLastParagraph();
}
function addImageBlock(data = {}) {
  const b = {
    id: createBlockId(),
    type: "image",
    src: data.src || data.url || data.image || "",
    caption: data.caption || "",
    alt: data.alt || data.alt_text || "",
    file: null,
    uploaded: !!(data.src || data.url || data.image),
  };
  articleBlocks.push(b);
  renderArticleBlocks();
  setTimeout(() => {
    const i = document.querySelector(`[data-image-input="${b.id}"]`);
    if (i) i.click();
  }, 100);
}
function renderArticleBlocks() {
  const c = document.getElementById("articleContent"),
    e = document.getElementById("articleEmptyState");
  if (!c) return;
  c.innerHTML = "";
  if (!articleBlocks.length) {
    if (e) e.style.display = "block";
    return;
  }
  if (e) e.style.display = "none";
  articleBlocks.forEach((b, i) => {
    const x =
      b.type === "paragraph"
        ? createParagraphElement(b, i)
        : b.type === "image"
          ? createImageElement(b, i)
          : null;
    if (x) c.appendChild(x);
  });
}
function createParagraphElement(b, index) {
  const w = document.createElement("div");
  w.className = "article-block article-paragraph-block";
  w.dataset.blockId = b.id;
  w.innerHTML = `<div class="article-block-header"><div class="article-block-title"><i class="ri-text"></i><span>Paragraph</span></div><div class="article-block-actions"><button type="button" class="article-block-remove" title="Remove paragraph" data-action="remove"><i class="ri-delete-bin-line"></i></button></div></div><div class="article-block-body"><div class="article-richtext-editor"><div class="article-richtext-toolbar"><button type="button" class="richtext-btn" data-command="bold" title="Bold"><i class="ri-bold"></i></button><button type="button" class="richtext-btn" data-command="italic" title="Italic"><i class="ri-italic"></i></button><button type="button" class="richtext-btn" data-command="underline" title="Underline"><i class="ri-underline"></i></button><span class="richtext-toolbar-divider"></span><button type="button" class="richtext-btn" data-command="createLink" title="Insert Link"><i class="ri-link"></i></button><button type="button" class="richtext-btn" data-command="unlink" title="Remove Link"><i class="ri-link-unlink"></i></button></div><div class="article-richtext-input" contenteditable="true" data-placeholder="Write your paragraph..."></div></div></div>`;
  const ed = w.querySelector(".article-richtext-input");
  ed.innerHTML = b.content || "";
  ed.addEventListener("input", () => (b.content = ed.innerHTML));
  ed.addEventListener("paste", handleRichTextPaste);
  ["focus", "click"].forEach((ev) =>
    ed.addEventListener(ev, () => {
      currentRichTextEditor = ed;
      updateRichTextToolbarState(ed);
    }),
  );
  ["keyup", "mouseup"].forEach((ev) =>
    ed.addEventListener(ev, () => updateRichTextToolbarState(ed)),
  );
  w.querySelectorAll(".richtext-btn").forEach((btn) => {
    btn.addEventListener("mousedown", (e) => e.preventDefault());
    btn.addEventListener("click", () =>
      handleRichTextCommand(btn.dataset.command, ed),
    );
  });
  const r = w.querySelector('[data-action="remove"]');
  if (r) r.addEventListener("click", () => removeArticleBlock(index));
  return w;
}
function createImageElement(b, index) {
  const w = document.createElement("div");
  w.className = "article-block article-image-block";
  w.dataset.blockId = b.id;
  w.innerHTML = `<div class="article-block-header"><div class="article-block-title"><i class="ri-image-line"></i><span>Image</span></div><div class="article-block-actions"><button type="button" class="article-block-remove" title="Remove image" data-action="remove"><i class="ri-delete-bin-line"></i></button></div></div><div class="article-block-body"><div class="article-image-editor"><div class="article-image-upload"><label class="article-upload-area" for=""><div class="article-upload-placeholder"><i class="ri-image-add-line"></i><strong>Upload Article Image</strong><span>JPG, PNG or WEBP</span><small>Maximum 5 MB · Recommended 16:9</small></div><img class="article-image-preview" src="" alt="Article Image Preview"></label><input type="file" class="article-image-input" accept="image/jpeg,image/png,image/webp" hidden><button type="button" class="btn btn-secondary article-change-image-btn"><i class="ri-image-edit-line"></i> Choose Image</button></div><div class="article-image-fields"><div class="form-group"><label>Caption</label><input type="text" class="article-image-caption" placeholder="Enter image caption..."><small>Optional caption displayed below the image.</small></div><div class="form-group"><label>Alternative Text</label><input type="text" class="article-image-alt" placeholder="Describe the image..."><small>Used for accessibility and SEO.</small></div></div></div></div>`;
  const area = w.querySelector(".article-upload-area"),
    input = w.querySelector(".article-image-input"),
    preview = w.querySelector(".article-image-preview"),
    ph = w.querySelector(".article-upload-placeholder"),
    cap = w.querySelector(".article-image-caption"),
    alt = w.querySelector(".article-image-alt"),
    change = w.querySelector(".article-change-image-btn"),
    remove = w.querySelector('[data-action="remove"]');
  const iid = "article-image-input-" + b.id;
  input.id = iid;
  area.setAttribute("for", iid);
  input.dataset.imageInput = b.id;
  if (b.src) {
    preview.src = resolveImagePath(b.src);
    preview.style.display = "block";
    ph.style.display = "none";
  } else {
    preview.style.display = "none";
    ph.style.display = "flex";
  }
  cap.value = b.caption || "";
  alt.value = b.alt || "";
  change.addEventListener("click", (e) => {
    e.preventDefault();
    input.click();
  });
  area.addEventListener("click", (e) => {
    e.preventDefault();
    input.click();
  });
  input.addEventListener("change", () => handleArticleImageSelect(input, b, w));
  cap.addEventListener("input", () => (b.caption = cap.value));
  alt.addEventListener("input", () => (b.alt = alt.value));
  remove.addEventListener("click", () => removeArticleBlock(index));
  return w;
}
function previewFile(file, preview, placeholder) {
  if (!file || !preview) return;
  const r = new FileReader();
  r.onload = (e) => {
    preview.src = e.target.result;
    preview.style.display = "block";
    if (placeholder) placeholder.style.display = "none";
  };
  r.readAsDataURL(file);
}
function showImageUploadingState(w, loading) {
  const b = w?.querySelector(".article-change-image-btn");
  if (!b) return;
  b.disabled = loading;
  b.innerHTML = loading
    ? `<i class="ri-loader-4-line ri-spin"></i> Uploading...`
    : `<i class="ri-image-edit-line"></i> Choose Image`;
}
async function uploadPressReleaseImage(file, type = "article") {
  if (!file) throw Error("No image file selected.");
  const endpoint = window.JFC_PRESS_RELEASE_UPLOAD_API;
  if (!endpoint) throw Error("Upload API URL is not available.");
  const fd = new FormData();
  fd.append("image", file);
  fd.append("type", type);
  let response;
  try {
    response = await fetch(endpoint, { method: "POST", body: fd });
  } catch (e) {
    throw Error("Unable to connect to the image upload server.");
  }
  let result;
  try {
    result = await response.json();
  } catch (e) {
    throw Error(`Server returned HTTP ${response.status}.`);
  }
  if (!response.ok)
    throw Error(
      result?.message ||
        result?.error ||
        `Server returned HTTP ${response.status}.`,
    );
  if (!result?.success)
    throw Error(result?.message || result?.error || "Failed to upload image.");
  const f = result.file || result.data?.file || result.data;
  if (!f)
    throw Error(
      "Upload succeeded but the server did not return file information.",
    );
  const u = f.url || f.path || f.src;
  if (!u) throw Error("Upload succeeded but image URL was not returned.");
  const n = String(u).trim().replace(/\\/g, "/");
  return { ...f, url: n, path: n };
}
function setupCoverImage() {
  const input = document.getElementById("coverImage"),
    preview = document.getElementById("imagePreview"),
    existing = document.getElementById("existingCoverImage");
  if (!input) return;
  input.addEventListener("change", async function () {
    const file = this.files?.[0];
    if (!file) return;
    const types = ["image/jpeg", "image/png", "image/webp"];
    if (!types.includes(file.type)) {
      alert("Only JPG, PNG and WEBP images are allowed.");
      this.value = "";
      return;
    }
    if (file.size > 5 * 1024 * 1024) {
      alert("Image size must not exceed 5 MB.");
      this.value = "";
      return;
    }
    this._selectedFile = file;
    if (preview) {
      const r = new FileReader();
      r.onload = (e) => {
        preview.src = e.target.result;
        preview.style.display = "block";
      };
      r.readAsDataURL(file);
    }
    try {
      setCoverUploadingState(true);
      const u = await uploadPressReleaseImage(file, "cover"),
        p = u.url || u.path;
      if (!p) throw Error("Uploaded cover image path is missing.");
      if (existing) existing.value = p;
      input.dataset.uploadedPath = p;
      if (preview) {
        preview.src = resolveImagePath(p);
        preview.style.display = "block";
      }
    } catch (e) {
      alert(e.message || "Failed to upload cover image.");
      this.value = "";
      this._selectedFile = null;
      this.dataset.uploadedPath = "";
      if (existing) existing.value = "";
    } finally {
      setCoverUploadingState(false);
    }
  });
}

function setCoverUploadingState(loading) {
  const a = document.querySelector(".upload-area"),
    h = a?.querySelector("h4");
  if (!h) return;
  h.innerHTML = loading
    ? `<i class="ri-loader-4-line ri-spin"></i> Uploading...`
    : "Upload Cover Image";
}

function removeArticleBlock(i) {
  if (i < 0 || i >= articleBlocks.length) return;
  if (!confirm("Remove this article block?")) return;
  articleBlocks.splice(i, 1);
  renderArticleBlocks();
}

function focusLastParagraph() {
  setTimeout(() => {
    const e = document.querySelectorAll(".article-richtext-input");
    if (e.length) {
      currentRichTextEditor = e[e.length - 1];
      currentRichTextEditor.focus();
    }
  }, 50);
}

function handleRichTextCommand(cmd, ed) {
  if (!ed) return;
  currentRichTextEditor = ed;
  saveRichTextSelection(ed);
  if (cmd === "createLink") {
    openRichTextLinkModal(ed);
    return;
  }
  ed.focus();
  document.execCommand(cmd, false, null);
  syncCurrentEditor(ed);
  updateRichTextToolbarState(ed);
}

function saveRichTextSelection(ed) {
  const s = getSelection();
  if (s?.rangeCount && ed.contains(s.getRangeAt(0).commonAncestorContainer))
    currentRichTextRange = s.getRangeAt(0).cloneRange();
}

function restoreRichTextSelection(ed) {
  if (!currentRichTextRange) {
    ed.focus();
    return;
  }
  const s = getSelection();
  s.removeAllRanges();
  s.addRange(currentRichTextRange);
  ed.focus();
}

function syncCurrentEditor(ed) {
  const w = ed?.closest(".article-block");
  const b = w && articleBlocks.find((x) => x.id === w.dataset.blockId);
  if (b) b.content = ed.innerHTML;
}

function updateRichTextToolbarState(ed) {
  const w = ed?.closest(".article-richtext-editor");
  if (!w) return;
  w.querySelectorAll(".richtext-btn[data-command]").forEach((btn) => {
    let a = false,
      c = btn.dataset.command;
    if (["bold", "italic", "underline"].includes(c))
      try {
        a = document.queryCommandState(c);
      } catch (e) {}
    if (c === "createLink") a = isSelectionInsideLink(ed);
    btn.classList.toggle("active", a);
  });
}

function isSelectionInsideLink(ed) {
  const s = getSelection();
  if (!s?.rangeCount) return false;
  let n = s.anchorNode;
  while (n && n !== ed) {
    if (n.nodeType === Node.ELEMENT_NODE && n.tagName === "A") return true;
    n = n.parentNode;
  }
  return false;
}

function setupRichTextModal() {}

function openRichTextLinkModal(ed) {
  currentRichTextEditor = ed;
  saveRichTextSelection(ed);
  document.getElementById("richTextLinkModalOverlay")?.remove();
  const s = getSelection();
  const text = s?.toString().trim() || "";
  let url = "",
    blank = true,
    n = s?.anchorNode;
  while (n && n !== ed) {
    if (n.nodeType === Node.ELEMENT_NODE && n.tagName === "A") {
      url = n.getAttribute("href") || "";
      blank = n.getAttribute("target") === "_blank";
      break;
    }
    n = n.parentNode;
  }

  const o = document.createElement("div");
  o.id = "richTextLinkModalOverlay";
  o.className = "richtext-link-modal-overlay";
  o.innerHTML = `<div class="richtext-link-modal" role="dialog" aria-modal="true"><div class="richtext-link-modal-header"><div class="richtext-link-modal-icon"><i class="ri-link"></i></div><div class="richtext-link-modal-heading"><h3>Insert Link</h3><p>Add a link to the selected text.</p></div><button type="button" class="richtext-link-modal-close" id="richTextLinkClose"><i class="ri-close-line"></i></button></div><div class="richtext-link-modal-body"><div class="form-group"><label>Selected Text</label><input type="text" id="richTextLinkText" value="${escapeAttribute(text)}" readonly></div><div class="form-group"><label for="richTextLinkUrl">URL</label><input type="url" id="richTextLinkUrl" placeholder="https://example.com" value="${escapeAttribute(url)}" autocomplete="off"></div><label class="richtext-link-checkbox"><input type="checkbox" id="richTextLinkNewTab" ${blank ? "checked" : ""}><span class="richtext-link-checkbox-box"><i class="ri-check-line"></i></span> Open link in a new tab</label></div><div class="richtext-link-modal-footer"><button type="button" class="btn btn-secondary" id="richTextLinkCancel">Cancel</button><button type="button" class="btn btn-primary" id="richTextLinkApply"><i class="ri-link"></i> Apply Link</button></div></div>`;
  document.body.appendChild(o);
  requestAnimationFrame(() => o.classList.add("show"));

  const ui = (id) => document.getElementById(id),
    urlInput = ui("richTextLinkUrl"),
    newTab = ui("richTextLinkNewTab");

  const close = () => {
    o.classList.remove("show");
    setTimeout(() => o.remove(), 180);
    document.removeEventListener("keydown", esc);
  };

  const esc = (e) => {
    if (e.key === "Escape") close();
  };
  ui("richTextLinkClose").onclick = close;
  ui("richTextLinkCancel").onclick = close;
  o.onclick = (e) => {
    if (e.target === o) close();
  };

  document.addEventListener("keydown", esc);
  ui("richTextLinkApply").onclick = () => {
    let u = urlInput.value.trim();
    if (!u) return alert("Please enter a URL.");
    if (!/^https?:\/\//i.test(u)) u = "https://" + u;
    try {
      new URL(u);
    } catch (e) {
      alert("Please enter a valid URL.");
      return;
    }
    restoreRichTextSelection(ed);
    const cs = getSelection();
    if (!cs?.toString().trim()) {
      alert("Please select text in the paragraph first.");
      return;
    }

    document.execCommand("createLink", false, u);
    const links = ed.querySelectorAll("a"),
      last = links[links.length - 1];
    if (last) {
      if (newTab.checked) {
        last.target = "_blank";
        last.rel = "noopener noreferrer";
      } else {
        last.removeAttribute("target");
        last.removeAttribute("rel");
      }
    }

    syncCurrentEditor(ed);
    updateRichTextToolbarState(ed);
    close();
  };

  setTimeout(() => {
    urlInput.focus();
    if (url) urlInput.select();
  }, 100);
}

function syncArticleBlocks() {
  document.querySelectorAll(".article-richtext-input").forEach((ed) => {
    const w = ed.closest(".article-block"),
      b = w && articleBlocks.find((x) => x.id === w.dataset.blockId);
    if (b) b.content = ed.innerHTML;
  });

  document.querySelectorAll(".article-image-caption").forEach((i) => {
    const w = i.closest(".article-block"),
      b = w && articleBlocks.find((x) => x.id === w.dataset.blockId);
    if (b) b.caption = i.value;
  });

  document.querySelectorAll(".article-image-alt").forEach((i) => {
    const w = i.closest(".article-block"),
      b = w && articleBlocks.find((x) => x.id === w.dataset.blockId);
    if (b) b.alt = i.value;
  });

}

async function handleArticleImageSelect(input, b, w) {
  const file = input.files?.[0];

  if (!file) return;
  const types = ["image/jpeg", "image/png", "image/webp"];

  if (!types.includes(file.type)) {
    alert("Only JPG, PNG and WEBP images are allowed.");
    input.value = "";
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    alert("Image size must not exceed 5 MB.");
    input.value = "";
    return;
  }

  b.file = file;
  const p = w?.querySelector(".article-image-preview"),
    ph = w?.querySelector(".article-upload-placeholder");
  previewFile(file, p, ph);

  try {
    showImageUploadingState(w, true);
    const u = await uploadPressReleaseImage(file, "article"),
      path = u.url || u.path;
      
    if (!path) throw Error("Uploaded article image path is missing.");
    b.src = path;
    b.file = null;
    b.uploaded = true;
    input.dataset.uploadedPath = path;
    
    if (p) {
      p.src = resolveImagePath(path);
      p.style.display = "block";
    }

    if (ph) ph.style.display = "none";

  } catch (e) {
    alert(e.message || "Failed to upload article image.");
    input.value = "";
    b.file = null;
    b.src = "";
    b.uploaded = false;
    input.dataset.uploadedPath = "";

    if (p) {
      p.removeAttribute("src");
      p.style.display = "none";
    }

    if (ph) ph.style.display = "flex";

  } finally {
    showImageUploadingState(w, false);
  }

}

function validateForm() {

  const title = getValue("title").trim(),

  cat = getValue("category").trim(),

    cn = getValue("category_name").trim(),

    desc = getValue("description").trim(),

    status = getValue("status").trim(),

    loc = getValue("location").trim(),

    cover = getValue("existingCoverImage").trim();

  if (!title) return { valid: false, message: "Title is required." };

  if (!cat) return { valid: false, message: "Please select a category." };

  if (cat === "Others" && !cn)
    return { valid: false, message: "Please enter the custom category name." };

  if (!desc) return { valid: false, message: "Short description is required." };

  if (!loc) {
    const e = document.getElementById("location");

    if (e) e.value = "Jakarta";
  }

  if (!cover)
    return {
      valid: false,
      message: "Cover image is required. Please upload a cover image first.",
    };

  if (!["draft", "published"].includes(status))
    return { valid: false, message: "Invalid status." };

  if (!articleBlocks.length)
    return {
      valid: false,
      message: "Please add at least one paragraph to the article.",
    };

  if (
    !articleBlocks.some(
      (b) => b.type === "paragraph" && stripHtml(b.content).trim(),
    )
  )
    return {
      valid: false,
      message: "At least one paragraph with content is required.",
    };

  for (let i = 0; i < articleBlocks.length; i++) {

    const b = articleBlocks[i];

    if (b.type === "paragraph" && !stripHtml(b.content).trim())
      return { valid: false, message: `Paragraph ${i + 1} is empty.` };

    if (b.type === "image" && !b.src)
      return {
        valid: false,
        message: `Article image ${i + 1} has not finished uploading.`,
      };

  }

  return { valid: true };

}

function stripHtml(html) {

  const d = document.createElement("div");
  d.innerHTML = html || "";
  return d.textContent || d.innerText || "";

}

function collectFormData() {

  const cat = getValue("category").trim(),

  cn = getValue("category_name").trim(),

    content = articleBlocks.map((b) =>
      b.type === "paragraph"
        ? { type: "paragraph", content: b.content || "" }
        : b.type === "image"
          ? {
              type: "image",
              src: b.src || "",
              caption: b.caption || "",
              alt: b.alt || "",
            }
          : b,
    );

  let location = getValue("location").trim() || "Jakarta";

  return {

    id: getValue("pressReleaseId") || null,
    
    title: getValue("title").trim(),

    slug: getValue("slug").trim(),

    description: getValue("description").trim(),

    content: JSON.stringify(content),

    cover_image: getValue("existingCoverImage").trim(),

    category: cat,

    category_name: cat === "Others" ? cn : "",

    location,

    published_date: getValue("date") || null,

    status: getValue("status") || "draft",

    meta_title: getValue("meta_title").trim(),

    meta_description: getValue("meta_description").trim(),

  };

}

function setupSaveButton() {

  const b = document.getElementById("savePressReleaseBtn");

  if (b)
    b.addEventListener("click", (e) => {
      e.preventDefault();
      savePressRelease();
    });

}

function setupFormSubmitProtection() {

  const f = document.getElementById("pressReleaseForm");

  if (f)

    f.addEventListener("submit", (e) => {
      e.preventDefault();
      savePressRelease();
    });

}

async function savePressRelease() {
  const form = document.getElementById("pressReleaseForm"),
    button = document.getElementById("savePressReleaseBtn");

  if (!form) return;

  syncArticleBlocks();

  const v = validateForm();

  if (!v.valid) {
    alert(v.message);
    return;
  }

  const data = collectFormData();

  if (button) {
    button.disabled = true;
    button.dataset.originalText = button.innerHTML;
    button.innerHTML = `<i class="ri-loader-4-line ri-spin"></i> Saving...`;
  }

  try {

    const endpoint = isEditMode
        ? window.JFC_PRESS_RELEASE_UPDATE_API
        : window.JFC_PRESS_RELEASE_CREATE_API,

        response = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

    let result;

    try {
      result = await response.json();

    } catch (e) {

      throw Error(`Server returned HTTP ${response.status}.`);

    }

    if (!response.ok)
      throw Error(
        result?.message ||
          result?.error ||
          `Server returned HTTP ${response.status}.`,
      );

    if (!result?.success)
      throw Error(
        result?.message || result?.error || "Failed to save Press Release.",
      );

    alert(result.message || "Press Release successfully saved.");
    location.href = "index.php";

  } catch (e) {

    console.error(e);
    alert(e.message || "An error occurred while saving Press Release.");

  } finally {

    if (button) {
      button.disabled = false;

      if (button.dataset.originalText)
        button.innerHTML = button.dataset.originalText;
    }

  }

}

async function loadPressRelease(id) {

  try {
    const r = await fetch(
      window.JFC_PRESS_RELEASE_GET_API + "?id=" + encodeURIComponent(id),
    );

    if (!r.ok) throw Error("Failed to load Press Release. HTTP " + r.status);

    const result = await r.json();

    if (!result.success)
      throw Error(result.message || "Failed to load Press Release.");

    let item = null;

    if (Array.isArray(result.data?.items))
      item = result.data.items.find((x) => Number(x.id) === Number(id));

    if (!item && result.data?.item) item = result.data.item;

    if (!item && result.data?.id) item = result.data;

    if (!item) throw Error("Press Release not found.");

    pressReleaseData = item;

    populateForm(item);

    setPageTitle("Edit Press Release");

  } catch (e) {

    console.error(e);
    alert(e.message || "Failed to load Press Release.");

  }

}

function populateForm(item) {

  setValue("pressReleaseId", item.id);

  setValue("title", item.title);

  setValue("slug", item.slug || generateSlug(item.title));

  const slug = document.getElementById("slug");

  if (slug) slug.readOnly = true;

  if (item.category_name?.trim()) {

    setValue("category", "Others");
    setValue("category_name", item.category_name);

  } else {

    setValue("category", item.category);
    setValue("category_name", "");

  }

  setValue("date", item.published_date || item.start_date || "");

  setValue("location", item.location || "Jakarta");

  setValue("description", item.description || item.excerpt || "");

  setValue("status", item.status || "draft");

  setValue("meta_title", item.meta_title || "");

  setValue("meta_description", item.meta_description || "");

  document.getElementById("category")?.dispatchEvent(new Event("change"));

  setValue("existingCoverImage", item.cover_image || "");

  const ci = document.getElementById("coverImage");

  if (ci && item.cover_image) ci.dataset.uploadedPath = item.cover_image;

  if (item.cover_image) {

    const p = document.getElementById("imagePreview");

    if (p) {
      p.src = resolveImagePath(item.cover_image);
      p.style.display = "block";
    }
  }

  articleBlocks = parseArticleContent(item.content);
  renderArticleBlocks();
}

function parseArticleContent(content) {

  if (!content) return [];
  let parsed = content;

  if (typeof content === "string") {

    try {

      parsed = JSON.parse(content);

    } catch (e) {

      return [
        {
          id: createBlockId(),
          type: "paragraph",
          content: escapeHtml(content),
        },
      ];
      
    }
  }

  if (Array.isArray(parsed))
    return parsed.map((i) =>
      i.type === "image"
        ? {
            id: i.id || createBlockId(),
            type: "image",
            src: i.src || i.url || i.image || "",
            caption: i.caption || "",
            alt: i.alt || i.alt_text || "",
            file: null,
            uploaded: true,
          }
        : {
            id: i.id || createBlockId(),
            type: "paragraph",
            content: i.content || "",
          },
    );
  if (Array.isArray(parsed?.blocks)) return parseArticleContent(parsed.blocks);

  if (typeof parsed?.content === "string")

    return [
      { id: createBlockId(), type: "paragraph", content: parsed.content },
    ];

  return [];
}

window.savePressRelease = savePressRelease;
window.addParagraphBlock = addParagraphBlock;
window.addImageBlock = addImageBlock;
window.uploadPressReleaseImage = uploadPressReleaseImage;
window.loadPressRelease = loadPressRelease;
window.collectFormData = collectFormData;
