let allEvents = [];

let currentStatusFilter = "";


/* =========================================================
   INIT
========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    () => {

        loadEvents();

    }
);


/* =========================================================
   DEBUG
========================================================= */

console.log(
    "API_URL =",
    API_URL
);

console.log(
    "UPLOAD_URL =",
    UPLOAD_URL
);


/* =========================================================
   LOAD EVENTS
========================================================= */
async function loadEvents(){

    try{

        const url =
            API_URL + "/events/get-admin-events.php";

        console.log(
            "ADMIN EVENTS API:",
            url
        );

        const res =
            await fetch(url);

        console.log(
            "RESPONSE STATUS:",
            res.status
        );

        if(!res.ok){

            throw new Error(
                "HTTP Error " + res.status
            );

        }

        const result =
            await res.json();

        console.log(
            "ADMIN EVENTS DATA:",
            result
        );

        console.log(
            "FIRST EVENT STATUS:",
            result.data?.[0]?.status
        );


        if(!result.success){

            throw new Error(
                result.message ||
                "Failed to load events"
            );

        }


        allEvents =
            Array.isArray(result.data)
                ? result.data
                : [];


        renderTable(allEvents);


    }catch(error){

        console.error(
            "LOAD EVENTS ERROR:",
            error
        );

        const tbody =
            document.getElementById("eventTable");

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;padding:30px;"
                >

                    Failed to load events.

                </td>

            </tr>

        `;

    }

}


/* =========================================================
   FILTER EVENTS
========================================================= */

function filterEvents(){

    const keyword =
        document
            .getElementById("searchEvent")
            .value
            .toLowerCase()
            .trim();


    const status =
        document
            .getElementById("statusFilter")
            .value;


    currentStatusFilter =
        status;


    const filtered =
        allEvents.filter(event => {


            const title =
                String(
                    event.title || ""
                )
                .toLowerCase();


            const category =
                String(
                    event.category || ""
                )
                .toLowerCase();


            const location =
                String(
                    event.location || ""
                )
                .toLowerCase();


            const matchesKeyword =
                title.includes(keyword) ||
                category.includes(keyword) ||
                location.includes(keyword);


            const matchesStatus =
                !status ||
                String(
                    event.status || ""
                ).toLowerCase() === status;


            return (
                matchesKeyword &&
                matchesStatus
            );

        });


    renderTable(filtered);

}


/* =========================================================
   SEARCH
========================================================= */

function searchEvent(){

    filterEvents();

}


/* =========================================================
   STATUS BADGE
========================================================= */

function getStatusBadge(status){

    const normalized =
        String(
            status || "draft"
        )
        .toLowerCase();


    switch(normalized){

        case "published":

            return `
                <span class="event-status-badge published">
                    Published
                </span>
            `;


        case "archived":

            return `
                <span class="event-status-badge archived">
                    Archived
                </span>
            `;


        case "draft":

        default:

            return `
                <span class="event-status-badge draft">
                    Draft
                </span>
            `;

    }

}


/* =========================================================
   CATEGORY
========================================================= */

function getCategoryClass(category){

    const normalized =
        String(
            category || "Others"
        )
        .toLowerCase()
        .trim()
        .replace(
            /\s+/g,
            "-"
        )
        .replace(
            /[^a-z0-9-]/g,
            ""
        );


    switch(normalized){

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


/* =========================================================
   RENDER TABLE
========================================================= */
function renderTable(data){

    const tbody =
        document.getElementById("eventTable");

    tbody.innerHTML = "";


    if(!Array.isArray(data) || data.length === 0){

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    style="
                        text-align:center;
                        padding:40px;
                    "
                >

                    No events found.

                </td>

            </tr>

        `;

        return;

    }


    data.forEach(event => {


        /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

        let image = "";

        if(event.cover_image){

            image =
                event.cover_image
                    .replace("uploads/", "");

        }


        const imageHTML = image

            ? `
                <img
                    src="${UPLOAD_URL}/${image}"
                    class="table-thumb"
                    alt="${event.title || "Event"}"
                >
            `

            : `
                <div
                    class="table-thumb"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#eee;
                    "
                >
                    —
                </div>
            `;


        /* =========================================================
        STATUS
        ========================================================= */

        const status =
            String(
                event.status || "draft"
            )
            .toLowerCase()
            .trim();


        let statusClass = "status-draft";

        let statusLabel = "Draft";


        switch(status){

            case "published":

                statusClass =
                    "status-published";

                statusLabel =
                    "Published";

                break;


            case "draft":

                statusClass =
                    "status-draft";

                statusLabel =
                    "Draft";

                break;


            case "archived":

                statusClass =
                    "status-archived";

                statusLabel =
                    "Archived";

                break;


            default:

                statusClass =
                    "status-draft";

                statusLabel =
                    "Draft";

                break;

        }      

        /*
        |--------------------------------------------------------------------------
        | ROW
        |--------------------------------------------------------------------------
        */

        tbody.innerHTML += `

            <tr>

                <td>

                    ${imageHTML}

                </td>


                <td>

                    <strong>
                        ${event.title || "Untitled Event"}
                    </strong>

                </td>


                <td>

                    ${event.category || "Others"}

                </td>


                <td>

                    ${event.start_date || "TBA"}

                </td>


                <td>

                    ${event.location || "TBA"}

                </td>


                <td>

                    <span class="status-badge ${statusClass}">

                        ${statusLabel}

                    </span>

                </td>


                <td>

                    <div class="table-action">

                        <a
                            href="form.php?id=${event.id}"
                            class="table-btn edit"
                        >

                            Edit

                        </a>


                        <button
                            class="table-btn delete"
                            onclick="deleteEvent(${event.id})"
                        >

                            Delete

                        </button>

                    </div>

                </td>

            </tr>

        `;

    });

}


/* =========================================================
   DELETE EVENT
========================================================= */

async function deleteEvent(id){

    if(
        !confirm(
            "Delete Event?"
        )
    ){

        return;

    }


    try{

        const response =
            await fetch(
                API_URL +
                "/events/delete-event.php",
                {

                    method:"POST",

                    headers:{
                        "Content-Type":
                            "application/json"
                    },

                    body:
                        JSON.stringify({
                            id:id
                        })

                }
            );


        const result =
            await response.json();


        if(!result.success){

            alert(
                result.message ||
                "Failed to delete event."
            );

            return;

        }


        await loadEvents();


    }catch(error){

        console.error(
            "DELETE EVENT ERROR:",
            error
        );


        alert(
            "Failed to delete event."
        );

    }

}