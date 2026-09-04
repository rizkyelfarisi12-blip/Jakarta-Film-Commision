<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>JFC Admin Login</title>

<link rel="stylesheet" href="assets/css/admin.css">

</head>

<body class="login-page">

    <div class="login-overlay"></div>

    <div class="login-card">

        <img
            src="assets/icon/JFC Logo 2BW.png"
            class="login-logo">

        <h1>Jakarta Film Commission</h1>

        <p>Administration Panel</p>

        <?php

        /*
        |--------------------------------------------------------------------------
        | ERROR MESSAGE
        |--------------------------------------------------------------------------
        |
        | login-process.php redirect ke sini dengan ?error=... kalau
        | gagal. Sebelumnya tidak pernah ditampilkan ke user.
        |
        |--------------------------------------------------------------------------
        */

        $error =
            $_GET["error"] ?? "";

        $errorMessages = [

            "empty" =>
                "Please enter both username and password.",

            "invalid" =>
                "Invalid username or password.",

            "inactive" =>
                "This account is inactive. Please contact a Super Admin.",

            "system" =>
                "A system error occurred. Please try again.",

            "locked" =>
                "Too many failed attempts. Please try again in " .
                (int) ($_GET["minutes"] ?? 15) .
                " minute(s)."

        ];

        if ($error !== "" && isset($errorMessages[$error])) {

            echo '<div class="login-error">' .
                htmlspecialchars($errorMessages[$error], ENT_QUOTES) .
                '</div>';

        }

        ?>

        <form action="login-process.php" method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                autocomplete="username"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password"
                autocomplete="current-password"
                required>

            <button type="submit">

                Login

            </button>

        </form>

    </div>

</body>

</html>