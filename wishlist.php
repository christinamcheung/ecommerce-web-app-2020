<!--
    cart.php page which loads the items added to the cart.
    user can checkout to place an order.
-->

<?php
include_once 'config/Database.php';
include_once 'models/Wishlist.php';
require 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

// Make sure the user is logged in.
if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: login.php");
    exit;
}

// Create new connection to the Database and create new Cart instance
$db = new Database();
$conn = $db->connect();
$wishlist = new Wishlist($conn);
$wishlist->user_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Import styling-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <title>Wishlist</title>
</head>

<body>
<?php
require_once 'header.php'; //Load header at top of page
?>
<br>
<!-- Container to hold the cart items and table-->
<div class="container col-1w"
     style="border: .25rem rgb(241, 241, 241) solid; border-radius: 2rem; padding-bottom: 1rem;">
    <br>
    <h2 class="text-center" id="cart-header">My Wishlist</h2><br><br>
    <div class="container col-12 bg-dark" style="padding: 1rem; border-radius: 2rem;">

        <table class="table table-dark table-striped table-hover thead-dark" style="margin-bottom:0;">
            <tr>
                <th scope="col">ID#</th>
                <th scope="col">Product</th>
                <th scope="col">Price</th>
                <th scope="col"></th>
            </tr>
            <?php
            $wishlist_items = $wishlist->getItems();
            //For every item in wishlist fetch the item details and print to the table
            foreach ($wishlist_items as $current_item) {
                    echo "<tr>
                                    <td>{$current_item['item_id']}</td>
                                    <td>{$current_item['name']}</td>
                                    <td>{$current_item['price']}</td>
                                    <td><a href='deleteItemFromWishlist.php?id={$current_item['item_id']}'><span class='material-icons text-light'>delete</span></a></td>
                                </tr>";
            }
            ?>
        </table>
    </div>
</div>
<!--Import scripts-->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>