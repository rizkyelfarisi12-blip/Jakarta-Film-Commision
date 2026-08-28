<?php

require_once __DIR__ . "/../api/db.php";


/*
|--------------------------------------------------------------------------
| CONFIGURATION
|--------------------------------------------------------------------------
*/

$username = "AdminJFCSuperFirst";

$password = "5Up3R4dM1n7Fc";

$name = "JFC Super Admin";

$email = null;

$role = "super_admin";

$status = "active";


/*
|--------------------------------------------------------------------------
| CHECK EXISTING USERNAME
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM admin_users
    WHERE username = ?
    LIMIT 1
");

$stmt->bind_param(
    "s",
    $username
);

$stmt->execute();

$result =
    $stmt->get_result();


if ($result->num_rows > 0) {

    exit(
        "Username already exists."
    );

}


/*
|--------------------------------------------------------------------------
| PASSWORD HASH
|--------------------------------------------------------------------------
*/

$passwordHash =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );


if (!$passwordHash) {

    exit(
        "Failed to generate password hash."
    );

}


/*
|--------------------------------------------------------------------------
| INSERT ADMIN
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO admin_users
    (
        username,
        password_hash,
        name,
        email,
        role,
        status
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
");


$stmt->bind_param(
    "ssssss",
    $username,
    $passwordHash,
    $name,
    $email,
    $role,
    $status
);


if (!$stmt->execute()) {

    exit(
        "Failed to create Super Admin: " .
        $stmt->error
    );

}


echo "
<!DOCTYPE html>

<html>

<head>

    <meta charset='UTF-8'>

    <title>Super Admin Created</title>

</head>

<body>

    <h1>Super Admin Created</h1>

    <p>
        Username:
        <strong>{$username}</strong>
    </p>

    <p>
        Role:
        <strong>Super Admin</strong>
    </p>

    <p>
        Account created successfully.
    </p>

    <p>
        <strong>
            IMPORTANT:
        </strong>
        Delete this file immediately.
    </p>

</body>

</html>
";


$conn->close();