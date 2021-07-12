<?php
require_once "config/Database.php";
require_once "models/Item.php";
require_once "models/Wishlist.php";
require_once 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

// Make sure the user is already logged in.
if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: login.php");
    exit;
}

//create a connection to the database
$db = new Database();
$conn = $db->connect();

// Check to see if the user is adding to wishlist and validate all required items.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_wishlist_submit'])) {
    if (!isset($_POST['item_id']) || (empty($_POST['item_id']) && $_POST['item_id'] != 0)) {
        http_response_code(Response::$BAD_REQUEST);
        echo 'Item ID is required.';
        exit;
    }

    // Add the item to the wishlist if not already in the wishlist.
    $wishlist = new Wishlist($conn);
    $wishlist->user_id = $_SESSION['id'];
    $wishlist->item_id = $_POST['item_id'];
    $added_wishlist_sucessfully = $wishlist->addItem();
    $item_added = $_POST['item_id'];
    if ($added_wishlist_sucessfully == null) {
        http_response_code(Response::$CONFLICT);
        echo 'Could not add the item to your wishlist.';
        exit;
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manga Store</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import stylesheets-->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">

</head>

<body>
<?php
require "header.php" //load header to the top of page;
?>
<div class="container col-12">
    <h2>Best Sellers</h2>
    <div class="items">
        <?php
        //print all items that are sold most
        $item = new Item($conn);
        if ($recommended = $item->getTopTen()) { // Go through and display top 10 most purchased items.
            foreach ($recommended as $i) {
                $item->item_id = $i['item_id'];
                $item->getItem();
                $item->displayCard(isset($item_added) ? $item_added : null, isset($added_wishlist_sucessfully) && $added_wishlist_sucessfully != null ? true : false);
            }
        }
        ?>
    </div>
    <h2>Catalogue</h2>
    <div class="items">
        <?php
        //print all items
        if ($items = $item->getItems()) { // Go through and display entire catalog.
            foreach ($items as $i) {
                $item->item_id = $i['item_id'];
                $item->getItem();
                $item->displayCard(isset($item_added) ? $item_added : null, isset($added_wishlist_sucessfully) && $added_wishlist_sucessfully != null ? true : false);
            }
        }
        ?>
    </div>
</div>

<!--JavaScript at end of body for optimized loading-->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>