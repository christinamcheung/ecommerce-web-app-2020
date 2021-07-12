<!-- 
    cart.php page which loads the items added to the cart.
    user can checkout to place an order.
-->

<?php
include_once 'config/Database.php';
include_once 'models/Cart.php';
include_once 'models/Item.php';
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

// Create new connection to the Database and create new Cart instance
$db = new Database();
$conn = $db->connect();
$cart = new Cart($conn);
$cart->user_id = $_SESSION['id'];

# If the but now button has been pressed previously, add the item to the cart.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_now_submit'])) {
    if (!isset($_POST['item_id']) || (empty($_POST['item_id']) && $_POST['item_id'] != 0)) {
        echo 'Item id is required.';
        exit;
    }

    $cart->item_id = $_POST['item_id'];
    $cart->quantity = 1;
    $cart->addItem();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Import styling-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <title>Cart</title>
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
    <h2 class="text-center" id="cart-header">My Cart</h2><br><br>
    <div class="container col-12 bg-dark" style="padding: 1rem; border-radius: 2rem;">

        <table class="table table-dark table-striped table-hover thead-dark" style="margin-bottom:0;">
            <tr>
                <th scope="col">ID#</th>
                <th scope="col">Product</th>
                <th scope="col">Price</th>
                <th scope="col">QTY</th>
                <th scope="col">Amount</th>
                <th scope="col"></th>
            </tr>
            <?php
            $totalSum = 0;
            $count = 0;
            $cart_items = $cart->getItems();
            //For every item in cart fetch the item details and print to the table
            foreach ($cart_items as $current_item) {
                $item = new Item($conn);
                $item->item_id = $current_item['item_id'];
                $quantity = $current_item['quantity'];

                if ($item->getItem()) { // Get item details.
                    $amount = $quantity * $item->price;
                    $totalSum += $amount; // Create totals for each item / entire cart.


                    echo "<tr>
                                    <td>$item->item_id</td>
                                    <td>$item->name</td>
                                    <td>$item->price</td>
                                    <td>$quantity</td>
                                    <td>$amount</td>
                                    <td><a href='deleteItemFromCart.php?id=$item->item_id'><span class='material-icons text-light'>delete</span></a></td>
                                </tr>";
                }
            }

            if (count($cart_items) > 0) {
            echo "<tr>
                            <th scope=\"col\">Total: $$totalSum</th>
                            <th scope=\"col\"> &emsp; &emsp;</th>
                            <th scope=\"col\"> &emsp;</th>
                            <th scope=\"col\"> &emsp;</th>
                            <th scope=\"col\"> &emsp;</th>
                            <th scope=\"col\">
                                <form action='checkout.php' Method='get'>
                                    <button type='submit' class='btn' id='cart-btn'> Checkout</button>
                                </form>
                            </th>
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