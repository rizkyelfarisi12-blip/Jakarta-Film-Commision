
        /*==========================================================
              JAKARTA FILM COMMISSION
              MEMBER REGISTRATION
        ==========================================================*/
        const MemberRegistration = {
            form: null,
            photoInput: null,
            dropArea: null,
            previewImage: null,
            uploadPlaceholder: null,
            draftKey: "jfc-individual-member",
            cropper:null,

            init() {
                this.form = document.getElementById("memberForm");

                this.photoInput = document.getElementById("photo");

                this.dropArea = document.getElementById("dropArea");

                this.previewImage = document.getElementById("previewImage");

                this.cropModal=document.getElementById("cropModal");

                this.cropImage=document.getElementById("cropImage");

                this.saveCrop=document.getElementById("saveCrop");

                this.cancelCrop=document.getElementById("cancelCrop");

                this.previewBtn=document.getElementById("previewBtn");

                this.previewModal=document.getElementById("previewModal");

                this.closePreview=document.getElementById("closePreview");

                this.backToEdit=document.getElementById("backToEdit");

                this.uploadPlaceholder = this.dropArea.querySelector(
                    ".upload-placeholder",
                );

                this.previewBtn.addEventListener("click",()=>{
                    if(!this.validate()) return;
                    this.showPhotoCrop();
                });
                
                this.closePreview.addEventListener("click",()=>{
                    this.previewModal.style.display="none";
                });

                this.backToEdit.addEventListener("click",()=>{
                    this.previewModal.style.display="none";
                });

                this.previewModal.addEventListener("click",(e)=>{
                    if(e.target===this.previewModal){
                        this.previewModal.style.display="none";
                    }
                });

                this.bindUpload();

                this.restoreDraft();

                this.autoSave();

                this.liveValidation();

            },

            /*==============================================
                UPLOAD PHOTO
            ==============================================*/
            bindUpload() {
                this.dropArea.addEventListener("click", () => {
                    this.photoInput.click();
                });

                this.photoInput.addEventListener("change", (e) => {
                    const file = e.target.files[0];

                    if (!file) return;

                    this.showPhotoCrop(file);
                });

                this.dropArea.addEventListener("dragover", (e) => {
                    e.preventDefault();

                    this.dropArea.classList.add("dragging");
                });

                this.dropArea.addEventListener("dragleave", () => {
                    this.dropArea.classList.remove("dragging");
                });

                this.dropArea.addEventListener("drop", (e) => {
                    e.preventDefault();
                    this.dropArea.classList.remove("dragging");
                    const file = e.dataTransfer.files[0];
                    if (!file) return;
                    this.setFile(file);
                    this.showPhotoCrop(file);
                });

                this.saveCrop.addEventListener("click",()=>{
                    const canvas=this.cropper.getCroppedCanvas({
                        width:800,
                        height:1000
                    });

                    canvas.toBlob(blob=>{

                        const file=new File(
                            [blob],
                            "profile.jpg",
                            {
                                type:"image/jpeg"
                            }
                        );

                        this.setFile(file);

                        this.previewImage.src=URL.createObjectURL(file);

                        this.previewImage.style.display="block";

                        this.dropArea.classList.add("has-image");

                    },"image/jpeg",0.9);
                });
                
                this.cancelCrop.addEventListener("click",()=>{

                    this.cropModal.style.display="none";

                    this.photoInput.value="";

                    if(this.cropper){

                        this.cropper.destroy();

                        this.cropper=null;

                    }

                });
            },

            setFile(file) {
                const dt = new DataTransfer();

                dt.items.add(file);

                this.photoInput.files = dt.files;
            },

            showPhotoCrop(file){
                const reader=new FileReader();
                reader.onload=(e)=>{

                    this.cropImage.src=e.target.result;

                    this.cropModal.style.display="flex";

                    if(this.cropper){
                        this.cropper.destroy();
                    }

                    this.cropper=new Cropper(
                        this.cropImage,
                        {
                            aspectRatio:4/5,
                            viewMode:1,
                            autoCropArea:1,
                            movable:true,
                            zoomable:true,
                            scalable:false,
                            rotatable:false
                        }
                    );

                }
                reader.readAsDataURL(file);
            },

            /*==============================================
                SAVE DRAFT
            ==============================================*/
            autoSave() {
                this.form.addEventListener("input", () => {
                    this.saveDraft();
                });

                this.form.addEventListener("change", () => {
                    this.saveDraft();
                });
            },

            saveDraft() {
                const draft = {};

                this.form.querySelectorAll("input, textarea, select").forEach((input) => {
                    switch (input.type) {
                        case "checkbox":
                            if (!draft[input.name]) {
                                draft[input.name] = [];
                            }

                            if (input.checked) {
                                draft[input.name].push(input.value);
                            }

                            break;

                        case "file":
                            break;

                        default:
                            draft[input.name] = input.value;

                            break;
                    }
                });

                localStorage.setItem(
                    this.draftKey,

                    JSON.stringify(draft),
                );
            },

            /*==============================================
                RESTORE DRAFT
            ==============================================*/
            restoreDraft() {
                const draft = localStorage.getItem(this.draftKey);

                if (!draft) return;

                const data = JSON.parse(draft);

                this.form.querySelectorAll("input, textarea, select").forEach((input) => {
                    switch (input.type) {
                        case "checkbox":
                            if (
                                data[input.name] &&
                                data[input.name].includes(input.value)
                            ) {
                                input.checked = true;
                            }

                            break;

                        case "file":
                            break;

                        default:
                            if (data[input.name]) {
                                input.value = data[input.name];
                            }

                            break;
                    }
                });
            },

            /*======================================
            CLEAR ERROR
            ======================================*/
            clearErrors(){
                this.form
                    .querySelectorAll(".error-text")
                    .forEach(el=>{
                        el.style.display="none";
                    });

                this.form
                    .querySelectorAll(".error-field")
                    .forEach(el=>{
                        el.classList.remove("error-field");
                    });

                this.form
                    .querySelectorAll(".error-card")
                    .forEach(el=>{
                        el.classList.remove("error-card");
                    });

                this.dropArea.classList.remove("error-card");
            },

            showError(input,message){

                input.classList.add("error-field");
                const group=input.closest(".form-group");

                if(!group) return;
                const error=group.querySelector(".error-text");

                if(error){
                    error.innerHTML=message;
                    error.style.display="block";
                }
            },

            /*======================================
            VALIDATE
            ======================================*/
            validate(){
                this.clearErrors();
                let valid=true;
                let firstError=null;

                const required=this.form.querySelectorAll("[required]");
                required.forEach(input=>{

                    if(input.value.trim()==""){
                        valid=false;

                        this.showError(
                            input,
                            "Field ini wajib diisi."
                        );

                        if(!firstError){
                            firstError=input;
                        }

                    }

                });

                // PHOTO
                if(this.photoInput.files.length===0 && !this.dropArea.classList.contains("has-image")){
                    valid=false;

                    this.dropArea.classList.add("error-card");

                    if(!firstError){
                        firstError=this.dropArea;
                    }

                }

                // ROLE
                const role=this.form.querySelectorAll(
                    'input[name="role"]:checked'
                );

                const roleOther=document
                    .getElementById("roleOtherInput")
                    .value
                    .trim();

                if(role.length===0 && roleOther===""){

                    valid=false;

                    document
                        .querySelectorAll('input[name="role"]')
                        .forEach(i=>{

                            i.closest(".check-card")
                            ?.classList.add("error-card");

                        });

                    document
                        .getElementById("roleOtherInput")
                        .classList.add("error-field");

                    if(!firstError){

                        firstError=this.form.querySelector(
                            'input[name="role"]'
                        );

                    }

                }

                // INTEREST
                const interest=this.form.querySelectorAll(
                    'input[name="interest"]:checked'
                );

                const interestOther=document
                    .getElementById("interestOtherInput")
                    .value
                    .trim();

                if(interest.length===0 && interestOther===""){

                    valid=false;

                    document
                        .querySelectorAll('input[name="interest"]')
                        .forEach(i=>{

                            i.closest(".check-card")
                            ?.classList.add("error-card");

                        });

                    document
                        .getElementById("interestOtherInput")
                        .classList.add("error-field");

                    if(!firstError){

                        firstError=this.form.querySelector(
                            'input[name="interest"]'
                        );

                    }

                }

                // PORTFOLIO
                const portfolioFields = [
                    "imdb",
                    "vimeo",
                    "youtube",
                    "instagram",
                    "website"
                ];

                let hasPortfolio = false;

                portfolioFields.forEach(name => {

                    const field = this.form.querySelector(`[name="${name}"]`);

                    if(field && field.value.trim() !== ""){

                        hasPortfolio = true;

                    }

                });

                if(!hasPortfolio){

                    valid = false;

                    portfolioFields.forEach(name => {

                        const field = this.form.querySelector(`[name="${name}"]`);

                        if(field){

                            field.classList.add("error-field");

                        }

                    });

                    if(!firstError){

                        firstError = this.form.querySelector('[name="imdb"]');

                    }

                }

                if(firstError){
                    firstError.scrollIntoView({
                        behavior:"smooth",
                        block:"center"
                    });

                    if(typeof firstError.focus === "function"){
                        firstError.focus();
                    }
                }

                if(!valid){
                    alert("Please complete all required fields.");
                }

                return valid;
            },

            /*======================================
            LIVE VALIDATION
            ======================================*/
            liveValidation(){

            this.form.querySelectorAll("input, textarea, select").forEach(input=>{
                input.addEventListener("input",()=>{

                    input.classList.remove("error-field");
                    const group=input.closest(".form-group");
                    if(group){

                        const err=group.querySelector(".error-text");
                        if(err){
                            err.style.display="none";
                        }

                    }

                });
            });

            // Role Other
            const roleOther=document.getElementById("roleOtherInput");
            roleOther.addEventListener("input",()=>{
                if(roleOther.value.trim()!==""){

                    roleOther.classList.remove("error-field");

                    document
                    .querySelectorAll('input[name="role"]')
                    .forEach(i=>{

                        i.closest(".check-card")
                        ?.classList.remove("error-card");

                    });

                }
            });

            // Interest Other
            const interestOther=document.getElementById("interestOtherInput");
            interestOther.addEventListener("input",()=>{
                if(interestOther.value.trim()!==""){

                    interestOther.classList.remove("error-field");

                    document
                    .querySelectorAll('input[name="interest"]')
                    .forEach(i=>{

                        i.closest(".check-card")
                        ?.classList.remove("error-card");

                    });

                }
            });

            // Portfolio
            const portfolio=[
                "imdb",
                "vimeo",
                "youtube",
                "instagram",
                "website"
            ];

            portfolio.forEach(name=>{

                const field=this.form.querySelector(`[name="${name}"]`);
                if(!field) return;
                field.addEventListener("input",()=>{

                    portfolio.forEach(n=>{

                        const f=this.form.querySelector(`[name="${n}"]`);
                        if(f){
                            f.classList.remove("error-field");
                        }

                    });

                });
            });

        },

        openPreviewModal(){
            this.previewModal.style.display="flex";

            // Photo
            document.getElementById("previewModalPhoto").src=
                this.previewImage.src;

            // Basic info
            document.getElementById("pvName").textContent=
                this.form.querySelector('[name="fullname"]').value;

            document.getElementById("pvWhatsapp").textContent=
                this.form.querySelector('[name="whatsapp"]').value;

            document.getElementById("pvEmail").textContent=
                this.form.querySelector('[name="email"]').value;

            // Roles
            const roleBox=document.getElementById("pvRoles");
            roleBox.innerHTML="";

            this.form.querySelectorAll('input[name="role"]:checked').forEach(el=>{
                roleBox.innerHTML += `<span>${el.value}</span>`;
            });

            const roleOther=this.form.querySelector("#roleOtherInput").value.trim();
            if(roleOther){
                roleBox.innerHTML += `<span>${roleOther}</span>`;
            }

            // Interests
            const interestBox=document.getElementById("pvInterests");
            interestBox.innerHTML="";

            this.form.querySelectorAll('input[name="interest"]:checked').forEach(el=>{
                interestBox.innerHTML += `<span>${el.value}</span>`;
            });

            const interestOther=this.form.querySelector("#interestOtherInput").value.trim();
            if(interestOther){
                interestBox.innerHTML += `<span>${interestOther}</span>`;
            }

            // Portfolio links
            const setLink=(id,name)=>{
                const el=document.getElementById(id);
                const value=this.form.querySelector(`[name="${name}"]`)?.value.trim();

                if(value){
                    el.textContent=value;
                    el.href=value;
                }else{
                    el.textContent="-";
                    el.removeAttribute("href");
                }
            };

            setLink("pvImdb","imdb");
            setLink("pvVimeo","vimeo");
            setLink("pvYoutube","youtube");
            setLink("pvInstagram","instagram");
            setLink("pvWebsite","website");
        },

            /*==============================================
                CLEAR DRAFT
            ==============================================*/
            clearDraft() {
                localStorage.removeItem(this.draftKey);
            },
        };

        document.addEventListener("DOMContentLoaded", () => {
            MemberRegistration.init();
        });