<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Events</title>

 <link rel="stylesheet" href="css-admin.css">

</head>
<body>

    <div class="container">
    <h1>🎬 Jakarta Film Commission CMS</h1>

        <div class="admin-grid">

            <div class="card">

                <input type="hidden" id="eventId">

                <input id="title" placeholder="Title">

                <input id="category" placeholder="Category">

                <input id="event_date" type="date">

                <input id="event_time" placeholder="Time">

                <input id="location" placeholder="Location">

                <input id="address" placeholder="Address">

                <label>Event Image</label>

                <input type="file" id="imageFile" accept="image/*">

                <input type="hidden" id="image">

                <img id="imagePreview" style="width:100%; height:220px; object-fit:cover; border-radius:10px; margin-bottom:15px;">

                <textarea id="description" placeholder="Description"></textarea>

                <textarea id="content" placeholder="Content (1 paragraph per line)" rows="6"></textarea>

                <input id="map_url" placeholder="Google Maps URL">

                <h3>Event Schedule</h3>
                <div id="scheduleContainer"></div>
                <button type="button" onclick="addScheduleRow()">+ Add Schedule</button>
                <hr>

                <button id="saveBtn" class="save-btn" onclick="saveEvent()">Save Event</button>

            </div>

            <div class="card">

                <input id="searchEvent" placeholder="Search Event..." onkeyup="searchEvent()">

                <div style="display:flex; gap:15px; margin-bottom:20px;">

                    <div class="card">Total Events:<strong id="totalEvents">0</strong></div>

                </div>

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                        <tbody id="eventTable"></tbody>

                </table>

            </div>
        </div>
    </div>

<script>
loadEvents();
addScheduleRow();
let allEvents = [];

async function loadEvents(){

    const res =
    await fetch("../api/get-events.php");

    const data =
    await res.json();

    allEvents = data;

    let html = "";

    data.forEach(event=>{

        html += `
        <tr>

            <td>${event.id}</td>
            <td>${event.title}</td>
            <td>${event.category}</td>
            <td>${event.event_date}</td>

            <td>

                <button
                class="edit-btn"
                onclick="editEvent(${event.id})">
                Edit
                </button>

                <button
                class="delete-btn"
                onclick="deleteEvent(${event.id})">
                Delete
                </button>

            </td>

        </tr>
        `;
    });

    document.getElementById("eventTable")
    .innerHTML = html;

    document.getElementById(
    "totalEvents"
    ).innerText =
    data.length;
}

function editEvent(id){

    const event =
    allEvents.find(e => e.id == id);

    if(!event) return;

    document.getElementById("eventId").value =
    event.id;

    document.getElementById("title").value =
    event.title;

    document.getElementById("category").value =
    event.category;

    document.getElementById("event_date").value =
    event.event_date;

    document.getElementById("event_time").value =
    event.event_time;

    document.getElementById("location").value =
    event.location;

    document.getElementById("address").value =
    event.address;

    document.getElementById("image").value =
    event.image;

    document.getElementById("description").value =
    event.description;

    try{
        const content =
        event.content
        ? JSON.parse(event.content)
        : [];
        document.getElementById("content").value =
        content.join("\n");

    }catch(error){
        console.error(
            "Content JSON Error:",
            error
        );
        document.getElementById("content").value =
        "";
    }

    document.getElementById("map_url").value =
    event.map_url;

    document.getElementById(
    "scheduleContainer"
    ).innerHTML = "";

    try{
        const schedules =
        event.schedule
        ? JSON.parse(event.schedule)
        : [];
        schedules.forEach(item => {
            addScheduleRow(
                item.time || "",
                item.title || "",
                item.description || ""
            );
        });

    }catch(error){
        console.error(
            "Schedule JSON Error:",
            error
        );
    }

    document.getElementById("saveBtn")
    .innerText = "Update Event";

    document
    .getElementById("image")
    .value =
    event.image;

    document
    .getElementById("imagePreview")
    .src =
    "../" + event.image;
}

async function saveEvent(){

    const id =
    document.getElementById("eventId").value;

    const schedules = [];
    document
    .querySelectorAll(".schedule-row")
    .forEach(row => {
        schedules.push({

            time:
            row.querySelector(".schedule-time")
            .value,

            title:
            row.querySelector(".schedule-title")
            .value,

            description:
            row.querySelector(".schedule-description")
            .value

        });
    });

    const payload = {

        id:id,

        title:
        document.getElementById("title").value,

        category:
        document.getElementById("category").value,

        event_date:
        document.getElementById("event_date").value,

        event_time:
        document.getElementById("event_time").value,

        location:
        document.getElementById("location").value,

        address:
        document.getElementById("address").value,

        image:
        document.getElementById("image").value,

        description:
        document.getElementById("description").value,

        content:
        JSON.stringify(
            document
            .getElementById("content")
            .value
            .split("\n")
            .filter(line => line.trim() !== "")
        ),

        schedule:
        JSON.stringify(schedules),

        map_url:
        document.getElementById("map_url").value

    };

    console.log("PAYLOAD:", payload);

    const url =
    id
    ? "../api/update-event.php"
    : "../api/create-event.php";
    
    const response = await fetch(url,{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify(payload)
    });

    const result = await response.json();

    console.log(result);

    if(result.success){

        alert("Data berhasil disimpan");

        resetForm();
        loadEvents();

    }else{

        alert(result.error);

    }

    resetForm();
    loadEvents();
}

function resetForm(){

    document.getElementById("eventId").value = "";

    document.getElementById("title").value = "";
    document.getElementById("category").value = "";
    document.getElementById("event_date").value = "";
    document.getElementById("event_time").value = "";
    document.getElementById("location").value = "";
    document.getElementById("address").value = "";
    document.getElementById("image").value = "";
    document.getElementById("description").value = "";
    document.getElementById("content").value = "";
    document.getElementById("map_url").value = "";
    document.getElementById("scheduleContainer").innerHTML = "";

    document.getElementById("saveBtn").innerText =
    "Save Event";

    document.getElementById("imagePreview")
    .src = "";

    document.getElementById("imageFile")
    .value = "";
}

async function deleteEvent(id){

    if(!confirm("Delete event?"))
    return;

    await fetch(
    "../api/delete-event.php",
    {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body:JSON.stringify({id})
    });

    loadEvents();
}

function addScheduleRow(
    time = "",
    title = "",
    description = ""
){
    const container =
    document.getElementById("scheduleContainer");

    const row =
    document.createElement("div");

    row.className = "schedule-row";

    row.innerHTML = `

        <input
        type="time"
        class="schedule-time"
        value="${time}">

        <input
        class="schedule-title"
        placeholder="Title"
        value="${title}">

        <input
        class="schedule-description"
        placeholder="Description"
        value="${description}">

        <button
        type="button"
        onclick="this.parentElement.remove()">
        Delete
        </button>

    `;
    container.appendChild(row);
}

function searchEvent(){

    const keyword =
    document
    .getElementById("searchEvent")
    .value
    .toLowerCase();

    const rows =
    document.querySelectorAll(
    "#eventTable tr"
    );

    rows.forEach(row=>{

        row.style.display =
        row.innerText
        .toLowerCase()
        .includes(keyword)
        ? ""
        : "none";

    });
}

document
.getElementById("imageFile")
.addEventListener("change", async function(){

    const file = this.files[0];

    if(!file) return;

    const formData = new FormData();

    formData.append("image", file);

    try{

        const response =
        await fetch("../api/upload-image.php",{
            method:"POST",
            body:formData
        });

        const result =
        await response.json();

        console.log(result);

        if(result.success){

            document.getElementById("image").value =
            result.path;

            document.getElementById("imagePreview").src =
            "../" + result.path;

        }else{

            alert("Upload gagal");
        }

    }catch(err){

        console.error(err);
        alert("Upload error");

    }
});

</script>

</body>
</html>