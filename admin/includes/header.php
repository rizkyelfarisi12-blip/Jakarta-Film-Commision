<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__.'/../../config/path.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title><?= $pageTitle ?? "JFC CMS"; ?></title>

<link rel="stylesheet" href="<?= $assetPath ?>assets/css/admin.css">

<link
href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
rel="stylesheet">

<script>

const API_URL = "<?=API_URL?>";

const UPLOAD_URL = "<?=UPLOAD_URL?>";

const ROOT_URL = "<?= ROOT_PATH ?>";

const ADMIN_URL = "<?=ADMIN_URL?>";

const ROOT_PATH = "<?=ROOT_PATH?>";

</script>

</head>

<body>