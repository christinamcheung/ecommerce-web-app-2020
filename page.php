<?php
require 'config/Database.php';
require 'models/User.php';
require 'models/Item.php';
require 'models/Cart.php';
require 'models/Wishlist.php';
require 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

$db = new Database();
$conn = $db->connect();

$quantity_error = null;
$form_submitted = $_SERVER['REQUEST_METHOD'] == 'POST';

// Make sure an item id is specified.
if ($form_submitted) {
    if (!isset($_POST['item_id']) || (empty($_POST['item_id']) && $_POST['item_id'] != 0)) {
        http_response_code(Response::$BAD_REQUEST); // Bad request.
        echo 'Item ID must be specified.';
        exit;
    }
} else {
    if (!isset($_GET['id']) || (empty($_GET['id']) && $_GET['id'] != 0)) {
        http_response_code(Response::$BAD_REQUEST); // Bad request.
        echo 'Item ID must be specified.';
        exit;
    }
}


// User is adding to cart.
if ($form_submitted && isset($_POST['add_cart_submit'])) {
    $item = new Item($conn);
    $item->item_id = $_POST['item_id'];
    if ($item->exists()) {
        if (!isset($_POST['quantity']) || (empty($_POST['quantity']) && $_POST['quantity'] != 0)) { // Check quantity is set.
            http_response_code(Response::$BAD_REQUEST);
            $quantity_error = "Required.";
        } else {
            $item->getItem();
            if ($_POST['quantity'] <= 0) { // Check quantity is > 0
                http_response_code(Response::$BAD_REQUEST);
                $quantity_error = "Quantity must be greater than 0.";
            } else {
                $cart = new Cart($conn);
                $cart->user_id = $_SESSION['id'];
                $cart_items = $cart->getItems();
                $quantity_in_cart = 0;
                foreach ($cart_items as $cart_item) { // Get the current quantity of the item from the cart.
                    if ($cart_item['item_id'] == $_POST['item_id']) {
                        $quantity_in_cart = $cart_item['quantity'];
                        break;
                    }
                }
                if ($quantity_in_cart > 0 && $quantity_in_cart + $_POST['quantity'] > $item->stock) { // No enough stock.
                    http_response_code(Response::$BAD_REQUEST);
                    $quantity_error = "Adding this many to the copies in your cart exceeds this item's stock.";
                } else if ($_POST['quantity'] > $item->stock) { // Not enough stock.
                    http_response_code(Response::$BAD_REQUEST);
                    $quantity_error = "There is not enough items in stock for your chosen quantity.";
                }
            }
        }
    }

    // If there is no error, add the item to the cart.
    if ($quantity_error == null) {
        $cart->item_id = $_POST['item_id'];
        $cart->quantity = $_POST['quantity'];
        if (!$cart->addItem()) { // Try adding the item.
            http_response_code(Response::$INTERNAL_SERVER_ERROR); // Server Error.
            echo 'There was an error adding the item.';
            exit;
        }
    }
} else if ($form_submitted && isset($_POST['add_wishlist_submit'])) { // User is adding to wishlist.
    $wishlist = new Wishlist($conn);
    $wishlist->user_id = $_SESSION['id'];
    $wishlist->item_id = $_POST['item_id'];
    $added_sucessfully = $wishlist->addItem(); // Add the item to their wishlist.
    if ($added_sucessfully == null) {
        http_response_code(Response::$CONFLICT);
        echo 'Could not add the item to your wishlist.';
        exit;
    }
}
$item_id = $form_submitted ? $_POST['item_id'] : $_GET['id'];

if ($form_submitted && isset($_POST['commentSubmit'])) { // User is leaving at comment.
    if (!isset($_SESSION['Logged']) || $_SESSION['Logged'] == false) {
        header('Location: login.php');
        exit;
    }
}

$db = new Database();
$conn = $db->connect();

$item = new Item($conn);
$item->item_id = $item_id;

if (!$item->exists()) { // Make sure the specified item exists.
    http_response_code(Response::$NOT_FOUND); // Not Found.
    echo 'Could not find the specified item.';
    exit;
}
$item->getItem();
$seller = new User($conn);
$seller->user_id = $item->seller_id;
$seller->getUser();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $item->name; ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import bootstrap.css-->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
</head>

<body>
<?php
require "header.php";

//page info
$content = <<<EOD
            <div class="item-container col-10 col-sm-10 col-" id="item-page">
                <div class="col-md-9 col-sm-12 col-" id="item-info">
                    <h2 id="page-title">{$item->name}</h2>
                    <h5 id="page-author">Author: <em>{$item->author}</em></h5>
                    <br>
                    <div id="item-details">
                        <div id="page-image">
                            <img src="data/product-images/{$item->image}" alt="{$item->name}" referrerpolicy="no-referrer"/>
                        </div>
                        <div id="page-extras">
                            <h5>Number of Pages in Comic: <em>{$item->number_pages}</em></h5>
                            <br>
                            <h5>Sold by: <em>{$seller->first_name} {$seller->last_name}</em></h5>
                            <h5>Stock available: <em>{$item->stock}</em></h5>
                        </div>
                    </div>
                    <br>
                    <div id="page-description">
                        <h5>Description:</h5>
                        <p>{$item->description}</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-xs-12" id="item-purchase">
                    <h4><b>Purchase</b></h4>
                    <br>
                    <h5 id="price_label"><b>Price:</b></h5>
                    <h3 id="price_value">&#36;{$item->price}</h3>
                    <br>
EOD;


$valid_quantity = $form_submitted && isset($_POST['add_cart_submit']) ? $quantity_error == null ? 'is-valid' : 'is-invalid' : '';
$default_value = $form_submitted && isset($_POST['add_cart_submit']) ? $_POST['quantity'] : 0;
if ($item->stock > 0) {
    // If there is stock, display add to cart button and quantity selector.
    $content .= "<form class=\"forms\" action=\"page.php?id={$item_id}\" method=\"post\">
                        <label for=\"selectQuantity\">Quantity:</label>
                        <input type=\"number\" class='{$valid_quantity}' name=\"quantity\" id=\"selectQuantity\" value=\"{$default_value}\" required/>
                        <div class='invalid-feedback'>
                            {$quantity_error}
                        </div>
                        <input type=\"hidden\" name=\"item_id\" value=\"{$item_id}\"/>
                        <button class=\"btn btn-primary\" id=\"cart-btn\" name=\"add_cart_submit\" type=\"submit\">Add to Cart</button>";

    if ($form_submitted && $quantity_error == null) {
        $content .= "<div class='valid-feedback'>
                            Item(s) added to cart!
                        </div>";
    }
}
$content .= "</form>";


// If item already in wishlist dont show add button.
$already_in_wishlist = Wishlist::containsItem($conn, $_SESSION['id'], $item_id);
if (!$already_in_wishlist) {
    $content .= "<form action='page.php' method='post'>
<input type=\"hidden\" name=\"item_id\" value=\"{$item_id}\"/>
<button class=\"btn btn-primary mt-3\" id=\"cart - btn\" name=\"add_wishlist_submit\" type=\"submit\">Add to Wishlist</button>
</form>";
}

if (isset($added_sucessfully) && $added_sucessfully != null) {
    $content .= "
                           <form>
                           <input type='hidden' class='is-valid' />
                            <div class='valid-feedback'>
                                Item added to Wishlist!
                            </div></form>";
}

echo $content;
echo "</div></div>";
?>
<div class="comments-wrapper col-8 col-sm-8 col-">
    <div style="margin: 0" class="card my-4">
        <h5 class="card-header">Leave a Review:</h5>
        <div class="card-body">
            <!-- Redirect to current page when submitting form. -->
            <form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post">
                <!-- Place to enter comment content -->
                <div class="form-group" style="width: 100%">
                    <textarea class="form-control" rows="3" name="new_comment"></textarea>
                </div>
                <input type="hidden" name="item_id" value="<?php echo $item_id;?>"/>
                <!-- Submit button for comment form -->
                <button type="submit" name="commentSubmit" class="btn btn-primary">Submit</button>
            </form>
            <?php
            // Check if new form was submitted.
            if ($form_submitted && isset($_POST['commentSubmit'])) {
                // Extract comment info.
                $new_comment = $_POST["new_comment"];
                // Check if comment was entered.
                $all_fields_filled = !empty($new_comment);
                if ($all_fields_filled) {
                    // Prepare statement for adding new comment,
                    $stmt = $conn->prepare("INSERT INTO reviews (item_id, user_id, comment_text) VALUES (:item_id, :user_id, :comment_text)");
                    // Attach comment info to the statement.
                    $stmt->bindParam(':item_id', $item_id);
                    $stmt->bindParam(':user_id', $_SESSION['id']);
                    $stmt->bindParam(':comment_text', $new_comment);
                    try {
                        $stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                } else {
                    echo '<br/><span class="text-danger">You need to enter a comment.</span><br/>';
                }
            }
            ?>
        </div>
    </div>
    <div>
        <?php
        $query = "SELECT u.first_name, u.last_name, r.comment_text FROM users u, reviews r WHERE r.item_id = :item_id AND u.user_id = r.user_id ORDER BY comment_id DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam("item_id", $item_id);
        $stmt->execute();
        $stmt->bindColumn('first_name', $first_name);
        $stmt->bindColumn('last_name', $last_name);
        $stmt->bindColumn('comment_text', $text);

        while ($stmt->fetch(PDO::FETCH_BOUND)) {
            $commenter_name = $first_name . ' ' . $last_name;
            echo "<div class=\"media mb-4\">
                            <div class=\"media-body\">
                                <h5 class=\"mt-0\">$commenter_name</h5>
                                $text
                            </div>
                        </div><br><hr>";
        }
        ?>
    </div>
</div>
<!-- Import scripts -->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>