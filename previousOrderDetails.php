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
    // Make sure an id has been specified.
} else if (!isset($_GET['id']) || (empty($_GET['id']) && $_GET['id'] != 0)) {
    http_response_code(Response::$BAD_REQUEST); // Bad request.
    echo 'Order ID must be specified.';
    exit;
} else {
    $db = new Database();
    $conn = $db->connect();

    $order = new Order($conn);
    $order->order_id = $_GET['id'];
    $order->user_id = $_SESSION['id'];

    if (!$order->exists()) { // Make sure the spe
        http_response_code(Response::$NOT_FOUND); // Not Found.
        echo 'Could not find the specified order.';
        exit;
    } else if (!$order->isOwnedByUser()) { // Make sure the user owns the order they try too look at.
        http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
        echo 'You do not have permission to access this order.';
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--Import stylesheets-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">

    <title>Cart</title>
</head>

<body>
<?php
require_once 'header.php';
?>
<br>
<div class="container col-1w"
     style="border: .25rem rgb(241, 241, 241) solid; border-radius: 2rem; padding-bottom: 1rem;">
    <br>
    <h2 class="text-center" id="cart-header">Order History</h2><br><br>
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
            $order = new Order($conn);
            $order->order_id = $_GET['id'];
            $item = new Item($conn);
            $total = 0;
            $totalSum = 0;
            foreach ($order->getSoldItems() as $i) { // Go through each item in the order.
                $item->item_id = $i['item_id'];
                $quantity = $i['quantity'];
                $item->getItem(); // Get item details and display the item.
                echo "<tr>";
                echo "<td>$item->item_id</td>";
                echo "<td>$item->name</td>";
                echo "<td>$item->price</td>";
                echo "<td>$quantity</td>";
                echo "<td>$total</td>";
                echo "<td></td>";
                echo "</tr>";
                $total = $total + $item->price * $quantity;
                $totalSum += $total;
            }

            echo "<tr>
                                <th scope=\"col\">Total: $ $totalSum</th>
                                <th scope=\"col\"> &emsp; &emsp;</th>
                                <th scope=\"col\"> &emsp;</th>
                                <th scope=\"col\"> &emsp;</th>
                                <th scope=\"col\"> &emsp;</th>
                                <th scope=\"col\"></th>
                            </tr>";
            ?>
        </table>
    </div>
</div>
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>