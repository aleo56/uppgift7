<?php

$db_hostname = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "filuppladdning";
$database = new mysqli($db_hostname, $db_username, $db_password, $db_database);

if ($database->connect_error) {
    die("connection to database failed :(");
}

$signingIn = isset($_POST["sign-in"]);
$signingUp = isset($_POST["sign-up"]);
$uploading = isset($_POST["upload"]);

if ($signingIn || $signingUp) {
    $username = $_POST["username"];
    $password = $_POST["password"];
}

if ($signingIn) {
    if (validLogin()) { // login success
        echo "you are now logged in :)<br>";
        session_start();
        $_SESSION["user"] = $_POST["username"];
    } else {
        echo "wrong login<br>";
    }
} else if ($signingUp) {
    if (validNewUsername()) {
        echo "new user added";
        $database->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
    } else {
        echo "invalid new username<br>";
    }
} else if ($uploading) {
    session_start();
    $file = $_FILES['file'];

    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png', 'pdf');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 100000) {
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                if (!is_dir("uploads")) {
                    mkdir("uploads");
                }
                $fileDestination = 'uploads/' . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);
                // lägg till i databas
                $username = $_SESSION["user"];
                $snuskig = false;
                if ($username == "holros") {
                    $snuskig = true;
                }
                $sql = "INSERT INTO uploads (user, filepath, uploadtime, snuskig) VALUES ('$username', '$fileDestination', NOW(), '$snuskig')";
                $result = $database->query($sql);

                header("Location: index.php?uploadsuccess");
            } else {
                echo "För stor fil";
            }
        } else {
            echo "Error";
        }
    } else {
        echo "fel filtyp";
    }
}



echo "<a href='index.php'>Go home</a>";

function validLogin()
{
    global $database;
    global $username;
    global $password;

    $password_query = $database->query("SELECT * FROM users WHERE username='$username'");
    return $password_query->fetch_assoc()["password"] === $password;
}

function validNewUsername()
{
    global $database;
    global $username;

    $username_query = $database->query("SELECT * FROM users WHERE username='$username'");
    return !$username_query->fetch_assoc();
}
