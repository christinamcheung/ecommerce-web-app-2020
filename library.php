<?php
include_once 'config/Database.php';
include_once 'models/Cart.php';
include_once 'models/Item.php';
require_once 'models/Order.php';
require_once 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

// Make sure the user is logged in.
if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: login.php");
    exit;
}

//establish database connection
$db = new Database();
$conn = $db->connect();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!--Import stylesheets-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen" />
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <title>Library</title>
</head>

<body>
    <?php
    require_once 'header.php'; //load header at top of page
    ?>
    <br>
    <!-- Container to hold order information -->
    <div class="container col-1w" style="border: .25rem rgb(241, 241, 241) solid; border-radius: 2rem; padding-bottom: 1rem;">
        <br>
        <h2 class="text-center" id="cart-header">My Library</h2><br><br>
        <div class="container col-12 bg-dark" style="padding: 1rem; border-radius: 2rem;">
            <div class="items">
                <?php
                $order = new Order($conn);
                $order->user_id = $_SESSION['id'];
                $item = new Item($conn);
                //for each order the user has placed, show order details
                foreach ($order->getOrdersForUser() as $o) {
                    $order->order_id = $o['order_id'];
                    foreach ($order->getSoldItems() as $product) {
                        $item->item_id = $product['item_id'];
                        $item->owned = True;
                        $item->getItem();
                        $item->displayCard();
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <!--Import scripts-->
    <script src="js/jquery-3.4.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>