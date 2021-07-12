<?php
include_once 'config/Database.php';
include_once 'models/Cart.php';
require_once 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

// Make sure the user is logged in.
if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: login.php");
    exit;
    // Make sure all fields are filled.
} else if ((!isset($_GET['id']) || empty($_GET['id'])) && $_GET['item_id'] != 0) {
    http_response_code(Response::$BAD_REQUEST); // Bad Request.
    echo "Item id is required.";
    exit;
}

$db = new Database();
$conn = $db->connect();

//get item id from url
$itemId = $_GET["id"];
//create cart obj
$cart = new Cart($conn);
$cart->item_id = $itemId;
$cart->user_id = $_SESSION['id'];
$cart->deleteItem(); // Delete the item from the cart.
header("Location: cart.php");
