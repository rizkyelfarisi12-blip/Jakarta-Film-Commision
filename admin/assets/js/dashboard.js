const Dashboard = {

    /* =====================================================
       CONFIG

       Sesuaikan path ini kalau lokasi file API Anda
       berbeda dari asumsi di sini.
    ===================================================== */

    FEATURED_EVENT_API: "../api/events/get-featured-event.php",

    PRESS_RELEASE_API: "../api/press-release/get-press.php",


    /* =====================================================
       INIT
    ===================================================== */

    async init(){

        try{

            await this.loadEvents();

            await this.loadFeaturedEvent();

            await this.loadPressReleases();

        }catch(error){

            console.error(
                "DASHBOARD ERROR:",
                error
            );

        }

    },


    /* =====================================================
       LOAD EVENTS (TOTAL + LATEST)
    ===================================================== */

    async loadEvents(){

        try{

            const response =
                await fetch(
                    "../api/events/dashboard-events.php"
                );


            const raw =
                await response.text();


            console.log(
                "DASHBOARD EVENT RAW:",
                raw
            );


            let data;


            try{

                data =
                    JSON.parse(raw);

            }catch(error){

                console.error(
                    "Dashboard API bukan JSON:",
                    raw
                );

                throw new Error(
                    "API dashboard-events.php mengembalikan response yang bukan JSON."
                );

            }


            if(!data.success){

                throw new Error(
                    data.message ||
                    data.error ||
                    "Gagal mengambil data event."
                );

            }


            /* =============================================
               TOTAL EVENTS
            ============================================= */

            const totalEvents =
                document.getElementById(
                    "totalEvents"
                );


            if(totalEvents){

                totalEvents.textContent =
                    data.totalEvents ?? 0;

            }


            /* =============================================
               LATEST EVENTS
            ============================================= */

            this.renderLatestEvents(
                data.latestEvents || []
            );


        }catch(error){

            console.error(
                "DASHBOARD EVENT ERROR:",
                error
            );


            const latest =
                document.getElementById(
                    "latestEvents"
                );


            if(latest){

                latest.innerHTML = `

                    <div class="dashboard-empty">

                        Failed to load events.

                    </div>

                `;

            }

        }

    },


    /* =====================================================
       LOAD FEATURED EVENT

       Sumber datanya API terpisah (get-featured-event.php),
       bukan dari dashboard-events.php. API ini mengembalikan
       salah satu dari:

       - { status: "success", type: "featured", event: {...} }
       - { status: "success", type: "upcoming", event: {...} }
       - { status: "empty" }
    ===================================================== */

    async loadFeaturedEvent(){

        try{

            const response =
                await fetch(
                    this.FEATURED_EVENT_API
                );


            const raw =
                await response.text();


            console.log(
                "FEATURED EVENT RAW:",
                raw
            );


            let data;


            try{

                data =
                    JSON.parse(raw);

            }catch(error){

                console.error(
                    "Featured event API bukan JSON:",
                    raw
                );

                throw new Error(
                    "API featured event mengembalikan response yang bukan JSON."
                );

            }


            if(
                data.status !== "success" ||
                !data.event
            ){

                this.renderFeaturedEvent(
                    null
                );

                return;

            }


            this.renderFeaturedEvent(
                data.event,
                data.type
            );


        }catch(error){

            console.error(
                "FEATURED EVENT ERROR:",
                error
            );


            const featured =
                document.getElementById(
                    "featuredEvent"
                );


            if(featured){

                featured.innerHTML = `

                    <div class="dashboard-empty">

                        Failed to load featured event.

                    </div>

                `;

            }

        }

    },


    /* =====================================================
       LOAD PRESS RELEASES

       Mengisi stat "Total Articles" dan tabel
       "Latest Press Release" di dashboard, memakai
       endpoint get-press.php (sama dengan yang dipakai
       halaman Press Release Management).
    ===================================================== */

    async loadPressReleases(){

        const totalArticles =
            document.getElementById(
                "totalArticles"
            );

        const recentTable =
            document.getElementById(
                "recentArticles"
            );

        try{

            const response =
                await fetch(
                    this.PRESS_RELEASE_API
                );


            const raw =
                await response.text();


            console.log(
                "PRESS RELEASE RAW:",
                raw
            );


            let data;


            try{

                data =
                    JSON.parse(raw);

            }catch(error){

                console.error(
                    "Press release API bukan JSON:",
                    raw
                );

                throw new Error(
                    "API get-press.php mengembalikan response yang bukan JSON."
                );

            }


            if(!data.success){

                throw new Error(
                    data.message ||
                    "Gagal mengambil data press release."
                );

            }


            /* =============================================
               TOTAL ARTICLES
            ============================================= */

            if(totalArticles){

                totalArticles.textContent =
                    data.data?.total ?? 0;

            }


            /* =============================================
               LATEST PRESS RELEASES

               get-press.php sudah mengurutkan berdasarkan
               published_date DESC, created_at DESC, id DESC
               jadi tinggal ambil beberapa item teratas.
            ============================================= */

            const items =
                Array.isArray(data.data?.items)
                    ? data.data.items
                    : [];


            this.renderRecentArticles(
                items.slice(0, 5)
            );


        }catch(error){

            console.error(
                "PRESS RELEASE ERROR:",
                error
            );


            if(totalArticles){

                totalArticles.textContent =
                    "0";

            }


            if(recentTable){

                recentTable.innerHTML = `

                    <tr>

                        <td
                            colspan="5"
                            style="text-align:center;padding:30px;"
                        >

                            Failed to load press releases.

                        </td>

                    </tr>

                `;

            }

        }

    },


    /* =====================================================
       LATEST EVENTS
    ===================================================== */

    renderLatestEvents(events){

        const container =
            document.getElementById(
                "latestEvents"
            );


        if(!container){

            return;

        }


        if(!events.length){

            container.innerHTML = `

                <div class="dashboard-empty">

                    No events have been added yet.

                </div>

            `;

            return;

        }


        container.innerHTML =
            events.slice(0, 3).map(item => {


                /* =========================================
                IMAGE
                ========================================= */

                const image =
                    item.cover_image
                        ? ROOT_PATH + "/" + item.cover_image
                        : "";


                /* =========================================
                DESCRIPTION
                ========================================= */

                const description =
                    typeof truncateWords === "function"

                        ?

                        truncateWords(
                            item.description || "",
                            16
                        )

                        :

                        (
                            item.description || ""
                        );


                /* =========================================
                CATEGORY NAME
                ========================================= */

                let categoryName =
                    item.category || "-";


                if(
                    item.category === "Others" &&
                    item.category_name
                ){

                    categoryName =
                        item.category_name;

                }


                /* =========================================
                CATEGORY CSS
                ========================================= */

                const categoryClass =
                    slugifyCategory(
                        item.category || ""
                    );


                /* =========================================
                STATUS
                ========================================= */

                const status =
                    item.status || "draft";


                /* =========================================
                RETURN
                ========================================= */

                return `

                    <a
                        href="events/form.php?id=${item.id}"
                        class="latest-event-item"
                    >

                        ${
                            image

                            ?

                            `
                            <img
                                src="${image}"
                                class="latest-event-image"
                                alt="${escapeHtml(
                                    item.title || "Event"
                                )}"
                            >
                            `

                            :

                            `
                            <div
                                class="latest-event-image"
                                aria-label="No image">
                            </div>
                            `
                        }


                        <div class="latest-event-content">


                            <div class="latest-event-title">

                                ${escapeHtml(
                                    item.title || "-"
                                )}

                            </div>


                            <div class="latest-event-description">

                                ${escapeHtml(
                                    description
                                )}

                            </div>


                            <div class="latest-event-meta">


                                <span
                                    class="
                                        event-category-badge
                                        category-${categoryClass}
                                    "
                                >

                                    ${escapeHtml(
                                        categoryName
                                    )}

                                </span>


                                <span
                                    class="
                                        status-badge
                                        status-${status}
                                    "
                                >

                                    ${escapeHtml(
                                        status
                                    )}

                                </span>


                            </div>


                        </div>


                    </a>

                `;

            }).join("");

    },


    /* =====================================================
       FEATURED EVENT

       "type" menandakan apakah event ini memang sedang
       di-featured secara manual ("featured"), atau ini
       cuma fallback event terdekat karena tidak ada yang
       di-featured ("upcoming").
    ===================================================== */

    renderFeaturedEvent(event, type = "featured"){

        const container =
            document.getElementById(
                "featuredEvent"
            );


        if(!container){

            return;

        }


        if(!event){

            container.innerHTML = `

                <div class="dashboard-empty">

                    No featured event is currently active.

                </div>

            `;

            return;

        }


        const image =
            event.cover_image
                ? ROOT_PATH + "/" + event.cover_image
                : "";


        const description =
            typeof truncateWords === "function"
                ? truncateWords(
                    event.description || "",
                    16
                )
                : (
                    event.description || ""
                );


        const category =
            event.category === "Others"
                ? (
                    event.category_name ||
                    "Others"
                )
                : (
                    event.category ||
                    "-"
                );


        const stateLabel =
            type === "upcoming"
                ? "Upcoming (auto)"
                : "Featured";


        container.innerHTML = `

            <a
                href="events/form.php?id=${event.id}"
                class="featured-event-card"
            >

                ${
                    image

                    ?

                    `
                    <img
                        src="${image}"
                        class="featured-event-image"
                        alt="${escapeHtml(
                            event.title || "Featured Event"
                        )}"
                    >
                    `

                    :

                    ""
                }


                <div class="featured-event-overlay">

                    <div class="featured-event-title">

                        ${escapeHtml(
                            event.title || "-"
                        )}

                    </div>


                    <div class="featured-event-description">

                        ${escapeHtml(
                            description
                        )}

                    </div>


                    <div class="featured-event-meta">

                        <span class="featured-event-badge">

                            ${escapeHtml(
                                stateLabel
                            )}

                        </span>


                        <span class="featured-event-badge">

                            ${escapeHtml(
                                category
                            )}

                        </span>


                        ${
                            event.location

                            ?

                            `
                            <span class="featured-event-badge">

                                ${escapeHtml(
                                    event.location
                                )}

                            </span>
                            `

                            :

                            ""
                        }

                    </div>

                </div>

            </a>

        `;

    },


    /* =====================================================
       RECENT PRESS RELEASES (TABLE)
    ===================================================== */

    renderRecentArticles(items){

        const container =
            document.getElementById(
                "recentArticles"
            );


        if(!container){

            return;

        }


        if(!items.length){

            container.innerHTML = `

                <tr>

                    <td
                        colspan="5"
                        style="text-align:center;padding:30px;"
                    >

                        No press releases have been added yet.

                    </td>

                </tr>

            `;

            return;

        }


        container.innerHTML =
            items.map(item => {


                /* =========================================
                IMAGE
                ========================================= */

                const imageUrl =
                    normalizePressReleaseImage(
                        item.cover_image
                    );


                /* =========================================
                CATEGORY
                ========================================= */

                const category =
                    item.category_display ||
                    item.category ||
                    "-";


                const categorySlug =
                    getPressReleaseCategoryClass(
                        item.category_filter ||
                        item.category
                    );


                /* =========================================
                STATUS
                ========================================= */

                const status =
                    String(
                        item.status || "draft"
                    ).toLowerCase();


                const statusClass =
                    status === "published"
                        ? "status-published"
                        : "status-draft";


                const statusLabel =
                    status === "published"
                        ? "Published"
                        : "Draft";


                /* =========================================
                DATE
                ========================================= */

                const date =
                    formatPressReleaseDate(
                        item.published_date ||
                        item.created_at
                    );


                /* =========================================
                RETURN ROW
                ========================================= */

                return `

                    <tr>

                        <td>

                            ${
                                imageUrl

                                ?

                                `
                                <img
                                    src="${imageUrl}"
                                    class="table-thumb"
                                    alt="${escapeHtml(
                                        item.title || "Press Release"
                                    )}"
                                    loading="lazy"
                                    onerror="this.style.display='none';"
                                >
                                `

                                :

                                `
                                <div class="table-thumb-placeholder">
                                    No Image
                                </div>
                                `
                            }

                        </td>


                        <td>

                            <strong>

                                ${escapeHtml(
                                    item.title || "-"
                                )}

                            </strong>

                        </td>


                        <td>

                            <span class="press-badge ${categorySlug}">

                                ${escapeHtml(category)}

                            </span>

                        </td>


                        <td>

                            ${escapeHtml(date)}

                        </td>


                        <td>

                            <div class="table-action">

                                <span class="status-badge ${statusClass}">

                                    ${statusLabel}

                                </span>


                                <a
                                    href="press-release/form.php?id=${item.id}"
                                    class="table-btn edit"
                                >

                                    Edit

                                </a>

                            </div>

                        </td>

                    </tr>

                `;

            }).join("");

    }

};


/* =========================================================
   HELPERS
========================================================= */

function escapeHtml(value = ""){

    return String(value)

        .replace(/&/g, "&amp;")

        .replace(/</g, "&lt;")

        .replace(/>/g, "&gt;")

        .replace(/"/g, "&quot;")

        .replace(/'/g, "&#039;");

}


function slugifyCategory(value = ""){

    return String(value)

        .toLowerCase()

        .trim()

        .replace(/[^a-z0-9]+/g, "-")

        .replace(/^-|-$/g, "");

}


/* =========================================================
   PRESS RELEASE CATEGORY COLOR CLASS

   Dipetakan manual (bukan slugify otomatis) supaya cocok
   dengan class warna yang sudah dipakai di tampilan user:

   Industry News    -> .industry
   Official Release -> .official-release
   Program Update   -> .program
   Others           -> .others
========================================================= */

function getPressReleaseCategoryClass(category){

    const normalized =
        String(category || "")
            .toLowerCase()
            .trim();


    switch(normalized){

        case "industry news":

            return "industry";


        case "official release":

            return "official-release";


        case "program update":

            return "program";


        default:

            return "others";

    }

}


/* =========================================================
   TRUNCATE WORDS

   Membatasi deskripsi pada card event (Latest Events &
   Featured Event) supaya tidak terlalu panjang. Fungsi ini
   sebelumnya dipanggil di renderLatestEvents() dan
   renderFeaturedEvent() tapi belum pernah didefinisikan,
   jadi truncation-nya tidak pernah benar-benar berjalan.
========================================================= */

function truncateWords(text, maxWords = 16){

    if(!text){

        return "";

    }


    const words =
        String(text)
            .replace(/\s+/g, " ")
            .trim()
            .split(" ");


    if(words.length <= maxWords){

        return words.join(" ");

    }


    return words.slice(0, maxWords).join(" ") + "...";

}


/* =========================================================
   PRESS RELEASE IMAGE PATH

   Sama seperti normalizeImageUrl() di press-release.js,
   diberi nama berbeda supaya tidak bentrok kalau
   press-release.js ikut ter-load di halaman yang sama.
========================================================= */

function normalizePressReleaseImage(path){

    let value =
        String(path || "").trim();


    if(!value){

        return "";

    }


    value =
        value.replace(/\\/g, "/");


    if(/^https?:\/\//i.test(value)){

        return value;

    }


    if(value.startsWith("/jfc/")){

        return value;

    }


    value =
        value.replace(/^\/+/, "");


    if(value.startsWith("uploads/press-release/")){

        return "/jfc/" + value;

    }


    if(value.startsWith("assets/uploads/press-release/")){

        return "/jfc/" + value;

    }


    return "/jfc/" + value;

}


/* =========================================================
   PRESS RELEASE DATE FORMAT

   Sama seperti formatDate() di press-release.js, diberi
   nama berbeda untuk menghindari bentrok nama fungsi.
========================================================= */

function formatPressReleaseDate(value){

    if(!value){

        return "-";

    }


    const stringValue =
        String(value).trim();


    const dateOnlyMatch =
        stringValue.match(
            /^(\d{4})-(\d{2})-(\d{2})$/
        );


    if(dateOnlyMatch){

        const year = dateOnlyMatch[1];

        const month = dateOnlyMatch[2];

        const day = dateOnlyMatch[3];


        const date =
            new Date(
                Number(year),
                Number(month) - 1,
                Number(day)
            );


        if(!Number.isNaN(date.getTime())){

            return date.toLocaleDateString(
                "en-GB",
                {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                }
            );

        }

    }


    const date =
        new Date(stringValue);


    if(!Number.isNaN(date.getTime())){

        return date.toLocaleDateString(
            "en-GB",
            {
                day: "2-digit",
                month: "short",
                year: "numeric"
            }
        );

    }


    return escapeHtml(stringValue);

}


/* =========================================================
   START
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        Dashboard.init();

    }
);