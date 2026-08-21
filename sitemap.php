<?php

require_once __DIR__ . "/api/db.php";


header("Content-Type: application/xml; charset=UTF-8");


$baseUrl = "https://jfc.co.id";


/*
|--------------------------------------------------------------------------
| XML HEADER
|--------------------------------------------------------------------------
*/

echo '<?xml version="1.0" encoding="UTF-8"?>';

echo '<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
>';


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

function xml($value)
{
    return htmlspecialchars(
        $value,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );
}


/*
|--------------------------------------------------------------------------
| STATIC PAGES
|--------------------------------------------------------------------------
*/

$staticPages = [

    [
        "url" => "/",
        "lastmod" => null
    ],

    [
        "url" => "/events.html",
        "lastmod" => null
    ],

    [
        "url" => "/films-list.html",
        "lastmod" => null
    ],

    [
        "url" => "/press-release-list.html",
        "lastmod" => null
    ],

    [
        "url" => "/programs.html",
        "lastmod" => null
    ]

];


foreach ($staticPages as $page) {

    echo "<url>";

    echo "<loc>"
        . xml(
            $baseUrl .
            $page["url"]
        )
        . "</loc>";

    echo "</url>";

}


/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

$eventQuery = mysqli_query(
    $conn,
    "

    SELECT
        slug,
        updated_at,
        created_at

    FROM events

    WHERE status = 'published'

    AND slug IS NOT NULL

    AND slug != ''

    ORDER BY
        updated_at DESC,
        created_at DESC

    "
);


if ($eventQuery) {

    while (
        $event =
        mysqli_fetch_assoc(
            $eventQuery
        )
    ) {

        /*
        |------------------------------------------------------------------
        | EVENT URL
        |------------------------------------------------------------------
        */

        $url =
            $baseUrl .
            "/event-detail.html?slug=" .
            rawurlencode(
                $event["slug"]
            );


        /*
        |------------------------------------------------------------------
        | LAST MODIFIED
        |------------------------------------------------------------------
        */

        $lastmod =
            $event["updated_at"] ??
            $event["created_at"] ??
            null;


        echo "<url>";

        echo "<loc>"
            . xml($url)
            . "</loc>";


        if ($lastmod) {

            $timestamp =
                strtotime($lastmod);

            if ($timestamp) {

                echo "<lastmod>"
                    . date(
                        "c",
                        $timestamp
                    )
                    . "</lastmod>";

            }

        }

        echo "</url>";

    }

}


/*
|--------------------------------------------------------------------------
| PRESS RELEASE
|--------------------------------------------------------------------------
|
| BAGIAN INI KITA SESUAIKAN SETELAH
| struktur database press release Anda
| dikirim.
|
|--------------------------------------------------------------------------
*/


echo "</urlset>";

$conn->close();