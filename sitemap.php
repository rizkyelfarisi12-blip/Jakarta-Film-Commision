<?php

/*
|--------------------------------------------------------------------------
| JAKARTA FILM COMMISSION
| AUTO XML SITEMAP
|--------------------------------------------------------------------------
|
| Sitemap dibuat secara dinamis dari database.
|
| Tidak perlu generate XML manual.
|
|--------------------------------------------------------------------------
*/


require_once __DIR__ . "/api/db.php";


/*
|--------------------------------------------------------------------------
| CONFIGURATION
|--------------------------------------------------------------------------
|
| GANTI DOMAIN INI DENGAN DOMAIN WEBSITE ANDA.
|
*/

$baseUrl =
    "https://www.jfc.co.id";


/*
|--------------------------------------------------------------------------
| REMOVE TRAILING SLASH
|--------------------------------------------------------------------------
*/

$baseUrl =
    rtrim(
        $baseUrl,
        "/"
    );


/*
|--------------------------------------------------------------------------
| XML HEADER
|--------------------------------------------------------------------------
*/

header(
    "Content-Type: application/xml; charset=UTF-8"
);


/*
|--------------------------------------------------------------------------
| XML ESCAPE
|--------------------------------------------------------------------------
*/

function xmlEscape($value)
{

    return htmlspecialchars(
        (string) $value,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );

}


/*
|--------------------------------------------------------------------------
| STATIC PAGES
|--------------------------------------------------------------------------
*/

$urls = [];


/*
|--------------------------------------------------------------------------
| HOMEPAGE
|--------------------------------------------------------------------------
*/

$urls[] = [

    "loc" =>
        $baseUrl . "/",

    "changefreq" =>
        "weekly",

    "priority" =>
        "1.0"

];


/*
|--------------------------------------------------------------------------
| EVENTS PAGE
|--------------------------------------------------------------------------
*/

$urls[] = [

    "loc" =>
        $baseUrl . "/events.html",

    "changefreq" =>
        "daily",

    "priority" =>
        "0.9"

];


/*
|--------------------------------------------------------------------------
| GET PUBLISHED EVENTS
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT
        id,
        slug,
        start_date,
        created_at

    FROM events

    WHERE
        status = 'published'

        AND slug IS NOT NULL

        AND slug != ''

    ORDER BY
        start_date ASC,
        created_at DESC

";


$result =
    mysqli_query(
        $conn,
        $sql
    );


/*
|--------------------------------------------------------------------------
| DATABASE ERROR
|--------------------------------------------------------------------------
*/

if (!$result) {

    http_response_code(500);

    echo '<?xml version="1.0" encoding="UTF-8"?>';

    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    echo '</urlset>';

    exit;

}


/*
|--------------------------------------------------------------------------
| EVENT URLS
|--------------------------------------------------------------------------
*/

while (
    $event =
    mysqli_fetch_assoc($result)
) {

    $slug =
        trim(
            $event["slug"] ?? ""
        );


    if ($slug === "") {

        continue;

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT DETAIL URL
    |--------------------------------------------------------------------------
    |
    | Mengikuti URL yang sekarang digunakan oleh events.js:
    |
    | event-detail.html?slug=...
    |
    */

    $eventUrl =
        $baseUrl .
        "/event-detail.html?slug=" .
        rawurlencode($slug);


    $item = [

        "loc" =>
            $eventUrl,

        "changefreq" =>
            "weekly",

        "priority" =>
            "0.8"

    ];


    /*
    |--------------------------------------------------------------------------
    | LAST MODIFICATION
    |--------------------------------------------------------------------------
    |
    | Untuk sementara menggunakan created_at jika tersedia.
    |
    */

    if (
        !empty(
            $event["created_at"]
        )
    ) {

        $timestamp =
            strtotime(
                $event["created_at"]
            );


        if ($timestamp !== false) {

            $item["lastmod"] =
                date(
                    "Y-m-d",
                    $timestamp
                );

        }

    }


    $urls[] =
        $item;

}


/*
|--------------------------------------------------------------------------
| XML OUTPUT
|--------------------------------------------------------------------------
*/

echo '<?xml version="1.0" encoding="UTF-8"?>';

echo "\n";

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

echo "\n";


foreach (
    $urls
    as $url
) {

    echo "    <url>\n";


    /*
    |----------------------------------------------------------------------
    | LOCATION
    |----------------------------------------------------------------------
    */

    echo "        <loc>";

    echo xmlEscape(
        $url["loc"]
    );

    echo "</loc>\n";


    /*
    |----------------------------------------------------------------------
    | LAST MOD
    |----------------------------------------------------------------------
    */

    if (
        !empty(
            $url["lastmod"]
        )
    ) {

        echo "        <lastmod>";

        echo xmlEscape(
            $url["lastmod"]
        );

        echo "</lastmod>\n";

    }


    /*
    |----------------------------------------------------------------------
    | CHANGE FREQUENCY
    |----------------------------------------------------------------------
    */

    if (
        !empty(
            $url["changefreq"]
        )
    ) {

        echo "        <changefreq>";

        echo xmlEscape(
            $url["changefreq"]
        );

        echo "</changefreq>\n";

    }


    /*
    |----------------------------------------------------------------------
    | PRIORITY
    |----------------------------------------------------------------------
    */

    if (
        isset(
            $url["priority"]
        )
    ) {

        echo "        <priority>";

        echo xmlEscape(
            $url["priority"]
        );

        echo "</priority>\n";

    }


    echo "    </url>\n";

}


echo "</urlset>";



/*
|--------------------------------------------------------------------------
| CLOSE DATABASE
|--------------------------------------------------------------------------
*/

$conn->close();