let isEditMode = false;
let currentUserRole = null;


/* =========================================================
   INIT
========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    isEditMode = !!id;

    if(isEditMode){

        loadUser(id);

    }else{

        setPageTitle("New User");

        document.getElementById("passwordHint").textContent =
            "Minimum 8 characters.";

    }

});


/* =========================================================
   HELPERS
========================================================= */

function setPageTitle(title){
    const el = document.getElementById("pageTitle");
    if(el) el.textContent = title;
}

function getValue(id){
    const el = document.getElementById(id);
    return el ? el.value : "";
}

function setValue(id, value){
    const el = document.getElementById(id);
    if(el) el.value = value ?? "";
}


/* =========================================================
   LOAD USER (EDIT MODE)
========================================================= */

async function loadUser(id){

    try{

        const response =
            await fetch(API_URL + "/users/get-user.php?id=" + encodeURIComponent(id));

        const result =
            await response.json();

        if(!result.success || !result.data){
            throw new Error(result.message || "User not found.");
        }

        const user = result.data;

        currentUserRole = user.role;

        setValue("userId", user.id);
        setValue("username", user.username);
        setValue("name", user.name);
        setValue("email", user.email);
        setValue("role", user.role);

        document.getElementById("status").checked =
            user.status === "active";

        document.getElementById("password").placeholder =
            "Leave blank to keep current password";

        document.getElementById("passwordConfirm").placeholder =
            "Leave blank to keep current password";

        document.getElementById("passwordHint").textContent =
            "Leave both fields blank to keep the current password. Fill in to change it (minimum 8 characters).";

        /*
        |----------------------------------------------------------
        | SUPER ADMIN NOTICE
        |----------------------------------------------------------
        |
        | Hanya boleh ada 1 Super Admin. Kasih tahu di form supaya
        | tidak bingung kalau nanti gagal saat mencoba promosikan
        | user lain jadi Super Admin.
        |
        */

        if(user.role === "super_admin"){

            document.getElementById("roleHint").innerHTML =
                `This is the only Super Admin account. Changing its role or deactivating it is not allowed.`;

        }else{

            document.getElementById("roleHint").innerHTML =
                `Determines which admin sections this user can access. Only one Super Admin account is allowed system-wide.`;

        }

        setPageTitle("Edit User");

        document.getElementById("saveUserBtn").innerHTML =
            `<i class="ri-save-line"></i> Update User`;

    }catch(error){

        console.error("LOAD USER ERROR:", error);
        alert(error.message || "Failed to load user.");
        window.location.href = "index.php";

    }

}


/* =========================================================
   VALIDATE
========================================================= */

function validateUserForm(){

    const username = getValue("username").trim();
    const name = getValue("name").trim();
    const email = getValue("email").trim();
    const password = getValue("password");
    const passwordConfirm = getValue("passwordConfirm");
    const role = getValue("role");

    if(!username){
        return { valid: false, message: "Username is required." };
    }

    if(!/^[a-zA-Z0-9_.]{3,50}$/.test(username)){
        return { valid: false, message: "Username must be 3-50 characters (letters, numbers, dot, underscore only)." };
    }

    if(!name){
        return { valid: false, message: "Full name is required." };
    }

    if(email){
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailPattern.test(email)){
            return { valid: false, message: "Please enter a valid email address." };
        }
    }

    if(!role){
        return { valid: false, message: "Please select a role." };
    }

    if(!isEditMode){
        if(!password){
            return { valid: false, message: "Password is required." };
        }
    }

    if(password || passwordConfirm){

        if(password.length < 8){
            return { valid: false, message: "Password must be at least 8 characters." };
        }

        if(password !== passwordConfirm){
            return { valid: false, message: "Password confirmation does not match." };
        }

    }

    return { valid: true };

}


/* =========================================================
   COLLECT DATA
========================================================= */

function collectUserFormData(){

    return {
        id: getValue("userId") || null,
        username: getValue("username").trim(),
        name: getValue("name").trim(),
        email: getValue("email").trim(),
        password: getValue("password"),
        role: getValue("role"),
        status: document.getElementById("status").checked ? "active" : "inactive"
    };

}


/* =========================================================
   SAVE USER
========================================================= */

async function saveUser(){

    const validation = validateUserForm();

    if(!validation.valid){
        alert(validation.message);
        return;
    }

    const data = collectUserFormData();

    const button = document.getElementById("saveUserBtn");
    const originalHtml = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `<i class="ri-loader-4-line"></i> Saving...`;

    try{

        const endpoint =
            isEditMode
                ? API_URL + "/users/update-user.php"
                : API_URL + "/users/create-user.php";

        const response =
            await fetch(endpoint, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });

        const result =
            await response.json();

        if(!result.success){
            throw new Error(result.message || "Failed to save user.");
        }

        alert(result.message || "User successfully saved.");

        window.location.href = "index.php";

    }catch(error){

        console.error("SAVE USER ERROR:", error);
        alert(error.message || "An error occurred while saving user.");

    }finally{

        button.disabled = false;
        button.innerHTML = originalHtml;

    }

}