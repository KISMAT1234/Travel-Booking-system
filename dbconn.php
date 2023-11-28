<?php
$servername = "b61efuvbqlxptl2ibh1o-mysql.services.clever-cloud.com";
$username = "uobsv3uhfqvdtaso";
$password = "PlTHYU9GFHbMo2lH0NWP";
$db = "b61efuvbqlxptl2ibh1o";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



<!-- Login -->
<?php

session_start();

$request_method = $_SERVER['REQUEST_METHOD'];
$msg = [];
if ($request_method === "POST") {

    require("dbconn.php");
    $page = $_SERVER['PHP_SELF'];
    if (validate_user()) {
        $msg[] = "Login Successful";
        $page = "profile.php";
        $_SESSION['msg'] = $msg;
        header("Location: $page");
    }
    $_SESSION['msg'] = $msg;
}
function validate_user()
{
    global $msg;
    if (!isset($_POST["email"]) && !isset($_POST["password"])) {
        $msg[] = "Invalid Email or Password";
        return false;
    }
    $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);


    $user = getDBUser($email);
    if ($user === false || !password_verify($password, $user['password'])) { // !true = false, !false = true
        $msg[] = "Invalid Email or Password";
        return false;
    }
    $_SESSION['user'] = $user;
    return true;
}

function getDBUser($email)
{
    global $conn, $msg;

    $sql = "SELECT * FROM user_accounts WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}
require('msg.php');
?>



<!-- Signup -->
<?php include('msg.php'); ?>


<!-- Logout -->
<?php
session_start();
unset($_SESSION['user']);
$msg[] = "You've signed out.";
$_SESSION['msg'] = $msg;
header("Location: login.php");
?>
