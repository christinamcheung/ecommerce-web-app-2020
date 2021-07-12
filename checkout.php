<?php
require_once "config/Database.php";
require_once "models/Order.php";
require_once "models/Cart.php";
require_once "models/Item.php";
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


$cart_error = null;
$shipping_address_error = null;
$city_error = null;
$country_error = null;
$state_error = null;
$zip_error = null;

$shipping_details = array(
        "shipping_address" => null,
        "city" => null,
        "country" => null,
        "state" => null,
        "zip" => null
);

$db = new Database();
$conn = $db->connect();

$cart = new Cart($conn);
$cart->user_id = $_SESSION['id'];

if (count($cart->getItems()) == 0) {
    http_response_code(Response::$BAD_REQUEST);
    header("Location: cart.php");
    exit;
}

$form_submitted = $_SERVER["REQUEST_METHOD"] == "POST";
// Check if the the form been submitted.
if ($form_submitted) {
    if (!isset($_POST['address']) || empty($_POST['address'])) {  // Check address field.
        $shipping_address_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $shipping_details['shipping_address'] = $_POST['address'];
    }

    if (!isset($_POST['country']) || empty($_POST['country'])) { // Check country field.
        $country_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $shipping_details['country'] = $_POST['country'];
        if ($shipping_details['country'] != "Canada" && $shipping_details['country'] != "England" && $shipping_details['country'] != "United States") {
            $country_error = "We only ship to Canada, England and the United States.";
        }
    }

    if (!isset($_POST['city']) || empty($_POST['city'])) { // Check city field.
        $city_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $shipping_details['city'] = $_POST['city'];
    }

    if (!isset($_POST['state']) || empty($_POST['state'])) { // Check state field.
        $state_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $shipping_details['state'] = $_POST['state'];
    }

    if (!isset($_POST['zip']) || empty($_POST['zip'])) { // Check zip field.
        $zip_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $shipping_details['zip'] = $_POST['zip'];
    }

    // Make sure all fields have been filled and no errors were found, if so process the order.
    if ($cart_error == null && $shipping_address_error == null && $country_error == null && $state_error == null && $city_error == null && $zip_error == null) {
        $order = new Order($conn);
        $order->user_id = $_SESSION["id"];
        $order->shipping_info = $shipping_details["shipping_address"] . ", " . $shipping_details["city"] . ", " . $shipping_details["state"] . ", " . $shipping_details["zip"] . ", " . $shipping_details["country"];
        $shipping = $order->shipping_info;
        $order->addToOrder(); // Add all items and shipping info ti the order.
        $_SESSION['order_id'] = $order->order_id;
        header("Location: confirm.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Import Stylesheets-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <title>Checkout</title>
</head>

<body>
<?php require "header.php"; // Load header to top of page
?>
<!-- Container to hold form -->
<div class="page-container">
    <h1 class="text-center" style="margin-top:1rem;">Checkout</h1>
    <!-- Form on submit post details to confirm.php -->
    <form class="forms" action="checkout.php" method="post">
        <div id="form-inputs">
            <div id="left-forms">
                <div class="form-group">
                    <div id="address">
                        <label for="InputAddress">Shipping Address</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $shipping_address_error != null ? 'is-invalid' : 'is-valid'?>" id="InputAddress" name="address" placeholder="Address" value="<?php echo $shipping_details['shipping_address'];?>" required>
                    </div>
                    <div id="city">
                        <label for="InputCity">City</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $city_error != null ? 'is-invalid' : 'is-valid'?>" id="InputCity" name="city" placeholder="City" value="<?php echo $shipping_details['city'];?>" required>
                    </div>
                    <div id="country">
                        <label for="SelectCountry">Country</label>
                        <select class="form-control <?php if ($form_submitted) echo $country_error != null ? 'is-invalid' : 'is-valid'?>" id="SelectCountry" name="country" required>
                            <?php // Display the options for country.
                            foreach (array("Canada", "England", "United States") as $country) {
                                if ($shipping_details['country'] == $country) {
                                    echo "<option selected>$country</option>";
                                } else {
                                    echo "<option>$country</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div id="right-forms">
                <div class="form-group">
                    <div id="state">
                        <label for="InputState">State</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $state_error != null ? 'is-invalid' : 'is-valid'?>" id="InputState" name="state" placeholder="State" value="<?php echo $shipping_details['state'];?>" required>
                    </div>
                    <div id="zip">
                        <label for="InputZip">Zip</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $zip_error != null ? 'is-invalid' : 'is-valid'?>" id="InputZip" name="zip" placeholder="Zip" value="<?php echo $shipping_details['zip'];?>" required>
                    </div>
                </div>
            </div>
        </div>
        <hr class="col-12">
        <div id="pay-button">
            <!--Post shipping information and place order on click-->
            <button type="submit" class="btn" id="paypal-button"></button>
        </div>
    </form>
</div>
<!-- Import scripts -->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>