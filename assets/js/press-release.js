/* ============================================================
   JAKARTA FILM COMMISSION
   PRESS RELEASE
   USER UI / DATABASE API
   FIXED VERSION - AUTO DETECT BASE PATH (LOCAL & SERVER)
   ============================================================ */

/* ============================================================
   AUTO-DETECT BASE PATH
   ============================================================
   Sebelumnya PRESS_RELEASE_API di-hardcode absolute dari root
   domain ("/api/press-release/get-press.php"). Itu cocok kalau
   website ada di root domain server (https://jfc.co.id/...),
   tapi RUSAK kalau diakses di local lewat subfolder, misalnya:

     http://localhost/jfc/press-release-list.html

   karena browser akan meminta http://localhost/api/... (tanpa
   /jfc/), bukan http://localhost/jfc/api/...

   Fix: deteksi otomatis folder tempat file JS ini di-load lewat
   document.currentScript.src, lalu semua path API/gambar dibangun
   relatif terhadap folder itu. Otomatis benar baik di local
   (subfolder) maupun di server (root domain) tanpa perlu diubah
   manual setiap kali pindah environment.
   ============================================================ */
const SITE_BASE_URL = (function () {
  const scriptEl = document.currentScript;

  if (scriptEl && scriptEl.src) {
    // Ambil semua bagian URL SEBELUM "assets/js/press-release.js"
    return scriptEl.src.replace(/assets\/js\/press-release\.js.*$/, "");
  }

  // Fallback kalau currentScript tidak tersedia (misal script
  // dimuat secara dinamis). Asumsikan root domain.
  return window.location.origin + "/";
})();

/* ============================================================
   CONFIGURATION
   ============================================================ */
const PRESS_RELEASE_API = SITE_BASE_URL + "api/press-release/get-press.php";

const PRESS_RELEASE_DETAIL_API =
  SITE_BASE_URL + "api/press-release/get-press-detail.php";

const PRESS_RELEASE_UPLOAD_PATH = SITE_BASE_URL + "uploads/press-release";

// IMAGE BASE PATH
const PRESS_RELEASE_IMAGE_BASE = SITE_BASE_URL;

// Global data
let pressData = [];

// FETCH PRESS RELEASE LIST
async function fetchPressReleases(options = {}) {
  const { status = "published" } = options;

  try {
    let url = PRESS_RELEASE_API;

    // Status
    if (status !== "") {
      url += "?status=" + encodeURIComponent(status);
    }

    // Request
    const response = await fetch(url, {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
      cache: "no-store",
    });

    // HTTP Error
    if (!response.ok) {
      throw new Error("HTTP " + response.status + " - " + response.statusText);
    }

    // JSON
    const result = await response.json();

 
    // API Error
    if (!result.success) {
      throw new Error(result.message || "Failed to retrieve press releases.");
    }

    /*
        |--------------------------------------------------------------------------
        | ITEMS
        |--------------------------------------------------------------------------
        */

    const items =
      result.data && Array.isArray(result.data.items) ? result.data.items : [];

    /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

    pressData = items.map(normalizePressRelease);

    /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

    return pressData;
  } catch (error) {
    console.error("fetchPressReleases():", error);

    throw error;
  }
}

/*
|--------------------------------------------------------------------------
| GET DETAIL BY SLUG
|--------------------------------------------------------------------------
*/

async function fetchPressReleaseBySlug(slug) {
  if (!slug) {
    throw new Error("Press release slug is required.");
  }

  try {
    const url = PRESS_RELEASE_DETAIL_API + "?slug=" + encodeURIComponent(slug);

    /*
        |--------------------------------------------------------------------------
        | REQUEST
        |--------------------------------------------------------------------------
        */

    const response = await fetch(url, {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
      cache: "no-store",
    });

    /*
        |--------------------------------------------------------------------------
        | HTTP ERROR
        |--------------------------------------------------------------------------
        */

    if (!response.ok) {
      throw new Error("HTTP " + response.status + " - " + response.statusText);
    }

    /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

    const result = await response.json();

    /*
        |--------------------------------------------------------------------------
        | API ERROR
        |--------------------------------------------------------------------------
        */

    if (!result.success) {
      throw new Error(result.message || "Press release not found.");
    }

    /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

    if (!result.data) {
      throw new Error("Press release data is empty.");
    }

    /*
        |--------------------------------------------------------------------------
        | NORMALIZE
        |--------------------------------------------------------------------------
        */

    return normalizePressRelease(result.data);
  } catch (error) {
    console.error("fetchPressReleaseBySlug():", error);

    throw error;
  }
}

/*
|--------------------------------------------------------------------------
| NORMALIZE DATABASE DATA
|--------------------------------------------------------------------------
|
| Database:
|
| cover_image
| published_date
| category_display
| category_filter
|
| Frontend:
|
| image
| date
| category
|
|--------------------------------------------------------------------------
*/

function normalizePressRelease(item) {
  if (!item || typeof item !== "object") {
    return null;
  }

  /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

  const image = normalizeImagePath(item.cover_image);

  /*
    |--------------------------------------------------------------------------
    | DATE
    |--------------------------------------------------------------------------
    */

  const date = item.published_date || item.created_at || "";

  /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

  const category = item.category || "";

  /*
    |--------------------------------------------------------------------------
    | DISPLAY CATEGORY
    |--------------------------------------------------------------------------
    */

  const categoryDisplay =
    item.category_display ||
    (category === "Others" ? item.category_name || "Others" : category);

  /*
    |--------------------------------------------------------------------------
    | CONTENT
    |--------------------------------------------------------------------------
    */

  let content = [];

  /*
    |--------------------------------------------------------------------------
    | DETAIL API
    |--------------------------------------------------------------------------
    */

  if (Array.isArray(item.content_data)) {
    content = item.content_data;
  } else if (typeof item.content === "string" && item.content.trim() !== "") {

  /*
    |--------------------------------------------------------------------------
    | LIST API / STRING CONTENT
    |--------------------------------------------------------------------------
    */
    try {
      const decoded = JSON.parse(item.content);

      if (Array.isArray(decoded)) {
        content = decoded;
      }
    } catch (error) {
      console.warn("Unable to parse press release content:", error);
    }
  }

  /*
    |--------------------------------------------------------------------------
    | RETURN
    |--------------------------------------------------------------------------
    */

  return {

    // Database Identity
    id: Number(item.id) || 0,

    slug: item.slug || generateSlug(item.title || ""),

    // Basic Information
    title: item.title || "",

    description: item.description || "",
  
    // Image
    image: image,

    cover_image: item.cover_image || "",

    // Category
    category: category,

    category_name: item.category_name || "",

    category_display: categoryDisplay,

    category_filter: item.category_filter || category,

    // Date
    date: date,

    published_date: item.published_date || null,

    // Location
    location: item.location || "",

    // Status
    status: item.status || "",

    // Content
    content: content,

    raw_content: item.content || "",

    // SEO
    meta_title: item.meta_title || "",

    meta_description: item.meta_description || "",

    // Database timestamps
    created_at: item.created_at || null,

    updated_at: item.updated_at || null,
  };
}

/* ============================================================
   IMAGE PATH
   ============================================================
   Sebelumnya fungsi ini selalu memaksa hasil jadi absolute dari
   ROOT DOMAIN ("/" + imagePath). Sekarang path digabung dengan
   SITE_BASE_URL yang sudah otomatis menyesuaikan lokasi project
   (root domain di server, atau subfolder di local).
   ============================================================ */
function normalizeImagePath(path) {
  if (!path) {
    return "";
  }

  let imagePath = String(path).trim();

  if (imagePath === "") {
    return "";
  }

  // Normalize Windows path
  imagePath = imagePath.replace(/\\/g, "/");

  // Already absolute URL
  if (/^https?:\/\//i.test(imagePath)) {
    return imagePath;
  }

  // Remove old /jfc prefix (legacy)
  imagePath = imagePath.replace(/^\/jfc\//i, "/");
  imagePath = imagePath.replace(/^jfc\//i, "");

  // Buang leading slash apa pun supaya tidak dobel saat digabung
  // dengan SITE_BASE_URL
  imagePath = imagePath.replace(/^\/+/, "");

  // Gabungkan dengan base path yang terdeteksi otomatis
  return SITE_BASE_URL + imagePath;
}

/* ============================================================
   FORMAT DATE
   ============================================================ */

function formatDate(date) {
  if (!date) {
    return "-";
  }

  const dateObject = new Date(date);

  if (Number.isNaN(dateObject.getTime())) {
    return "-";
  }

  return dateObject.toLocaleDateString("id-ID", {
    day: "numeric",

    month: "long",

    year: "numeric",
  });
}

/* ============================================================
   CATEGORY COLOR CLASS
   ============================================================ */

function getCategoryClass(category) {
  const value = String(category || "")
    .trim()
    .toLowerCase();

  switch (value) {
    case "official release":
      return "official-release";

    case "program update":
      return "program";

    case "industry news":
      return "industry";

    case "others":
      return "others";

    default:
      return "others";
  }
}

/* ============================================================
   GET LATEST PRESS
   ============================================================ */

function getLatestPress(limit = 3, excludeSlug = null) {
  return [...pressData]

    .filter((item) => {
      if (!excludeSlug) {
        return true;
      }

      return item.slug !== excludeSlug;
    })

    .sort((a, b) => {
      const dateA = new Date(a.date || 0);

      const dateB = new Date(b.date || 0);

      return dateB - dateA;
    })

    .slice(0, limit);
}

/* ============================================================
   CALCULATE READ TIME
   ============================================================ */

function calculateReadTime(content) {
  if (!Array.isArray(content) || content.length === 0) {
    return "1 min read";
  }

  let text = "";

  content.forEach((block) => {
    if (!block || typeof block !== "object") {
      return;
    }

    // Paragraph
    if (block.type === "paragraph") {
      text += " " + stripHtml(block.content || "");
    } else if (typeof block.content === "string") {

      // Other text bloc
      text += " " + stripHtml(block.content);
    }
  });

  const words = text.trim().split(/\s+/).filter(Boolean);

  const minutes = Math.max(1, Math.ceil(words.length / 200));

  return minutes + " min read";
}

/* ============================================================
   STRIP HTML
   ============================================================ */

function stripHtml(html) {
  if (!html) {
    return "";
  }

  const temporary = document.createElement("div");

  temporary.innerHTML = html;

  return temporary.textContent || temporary.innerText || "";
}

/* ============================================================
   LIMIT TEXT
   ============================================================ */

function limitText(text, maxLength = 180) {
  if (!text) {
    return "";
  }

  const value = String(text).trim();

  if (value.length <= maxLength) {
    return value;
  }

  return value.substring(0, maxLength).trimEnd() + "...";
}

/* ============================================================
   GENERATE SLUG
   ============================================================ */

function generateSlug(text) {
  if (!text) {
    return "";
  }

  return String(text)
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, "")
    .replace(/[\s_-]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

/* ============================================================
   INITIAL LOAD HELPER
   ============================================================ */

async function loadPressReleases() {
  try {
    const data = await fetchPressReleases({
      status: "published",
    });

    console.log("Press releases loaded:", data);

    return data;
  } catch (error) {
    console.error("Unable to load press releases:", error);

    pressData = [];

    return [];
  }
}

/* ============================================================
   EXPORT / GLOBAL
   ============================================================ */

window.pressData = pressData;

window.fetchPressReleases = fetchPressReleases;

window.fetchPressReleaseBySlug = fetchPressReleaseBySlug;

window.loadPressReleases = loadPressReleases;

window.normalizePressRelease = normalizePressRelease;

window.normalizeImagePath = normalizeImagePath;

window.formatDate = formatDate;

window.getCategoryClass = getCategoryClass;

window.getLatestPress = getLatestPress;

window.calculateReadTime = calculateReadTime;

window.stripHtml = stripHtml;

window.limitText = limitText;

window.generateSlug = generateSlug;