/* ==========================================================
   JAKARTA FILM COMMISSION
   EVENTS PAGE
========================================================== */

let allEvents = [];

let currentCategory = "All";

let visibleCount = 6;


const eventsGrid =
    document.getElementById(
        "eventsGrid"
    );

const loadMoreBtn =
    document.getElementById(
        "loadMoreBtn"
    );


/* ==========================================================
   FORMAT DATE
========================================================== */

function formatDate(dateString) {

    if (!dateString) {
        return "";
    }

    const parts =
        String(dateString).split("-");

    if (parts.length !== 3) {
        return dateString;
    }

    const year =
        Number(parts[0]);

    const month =
        Number(parts[1]) - 1;

    const day =
        Number(parts[2]);

    const date =
        new Date(
            year,
            month,
            day
        );

    return date.toLocaleDateString(
        "en-US",
        {
            day: "numeric",
            month: "short",
            year: "numeric"
        }
    );

}


/* ==========================================================
   EVENT DATE RANGE
========================================================== */

function formatEventDate(event) {

    if (!event.start_date) {
        return "";
    }

    const start =
        formatDate(
            event.start_date
        );

    if (
        event.end_date &&
        event.end_date !== event.start_date
    ) {

        const end =
            formatDate(
                event.end_date
            );

        return `${start} - ${end}`;

    }

    return start;

}


/* ==========================================================
   CATEGORY CLASS
========================================================== */

function getCategoryClass(category) {

    if (!category) {
        return "category-others";
    }

    const normalized =
        String(category)
            .toLowerCase()
            .trim()
            .replace(/\s+/g, "-")
            .replace(
                /[^a-z0-9-]/g,
                ""
            );


    switch (normalized) {

        case "nonton-di":
            return "category-nonton-di";

        case "nonton-bareng":
            return "category-nonton-bareng";

        case "jakarta-film-lab":
            return "category-jakarta-film-lab";

        default:
            return "category-others";

    }

}


/* ==========================================================
   TEXT LIMIT
========================================================== */

function truncateText(
    text,
    maxLength
) {

    if (!text) {
        return "";
    }

    text =
        String(text)
            .replace(/\s+/g, " ")
            .trim();

    if (
        text.length <= maxLength
    ) {

        return text;

    }

    return (
        text
            .substring(
                0,
                maxLength
            )
            .trim()
            .replace(
                /\s+\S*$/,
                ""
            )
        + "..."
    );

}


/* ==========================================================
   ESCAPE HTML
========================================================== */

function escapeHtml(value) {

    if (
        value === null ||
        value === undefined
    ) {

        return "";

    }

    return String(value)
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );

}


/* ==========================================================
   GET CATEGORY DISPLAY
========================================================== */

function getCategoryDisplay(event) {

    /*
    |--------------------------------------------------------------------------
    | API baru
    |--------------------------------------------------------------------------
    */

    if (
        event.category_display &&
        String(
            event.category_display
        ).trim()
    ) {

        return String(
            event.category_display
        ).trim();

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    |
    | Untuk data lama / API lama.
    |--------------------------------------------------------------------------
    */

    if (
        String(event.category || "")
            .trim() === "Others" &&
        event.category_name
    ) {

        return String(
            event.category_name
        ).trim();

    }


    return (
        event.category ||
        "Others"
    );

}


/* ==========================================================
   GET CATEGORY FILTER
========================================================== */

function getCategoryFilter(event) {

    /*
    |--------------------------------------------------------------------------
    | API baru
    |--------------------------------------------------------------------------
    */

    if (
        event.category_filter
    ) {

        return String(
            event.category_filter
        ).trim();

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    */

    return String(
        event.category || "Others"
    ).trim();

}


/* ==========================================================
   LOAD EVENTS
========================================================== */

async function loadEvents() {

    try {

        const response =
            await fetch(
                "api/events/get-events.php"
            );


        if (!response.ok) {

            throw new Error(
                "Failed to load events"
            );

        }


        const data =
            await response.json();


        console.log(
            "EVENTS:",
            data
        );


        if (
            !Array.isArray(data)
        ) {

            throw new Error(
                "Invalid events data"
            );

        }


        allEvents = data;


        renderEvents();

    }
    catch (error) {

        console.error(
            "Events API:",
            error
        );


        eventsGrid.innerHTML = `

            <div class="event-empty">

                <h3>
                    Unable to load events
                </h3>

                <p>
                    Please try again later.
                </p>

            </div>

        `;


        loadMoreBtn.style.display =
            "none";

    }

}


/* ==========================================================
   RENDER EVENTS
========================================================== */

function renderEvents() {


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    const filteredEvents =

        currentCategory === "All"

        ? allEvents

        : allEvents.filter(
            event =>
                getCategoryFilter(
                    event
                ) === currentCategory
        );


    /*
    |--------------------------------------------------------------------------
    | VISIBLE
    |--------------------------------------------------------------------------
    */

    const visibleEvents =
        filteredEvents.slice(
            0,
            visibleCount
        );


    eventsGrid.innerHTML =
        "";


    /*
    |--------------------------------------------------------------------------
    | EMPTY
    |--------------------------------------------------------------------------
    */

    if (
        !visibleEvents.length
    ) {

        eventsGrid.innerHTML = `

            <div class="event-empty">

                <h3>
                    No events found
                </h3>

                <p>
                    There are no events in this category.
                </p>

            </div>

        `;


        loadMoreBtn.style.display =
            "none";

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    visibleEvents.forEach(
        event => {


            const card =
                document.createElement(
                    "article"
                );


            /*
            |--------------------------------------------------------------
            | CATEGORY TYPE
            |--------------------------------------------------------------
            */

            const categoryFilter =
                getCategoryFilter(
                    event
                );


            const categoryDisplay =
                getCategoryDisplay(
                    event
                );


            const categoryClass =
                getCategoryClass(
                    categoryFilter
                );


            card.className =
                `event-card ${categoryClass}`;


            /*
            |--------------------------------------------------------------
            | TEXT
            |--------------------------------------------------------------
            */

            const title =
                truncateText(
                    event.title ||
                    "Untitled Event",
                    65
                );


            const location =
                truncateText(
                    event.location ||
                    "Location not specified",
                    35
                );


            const description =
                truncateText(
                    event.description ||
                    "",
                    125
                );


            const category =
                truncateText(
                    categoryDisplay,
                    25
                );


            const date =
                formatEventDate(
                    event
                );


            /*
            |--------------------------------------------------------------
            | CARD
            |--------------------------------------------------------------
            */

            card.innerHTML = `

                <div class="event-card-image">

                    <img
                        src="${escapeHtml(
                            event.cover_image || ""
                        )}"
                        alt="${escapeHtml(
                            event.title ||
                            "Event"
                        )}"
                        loading="lazy"
                    >

                    <span class="event-card-category">
                        ${escapeHtml(category)}
                    </span>

                </div>


                <div class="event-card-content">


                    <div class="event-card-date">

                        <span class="event-date-icon">

                            <img
                                src="assets/icon/date.png"
                                alt="Date"
                            >

                        </span>

                        <span>
                            ${escapeHtml(date)}
                        </span>

                    </div>


                    <h3 class="event-card-title">

                        ${escapeHtml(title)}

                    </h3>


                    <div class="event-card-location">

                        <span class="location-icon">

                            <img
                                src="assets/icon/pin.png"
                                alt="Location"
                            >

                        </span>

                        <span>
                            ${escapeHtml(location)}
                        </span>

                    </div>


                    <p class="event-card-description">

                        ${escapeHtml(description)}

                    </p>


                    <div class="event-card-footer">

                        <a
                            href="event-detail.html?slug=${encodeURIComponent(
                                event.slug || ""
                            )}"
                            class="event-card-button"
                        >

                            <span>
                                View Event
                            </span>

                            <strong>
                                →
                            </strong>

                        </a>

                    </div>


                </div>

            `;


            eventsGrid.appendChild(
                card
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD MORE
    |--------------------------------------------------------------------------
    */

    if (
        visibleCount <
        filteredEvents.length
    ) {

        loadMoreBtn.style.display =
            "inline-flex";

    }
    else {

        loadMoreBtn.style.display =
            "none";

    }

}


/* ==========================================================
   FILTER BUTTON
========================================================== */

document
    .querySelectorAll(".filter-btn")
    .forEach(
        button => {

            button.addEventListener(
                "click",
                function () {


                    document
                        .querySelectorAll(
                            ".filter-btn"
                        )
                        .forEach(
                            btn => {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                    this.classList.add(
                        "active"
                    );


                    currentCategory =
                        this.dataset.category;


                    visibleCount = 6;


                    renderEvents();

                }
            );

        }
    );


/* ==========================================================
   LOAD MORE
========================================================== */

if (loadMoreBtn) {

    loadMoreBtn.addEventListener(
        "click",
        function () {

            visibleCount += 6;

            renderEvents();

        }
    );

}


/* ==========================================================
   INITIALIZE
========================================================== */

loadEvents();