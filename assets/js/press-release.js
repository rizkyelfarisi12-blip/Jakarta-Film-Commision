
/* =========================
FORMAT DATE
========================= */
function formatDate(date){
    return new Date(date).toLocaleDateString("id-ID",
        {
        day:"numeric",
        month:"long",
        year:"numeric"
        }
    );
}

/* =========================
CATEGORY COLOR
========================= */
function getCategoryClass(category){
    switch(category.toLowerCase()){

        case "industry":
            return "industry";

        case "official release":
            return "official-release";

        case "program":
            return "program";

        default:
            return "";
    }
}


/* =========================
GET LATES RELEASE
========================= */
function getLatestPress(limit = 3, excludeSlug = null){

    return [...pressData]

        .filter(item =>

            excludeSlug
            ? generateSlug(item.title) !== excludeSlug
            : true
        )

        .sort((a,b)=>

            new Date(b.date) - new Date(a.date)
        )

        .slice(0, limit);

}


/* =========================
CALCULATE TIME
========================= */
function calculateReadTime(content){

    const words = content.join(" ").split(/\s+/).length;

    const minutes = Math.max(
        1,
        Math.ceil(words / 200)
    );

    return minutes + " min read";

}