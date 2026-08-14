const Dashboard = {

    /* =====================================================
       INIT
    ===================================================== */

    async init(){

        try{

            this.renderStats();

            await this.loadEvents();

        }catch(error){

            console.error(
                "DASHBOARD ERROR:",
                error
            );

        }

    },


    /* =====================================================
       STATISTICS
    ===================================================== */

    renderStats(){

        /*
        |--------------------------------------------------------------------------
        | TOTAL ARTICLES
        |--------------------------------------------------------------------------
        */

        const totalArticles =
            document.getElementById(
                "totalArticles"
            );


        if(totalArticles){

            /*
            | Existing press-release system
            | can update this later.
            */

            if(typeof allArticles !== "undefined"){

                totalArticles.textContent =
                    allArticles.length;

            }

        }

    },


    /* =====================================================
       LOAD EVENTS
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


            /* =============================================
               FEATURED EVENT
            ============================================= */

            this.renderFeaturedEvent(
                data.featuredEvent
            );


        }catch(error){

            console.error(
                "DASHBOARD EVENT ERROR:",
                error
            );


            this.renderEventError();

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
    ===================================================== */

    renderFeaturedEvent(event){

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
       ERROR
    ===================================================== */

    renderEventError(){

        const latest =
            document.getElementById(
                "latestEvents"
            );


        const featured =
            document.getElementById(
                "featuredEvent"
            );


        if(latest){

            latest.innerHTML = `

                <div class="dashboard-empty">

                    Failed to load events.

                </div>

            `;

        }


        if(featured){

            featured.innerHTML = `

                <div class="dashboard-empty">

                    Failed to load featured event.

                </div>

            `;

        }

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
   START
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        Dashboard.init();

    }
);