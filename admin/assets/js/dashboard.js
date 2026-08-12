/*==========================================================
JAKARTA FILM COMMISSION
ADMIN DASHBOARD
==========================================================*/

const Dashboard = {

    init(){

        this.renderStats();
        this.renderRecentArticles();

    },

    /*=========================================
    STATISTICS
    =========================================*/
    renderStats(){
        const totalArticles = pressData.length;

        const official =
            pressData.filter(item =>
                item.category === "Official Release"
            ).length;

        const program =
            pressData.filter(item =>
                item.category === "Program Update"
            ).length;

        const industry =
            pressData.filter(item =>
                item.category === "Industry News"
            ).length;

        document.getElementById("totalArticles").textContent =
            totalArticles;

        document.getElementById("officialCount").textContent =
            official;

        document.getElementById("programCount").textContent =
            program;

        document.getElementById("industryCount").textContent =
            industry;
    },

    /*=========================================
    RECENT ARTICLES
    =========================================*/
    renderRecentArticles(){

        const tbody =
            document.getElementById("recentArticles");

        tbody.innerHTML = "";

        const latest =
            [...pressData]
            .sort((a,b)=>
                new Date(b.date)-new Date(a.date)
            )
            .slice(0,5);

        latest.forEach(article=>{

            tbody.innerHTML += `

            <tr>

                <td>
                    <img
                        src="${article.image}"
                        class="thumb">
                </td>

                <td>
                    ${article.title}
                </td>

                <td>
                    <span class="badge ${this.categoryClass(article.category)}">
                        ${article.category}
                    </span>
                </td>

                <td>
                    ${formatDate(article.date)}
                </td>

                <td>
                    <button class="table-btn edit">
                        Edit
                    </button>
                </td>

            </tr>

            `;
        });
    },

    categoryClass(category){

        switch(category){

            case "Official Release":
                return "official";

            case "Program Update":
                return "program";

            case "Industry News":
                return "industry";

            default:
                return "";
        }
    }
};

document.addEventListener("DOMContentLoaded",()=>{

    Dashboard.init();

});