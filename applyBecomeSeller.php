<?php
include_once "config/Database.php";
include_once "models/User.php";
require_once 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

$db = new Database();
$user = new User($db->connect());
$user->user_id = $_SESSION['id'];
$user->getUser();

// If the user isn't logged in take them to the login page.
if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: login.php");
    exit;
} else {
    if ($user->type !== 'consumer') { // Make sure the user is a consumer.
        http_response_code(Response::$UNAUTHORIZED); // Forbidden.
        echo 'You do not have permission to access this page.';
        exit;
    }
}

$user->applyToBeSeller(); // Create the seller request.
header("Location: index.php");
exit;
