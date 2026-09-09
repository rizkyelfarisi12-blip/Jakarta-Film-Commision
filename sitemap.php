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
| RESOLVE LAST MODIFIED DATE
|--------------------------------------------------------------------------
|
| Prioritas: updated_at > created_at.
|
| lastmod HARUS mencerminkan kapan konten terakhir diubah,
| bukan kapan pertama kali dibuat - supaya search engine tahu
| kalau ada konten lama yang baru saja diedit dan perlu
| di-crawl ulang.
|--------------------------------------------------------------------------
*/

function resolveLastMod($row)
{

    $value =
        !empty($row["updated_at"])
            ? $row["updated_at"]
            : ($row["created_at"] ?? null);


    if (empty($value)) {

        return null;

    }


    $timestamp =
        strtotime($value);


    if ($timestamp === false) {

        return null;

    }


    return date("Y-m-d", $timestamp);

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
| EVENTS LIST PAGE
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
| PRESS RELEASE LIST PAGE
|--------------------------------------------------------------------------
*/

$urls[] = [

    "loc" =>
        $baseUrl . "/press-release-list.html",

    "changefreq" =>
        "daily",

    "priority" =>
        "0.9"

];


/*
|--------------------------------------------------------------------------
| GET PUBLISHED EVENTS
|--------------------------------------------------------------------------
|
| CATATAN: query ini butuh kolom "updated_at" di tabel events.
| Kalau kolom itu belum ada dan query di bawah error, jalankan
| dulu:
|
|   ALTER TABLE events
|   ADD COLUMN updated_at TIMESTAMP NOT NULL
|   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
|   AFTER created_at;
|
|--------------------------------------------------------------------------
*/

$eventsSql = "

    SELECT
        id,
        slug,
        start_date,
        created_at,
        updated_at

    FROM events

    WHERE
        status = 'published'

        AND slug IS NOT NULL

        AND slug != ''

    ORDER BY
        start_date ASC,
        created_at DESC

";


$eventsResult =
    mysqli_query(
        $conn,
        $eventsSql
    );


/*
|--------------------------------------------------------------------------
| EVENT URLS
|--------------------------------------------------------------------------
*/

if ($eventsResult) {

    while (
        $event =
        mysqli_fetch_assoc($eventsResult)
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


        $lastMod =
            resolveLastMod($event);


        if ($lastMod !== null) {

            $item["lastmod"] =
                $lastMod;

        }


        $urls[] =
            $item;

    }

} else {

    /*
    | Query gagal (misal kolom updated_at belum ada) - jangan
    | hentikan seluruh sitemap, cukup skip bagian events dan
    | tetap lanjut ke press release + halaman statis.
    */

    error_log(
        "Sitemap: failed to fetch events - " .
        mysqli_error($conn)
    );

}


/*
|--------------------------------------------------------------------------
| GET PUBLISHED PRESS RELEASES
|--------------------------------------------------------------------------
|
| CATATAN: query ini pakai updated_at, yang sudah dipastikan
| ada di tabel press_releases (dipakai juga di get-press.php).
|--------------------------------------------------------------------------
*/

$pressSql = "

    SELECT
        id,
        slug,
        published_date,
        created_at,
        updated_at

    FROM press_releases

    WHERE
        status = 'published'

        AND slug IS NOT NULL

        AND slug != ''

    ORDER BY
        published_date DESC,
        created_at DESC

";


$pressResult =
    mysqli_query(
        $conn,
        $pressSql
    );


/*
|--------------------------------------------------------------------------
| PRESS RELEASE URLS
|--------------------------------------------------------------------------
*/

if ($pressResult) {

    while (
        $press =
        mysqli_fetch_assoc($pressResult)
    ) {

        $slug =
            trim(
                $press["slug"] ?? ""
            );


        if ($slug === "") {

            continue;

        }


        /*
        |--------------------------------------------------------------------------
        | PRESS RELEASE DETAIL URL
        |--------------------------------------------------------------------------
        |
        | Mengikuti URL yang dipakai oleh press-release.html:
        |
        | press-release.html?slug=...
        |
        */

        $pressUrl =
            $baseUrl .
            "/press-release.html?slug=" .
            rawurlencode($slug);


        $item = [

            "loc" =>
                $pressUrl,

            "changefreq" =>
                "weekly",

            "priority" =>
                "0.8"

        ];


        $lastMod =
            resolveLastMod($press);


        if ($lastMod !== null) {

            $item["lastmod"] =
                $lastMod;

        }


        $urls[] =
            $item;

    }

} else {

    error_log(
        "Sitemap: failed to fetch press releases - " .
        mysqli_error($conn)
    );

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