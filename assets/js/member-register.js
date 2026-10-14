/* =============================================================
   JAKARTA FILM COMMISSION
   MEMBER REGISTRATION - SHARED HELPERS
============================================================= */
(function (global) {
  "use strict";

  const MR = {};

  /* ---------------------------------------------------------
     UTIL
  --------------------------------------------------------- */
  MR.esc = function (value) {
    return String(value == null ? "" : value).replace(/[&<>"']/g, (c) => {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  };

  MR.formatBytes = function (bytes) {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
  };

  MR.storage = {
    get(key) {
      try {
        return window.localStorage.getItem(key);
      } catch (e) {
        return null;
      }
    },
    set(key, value) {
      try {
        window.localStorage.setItem(key, value);
      } catch (e) {
        /* abaikan: private mode / storage penuh */
      }
    },
    remove(key) {
      try {
        window.localStorage.removeItem(key);
      } catch (e) {
        /* abaikan */
      }
    },
  };

  MR.isEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);

  MR.isPhone = function (v) {
    const cleaned = v.replace(/[\s().-]/g, "");
    return /^\+?\d{9,15}$/.test(cleaned);
  };

  /* Mengembalikan URL valid (menambah https:// bila kosong) atau null */
  MR.normalizeUrl = function (v) {
    let value = (v || "").trim();
    if (!value) return null;
    if (!/^https?:\/\//i.test(value)) value = "https://" + value;
    try {
      const url = new URL(value);
      if (!url.hostname.includes(".")) return null;
      return url.href;
    } catch (e) {
      return null;
    }
  };

  /* ---------------------------------------------------------
     ERROR HANDLING
     Struktur: <div class="mr-field" data-field="x"> ... <p class="mr-error">
  --------------------------------------------------------- */
  MR.setError = function (field, message) {
    if (!field) return;
    field.classList.add("has-error");
    const err = field.querySelector(".mr-error");
    if (err) err.textContent = message;
    const control = field.querySelector(".mr-input, .mr-textarea");
    if (control) control.setAttribute("aria-invalid", "true");
  };

  MR.clearError = function (field) {
    if (!field) return;
    field.classList.remove("has-error");
    const err = field.querySelector(".mr-error");
    if (err) err.textContent = "";
    const control = field.querySelector(".mr-input, .mr-textarea");
    if (control) control.removeAttribute("aria-invalid");
  };

  MR.clearAllErrors = function (root) {
    root.querySelectorAll(".mr-field.has-error").forEach(MR.clearError);
  };

  MR.focusFirstError = function (root) {
    const first = root.querySelector(".mr-field.has-error");
    if (!first) return;
    first.scrollIntoView({ behavior: "smooth", block: "center" });
    const target = first.querySelector(".mr-input, .mr-textarea, input, .mr-drop");
    if (target && typeof target.focus === "function") {
      setTimeout(() => target.focus({ preventScroll: true }), 350);
    }
  };

  /* Hapus error otomatis saat pengguna mengubah isian */
  MR.bindLiveClear = function (form) {
    const handler = (event) => {
      /* error upload ditangani oleh dropzone sendiri */
      if (event.target.type === "file") return;
      const field = event.target.closest(".mr-field");
      if (field) MR.clearError(field);
    };
    form.addEventListener("input", handler);
    form.addEventListener("change", handler);
  };

  /* ---------------------------------------------------------
     OPTION GROUPS (radio / checkbox + "Yang lain")
     Opsi lainnya: <input value="__other"> + <input class="mr-other-input">
  --------------------------------------------------------- */
  MR.initOptionGroups = function (form) {
    const sync = () => {
      form.querySelectorAll(".mr-options").forEach((group) => {
        const other = group.querySelector('input[value="__other"]');
        const text = group.querySelector(".mr-other-input");
        if (!other || !text) return;
        const open = other.checked;
        text.classList.toggle("is-open", open);
        if (!open) text.value = "";
      });
    };

    form.addEventListener("change", (event) => {
      sync();
      const other = event.target.closest(".mr-options") && event.target.value === "__other";
      if (other && event.target.checked) {
        const text = event.target.closest(".mr-options").querySelector(".mr-other-input");
        if (text) text.focus();
      }
    });

    sync();
    MR.syncOptionGroups = sync;
  };

  /* Ambil nilai grup: { values: [...], other: "" } */
  MR.readGroup = function (form, name) {
    const inputs = Array.from(form.querySelectorAll(`input[name="${name}"]:checked`));
    const group = form.querySelector(`input[name="${name}"]`);
    const textEl = group ? group.closest(".mr-options").querySelector(".mr-other-input") : null;
    const values = [];
    let other = "";
    inputs.forEach((input) => {
      if (input.value === "__other") {
        other = textEl ? textEl.value.trim() : "";
      } else {
        values.push(input.value);
      }
    });
    return { values, other, otherChecked: inputs.some((i) => i.value === "__other") };
  };

  /* Gabungkan pilihan + isian lain menjadi satu array label */
  MR.groupLabels = function (g) {
    return g.other ? g.values.concat(g.other) : g.values.slice();
  };

  /* ---------------------------------------------------------
     DROPZONE
     <div class="mr-drop" tabindex="0" role="button"> <input type="file"> ...
  --------------------------------------------------------- */
  MR.initDrop = function (options) {
    const root = options.root;
    const input = root.querySelector('input[type="file"]');
    const field = root.closest(".mr-field");
    const title = root.querySelector(".mr-drop-title");
    const defaultTitle = title ? title.textContent : "";
    const img = root.querySelector("img");
    const ext = root.querySelector(".mr-doc-ext");
    let objectUrl = null;

    const fail = (message) => {
      MR.setError(field, message);
      input.value = "";
    };

    const validate = (file) => {
      const name = file.name.toLowerCase();
      const extension = name.includes(".") ? name.split(".").pop() : "";
      const okType =
        options.mimes.includes(file.type) || options.exts.includes(extension);
      if (!okType) return options.typeMessage;
      if (file.size > options.maxBytes) {
        return "Ukuran file " + MR.formatBytes(file.size) + " melebihi batas " + MR.formatBytes(options.maxBytes) + ".";
      }
      return null;
    };

    const handle = (file) => {
      if (!file) return;
      const problem = validate(file);
      if (problem) {
        fail(problem);
        return;
      }
      MR.clearError(field);
      options.onFile(file);
    };

    /* dipanggil halaman saat file final siap (mis. setelah crop) */
    const api = {
      setFile(file) {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        root.classList.add("has-file");
        if (title) title.textContent = file.name;
        const sub = root.querySelector(".mr-drop-sub");
        if (sub) sub.textContent = MR.formatBytes(file.size) + " · siap diunggah";
        if (img && file.type.startsWith("image/")) {
          objectUrl = URL.createObjectURL(file);
          img.src = objectUrl;
        }
        if (ext) ext.textContent = (file.name.split(".").pop() || "file").toUpperCase();
        root.dispatchEvent(new CustomEvent("mr:file", { bubbles: true }));
      },
      clear() {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        input.value = "";
        root.classList.remove("has-file");
        if (title) title.textContent = defaultTitle;
        const sub = root.querySelector(".mr-drop-sub");
        if (sub) sub.textContent = sub.dataset.default || "";
        if (img) img.removeAttribute("src");
        if (options.onClear) options.onClear();
        root.dispatchEvent(new CustomEvent("mr:file", { bubbles: true }));
      },
    };

    const sub = root.querySelector(".mr-drop-sub");
    if (sub) sub.dataset.default = sub.textContent;

    root.addEventListener("click", (event) => {
      if (event.target.closest("[data-drop-remove]")) {
        event.stopPropagation();
        api.clear();
        return;
      }
      if (event.target.closest("[data-drop-edit]")) {
        event.stopPropagation();
        if (options.onEdit) options.onEdit();
        return;
      }
      input.click();
    });

    root.addEventListener("keydown", (event) => {
      if ((event.key === "Enter" || event.key === " ") && event.target === root) {
        event.preventDefault();
        input.click();
      }
    });

    input.addEventListener("click", (event) => event.stopPropagation());
    input.addEventListener("change", () => {
      handle(input.files && input.files[0]);
    });

    ["dragenter", "dragover"].forEach((name) => {
      root.addEventListener(name, (event) => {
        event.preventDefault();
        root.classList.add("is-dragging");
      });
    });
    ["dragleave", "drop"].forEach((name) => {
      root.addEventListener(name, (event) => {
        event.preventDefault();
        root.classList.remove("is-dragging");
      });
    });
    root.addEventListener("drop", (event) => {
      handle(event.dataTransfer && event.dataTransfer.files[0]);
    });

    return api;
  };

  /* ---------------------------------------------------------
     CHECKLIST + PROGRESS di panel kiri
     items: [{ label, ok: () => boolean }]
  --------------------------------------------------------- */
  MR.initChecklist = function (form, items) {
    const list = document.querySelector("[data-checklist]");
    const bar = document.querySelector("[data-progress-bar]");
    const text = document.querySelector("[data-progress-text]");
    if (!list) return { update() {} };

    list.innerHTML = items.map((item) => "<li>" + MR.esc(item.label) + "</li>").join("");
    const nodes = Array.from(list.children);

    const update = () => {
      let done = 0;
      items.forEach((item, i) => {
        const ok = !!item.ok();
        nodes[i].classList.toggle("is-done", ok);
        if (ok) done++;
      });
      const pct = Math.round((done / items.length) * 100);
      if (bar) bar.style.width = pct + "%";
      if (text) text.textContent = done + " dari " + items.length + " bagian wajib terisi";
    };

    form.addEventListener("input", update);
    form.addEventListener("change", update);
    form.addEventListener("mr:file", update);
    update();
    return { update };
  };

  /* ---------------------------------------------------------
     DRAFT (simpan otomatis di browser, tanpa file)
  --------------------------------------------------------- */
  MR.initDraft = function (form, key) {
    let timer = null;

    const collect = () => {
      const data = {};
      form.querySelectorAll("input, textarea, select").forEach((el) => {
        if (!el.name || el.type === "file") return;
        if (el.type === "checkbox" || el.type === "radio") {
          if (el.checked) data[el.name + "::" + el.value] = true;
        } else {
          data[el.name] = el.value;
        }
      });
      return data;
    };

    const save = () => {
      clearTimeout(timer);
      timer = setTimeout(() => MR.storage.set(key, JSON.stringify(collect())), 300);
    };

    const restore = () => {
      const raw = MR.storage.get(key);
      if (!raw) return;
      let data;
      try {
        data = JSON.parse(raw);
      } catch (e) {
        return;
      }
      form.querySelectorAll("input, textarea, select").forEach((el) => {
        if (!el.name || el.type === "file") return;
        if (el.type === "checkbox" || el.type === "radio") {
          el.checked = !!data[el.name + "::" + el.value];
        } else if (typeof data[el.name] === "string") {
          el.value = data[el.name];
        }
      });
      if (MR.syncOptionGroups) MR.syncOptionGroups();
      form.dispatchEvent(new Event("input", { bubbles: true }));
    };

    form.addEventListener("input", save);
    form.addEventListener("change", save);
    restore();

    return {
      clear() {
        clearTimeout(timer);
        MR.storage.remove(key);
      },
    };
  };

  /* ---------------------------------------------------------
     MODAL
  --------------------------------------------------------- */
  MR.modal = function (el) {
    let lastFocus = null;

    const close = () => {
      if (!el.classList.contains("is-open")) return;
      el.classList.remove("is-open");
      document.body.classList.remove("mr-lock");
      if (lastFocus && typeof lastFocus.focus === "function") lastFocus.focus();
      el.dispatchEvent(new CustomEvent("mr:close"));
    };

    const open = () => {
      lastFocus = document.activeElement;
      el.classList.add("is-open");
      document.body.classList.add("mr-lock");
      const card = el.querySelector(".mr-modal-card");
      if (card) {
        card.scrollTop = 0;
        card.setAttribute("tabindex", "-1");
        card.focus({ preventScroll: true });
      }
    };

    el.addEventListener("click", (event) => {
      if (event.target === el || event.target.closest("[data-modal-close]")) close();
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && el.classList.contains("is-open")) close();
    });

    return { open, close, el };
  };

  /* ---------------------------------------------------------
     KIRIM DATA
     DEMO_MODE = true  -> hanya simulasi (belum ada backend publik)
     DEMO_MODE = false -> POST FormData ke API_URL, respons JSON { success, message }
  --------------------------------------------------------- */
  MR.send = async function (url, formData, demo) {
    if (demo) {
      await new Promise((resolve) => setTimeout(resolve, 900));
      return { success: true, message: "Demo mode: data belum dikirim ke server." };
    }

    const response = await fetch(url, { method: "POST", body: formData });
    let result = null;
    try {
      result = await response.json();
    } catch (e) {
      throw new Error("Server memberikan respons yang tidak dikenali.");
    }
    if (!response.ok || !result.success) {
      throw new Error((result && result.message) || "Gagal mengirim pendaftaran.");
    }
    return result;
  };

  /* Bangun baris ringkasan untuk modal review */
  MR.summaryItem = function (label, valueHtml, full) {
    return (
      '<div class="mr-sum-item' + (full ? " is-full" : "") + '"><dt>' +
      MR.esc(label) +
      "</dt><dd>" +
      valueHtml +
      "</dd></div>"
    );
  };

  MR.chipsHtml = function (labels) {
    if (!labels.length) return "-";
    return '<div class="mr-chips">' + labels.map((l) => '<span class="mr-chip">' + MR.esc(l) + "</span>").join("") + "</div>";
  };

  global.MR = MR;
})(window);
