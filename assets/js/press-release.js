/* ============================================================
   JAKARTA FILM COMMISSION
   PRESS RELEASE
   USER UI / DATABASE API
   ============================================================ */

/* ============================================================
   CONFIGURATION
   ============================================================ */

const PRESS_RELEASE_API = "/jfc/api/press-release/get-press.php";

const PRESS_RELEASE_DETAIL_API = "/jfc/api/press-release/get-press-detail.php";

/*
|--------------------------------------------------------------------------
| IMAGE BASE PATH
|--------------------------------------------------------------------------
|
| Database menyimpan:
|
| uploads/press-release/example.webp
|
| Browser membutuhkan:
|
| /jfc/uploads/press-release/example.webp
|
|--------------------------------------------------------------------------
*/

const PRESS_RELEASE_IMAGE_BASE = "/jfc/";

/* ============================================================
   PRESS DATA
   ============================================================ */

/*
|--------------------------------------------------------------------------
| Global data
|--------------------------------------------------------------------------
|
| Array ini menggantikan:
|
| assets/data/press-release-data.js
|
| Untuk sementara struktur data frontend dinormalisasi
| agar HTML lama tetap dapat digunakan.
|
|--------------------------------------------------------------------------
*/

let pressData = [];

/* ============================================================
   API HELPER
   ============================================================ */

/*
|--------------------------------------------------------------------------
| FETCH PRESS RELEASE LIST
|--------------------------------------------------------------------------
*/

async function fetchPressReleases(options = {}) {
  const { status = "published" } = options;

  try {
    let url = PRESS_RELEASE_API;

    /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

    if (status !== "") {
      url += "?status=" + encodeURIComponent(status);
    }

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

/* ============================================================
   FETCH PRESS RELEASE DETAIL
   ============================================================ */

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

/* ============================================================
   NORMALIZE PRESS RELEASE
   ============================================================ */

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
    |
    | get-press.php lama Anda mengembalikan content sebagai STRING.
    |
    | get-press-detail.php mengembalikan:
    |
    | content
    | content_data
    |
    | Karena itu kita support keduanya.
    |
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
    /*
        | Database identity
        */

    id: Number(item.id) || 0,

    slug: item.slug || generateSlug(item.title || ""),

    /*
        | Basic information
        */

    title: item.title || "",

    description: item.description || "",

    /*
        | Image
        */

    image: image,

    cover_image: item.cover_image || "",

    /*
        | Category
        */

    category: category,

    category_name: item.category_name || "",

    category_display: categoryDisplay,

    category_filter: item.category_filter || category,

    /*
        | Date
        */

    date: date,

    published_date: item.published_date || null,

    /*
        | Location
        */

    location: item.location || "",

    /*
        | Status
        */

    status: item.status || "",

    /*
        | Content
        */

    content: content,

    raw_content: item.content || "",

    /*
        | SEO
        */

    meta_title: item.meta_title || "",

    meta_description: item.meta_description || "",

    /*
        | Database timestamps
        */

    created_at: item.created_at || null,

    updated_at: item.updated_at || null,
  };
}

/* ============================================================
   IMAGE PATH
   ============================================================ */

/*
|--------------------------------------------------------------------------
| NORMALIZE IMAGE PATH
|--------------------------------------------------------------------------
*/

function normalizeImagePath(path) {
  if (!path) {
    return "";
  }

  let imagePath = String(path).trim();

  if (imagePath === "") {
    return "";
  }

  /*
    |--------------------------------------------------------------------------
    | Backslash
    |--------------------------------------------------------------------------
    */

  imagePath = imagePath.replace(/\\/g, "/");

  /*
    |--------------------------------------------------------------------------
    | Already absolute URL
    |--------------------------------------------------------------------------
    */

  if (/^https?:\/\//i.test(imagePath)) {
    return imagePath;
  }

  /*
    |--------------------------------------------------------------------------
    | Already starts with /jfc/
    |--------------------------------------------------------------------------
    */

  if (imagePath.indexOf("/jfc/") === 0) {
    return imagePath;
  }

  /*
    |--------------------------------------------------------------------------
    | Remove leading slash
    |--------------------------------------------------------------------------
    */

  imagePath = imagePath.replace(/^\/+/, "");

  /*
    |--------------------------------------------------------------------------
    | Return application path
    |--------------------------------------------------------------------------
    */

  return PRESS_RELEASE_IMAGE_BASE + imagePath;
}

/* ============================================================
   FORMAT DATE
   ============================================================ */

/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| CATEGORY CLASS
|--------------------------------------------------------------------------
|
| Database categories:
|
| Official Release
| Program Update
| Industry News
| Others
|
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| GET LATEST RELEASE
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| CALCULATE READ TIME
|--------------------------------------------------------------------------
*/

function calculateReadTime(content) {
  if (!Array.isArray(content) || content.length === 0) {
    return "1 min read";
  }

  let text = "";

  content.forEach((block) => {
    if (!block || typeof block !== "object") {
      return;
    }

    /*
            |--------------------------------------------------------------------------
            | Paragraph
            |--------------------------------------------------------------------------
            */

    if (block.type === "paragraph") {
      text += " " + stripHtml(block.content || "");
    } else if (typeof block.content === "string") {

    /*
            |--------------------------------------------------------------------------
            | Other text blocks
            |--------------------------------------------------------------------------
            */
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

/*
|--------------------------------------------------------------------------
| STRIP HTML
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| LIMIT TEXT
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| GENERATE SLUG
|--------------------------------------------------------------------------
|
| Digunakan sebagai fallback saja.
|
| URL detail utama tetap menggunakan slug
| dari database.
|
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| LOAD PRESS RELEASE DATA
|--------------------------------------------------------------------------
|
| Fungsi ini dapat dipanggil dari:
|
| press-release-list.html
|
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Browser global
|--------------------------------------------------------------------------
|
| Tidak menggunakan ES Module agar kompatibel dengan HTML
| Anda yang sekarang.
|
|--------------------------------------------------------------------------
*/

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
