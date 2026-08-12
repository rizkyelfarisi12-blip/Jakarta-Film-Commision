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

        <form action="login-process.php" method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password"
                required>

            <button type="submit">

                Login

            </button>

        </form>

    </div>

</body>

</html>