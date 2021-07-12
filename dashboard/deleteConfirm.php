<?php
//include classes
include '../models/User.php';
include_once '../config/Database.php';
require_once '../util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

$db = new Database();
$conn = $db->connect();
$user = new User($conn);

if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) { // Make sure the user is logged in.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: ../login.php");
    exit;
} else {
    $user = new User($conn);
    $user->user_id = $_SESSION['id'];
    $user->getUser();
    if ($user->type !== 'admin') { // Make sure the user is an admin.
        http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
        echo 'You do not have permission to access this page.';
        exit;
    }
}

if (!isset($_GET['id']) || empty($_GET['id'])) { // Make sure an ID was specified.
    http_response_code(Response::$BAD_REQUEST); // Bad Request.
    echo 'ID is required.';
    exit;
} else {
    $user->user_id = $_GET["id"];
    if ($user->existsById()) { // Make sure the specified user exists.
        $user->deleteUser(); // Delete the user.
        header("Location: accountManage.php");
        exit;
    } else {
        http_response_code(Response::$NOT_FOUND); // Not Found.
        echo 'The specified user was not found.';
        exit;
    }
}