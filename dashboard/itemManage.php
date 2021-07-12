<?php
include "dashboard_header.php";
include "dashboard_sidebar.php";
require_once "../models/Item.php";
require_once "../models/User.php";
require_once '../util/Response.php';

$user = new User($conn); // Connection comes from the dashboard header.
$user->user_id = $_SESSION['id'];

$user->getUser();
if ($user->type !== 'seller') { // Make sure the user is a seller.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // If request was GET, make sure a valid item_id was specified.
    if (isset($_GET['itemId']) && (!empty($_GET['itemId']) || $_GET['itemId'] == 0)) {
        $id = $_GET['itemId'];
    } else {
        http_response_code(Response::$BAD_REQUEST); // Bad Request.
        echo 'Item id is required.';
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') { // If request was POST, make sure a valid item_id was specified.
    if (isset($_POST['itemId']) && (!empty($_POST['itemId']) || $_POST['itemId'] == 0)) {
        $id = $_POST['itemId'];
    } else {
        http_response_code(Response::$BAD_REQUEST); // Bad Request.
        echo 'Item id is required.';
        exit;
    }
} else {
    http_response_code(Response::$BAD_REQUEST); // Bad Request.
    echo 'Invalid Request method.';
    exit;
}

$item = new Item($conn);
$item->item_id = $id;
if ($item->exists()) { // Make sure the specified item exists.
    $item->getItem();
    if (!($item->seller_id === $_SESSION['id'])) { // Make sure the user owns the item they are trying to modify.
        http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
        echo "You do not own this item.";
        exit;
    }
} else {
    http_response_code(Response::$NOT_FOUND); // Not Found.
    echo 'Item does not exist.';
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // If the page has been submitted.
    if ( // Make sure all require fields are filled.
        isset($_POST['itemName']) && isset($_POST['itemAuthor']) && isset($_POST['itemPages']) && isset($_POST['itemPrice']) && isset($_POST['itemStock']) && isset($_POST['itemDescription']) &&
        !empty($_POST['itemName']) && !empty($_POST['itemAuthor']) && !empty($_POST['itemPages']) && !empty($_POST['itemPrice']) && !empty($_POST['itemStock']) && !empty($_POST['itemDescription'])
    ) {
        $item->name = $_POST['itemName'];
        $item->author = $_POST['itemAuthor'];
        $item->price = $_POST['itemPrice'];
        $item->stock = $_POST['itemStock'];
        $item->description = $_POST['itemDescription'];
        $item->number_pages = $_POST['itemPages'];


        $upload_successful = true;
        $file_uploaded = false;
        if (isset($_FILES['itemImage']['name']) && !empty($_FILES['itemImage']['name'])) { // Check if the item image was changed.
            if (!($_FILES['itemImage']['name'] === $item->image)) {
                $file_uploaded = true;

                // Check if an item was submitted.
                if (file_exists($_FILES['itemImage']['tmp_name']) && is_uploaded_file($_FILES['itemImage']['tmp_name'])) {
                    $item->author = $_POST['itemAuthor'];
                    $item->price = $_POST['itemPrice'];
                    $item->stock = $_POST['itemStock'];
                    $item->description = $_POST['itemDescription'];
                    $item->number_pages = $_POST['itemPages'];

                    $target_dir = "../data/product-images/";
                    $unique_filename = uniqid('uploaded-', true) // Generate unique ID for uploaded image.
                        . '.' . strtolower(pathinfo($_FILES['itemImage']['name'], PATHINFO_EXTENSION));
                    $target_file = $target_dir . $unique_filename; // Path to store the uploaded image.
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Get file extension.
                    $check = getimagesize($_FILES["itemImage"]["tmp_name"]);
                    // Make sure the image isn't too big.
                    if ($check !== false) {
                        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") { // Check extension is valid.
                            http_response_code(415); // Unsupported Media Type.
                            $error = "Sorry, only JPG, JPEG & PNG files are allowed.";
                            $upload_successful = false;

                            // Try saving the new image.
                        } else if (!move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file)) {
                            http_response_code(Response::$INTERNAL_SERVER_ERROR); // Server Error.
                            $error = "Sorry, there was an error uploading your file.";
                            $upload_successful = false;
                        }
                    } else {
                        http_response_code(415); // Unsupported Media Type.
                        $error = "File is not an image.";
                        $upload_successful = false;
                    }
                } else {
                    http_response_code(413); // Payload Too Large.
                    $error = "Files is too big.";
                    $upload_successful = false;
                }
            }
        }

        if ($file_uploaded) { // If a new file was uploaded.
            if ($upload_successful) {
                $old_file = $item->image;
                $item->image = $unique_filename;
                $success = $item->update(); // Update the item with the new image name.
                if ($success != null) { // If the item was updated successfully.
                    unlink("../data/product-images/" . $old_file); // Delete the old image.
                    header("Location: ../page.php?id=$item->item_id"); // Go to item page.
                    exit;
                } else { // If the update was not successful.
                    unlink($target_file); // Remove the new file.
                    $item->image = $old_file; // Set the item image back to the original.
                    http_response_code(304); // Not Modified.
                    $error = "There was an error updating the item.";
                }
            }
        } else { // If no image was added.
            $success = $item->update(); // Update the item with new details.
            if ($success != null) { // If update was successfull.
                header("Location: ../page.php?id=$item->item_id"); // Go to item page.
                exit;
            } else { // If update was unsuccessful.
                http_response_code(304); // Not Modified.
                $error = "There was an error updating the item.";
            }
        }
    } else {
        http_response_code(Response::$BAD_REQUEST); // Bad Request.
        $error = 'All fields must be filled besides image, we will use the current if one is not specified.';
    }
}
?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php
            include "dashboard_topbar.php";
            ?>

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Update Item</h1>
                </div>

                <form action="itemManage.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" value="<?php echo $item->item_id; ?>" name="itemId"/>
                    <div class="form-group">
                        <label for="itemName">Item Name</label>
                        <input class="form-control" type="text" id="itemName" name="itemName"
                               value="<?php echo $item->name; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemAuthor">Author</label>
                        <input class="form-control" type="text" id="itemAuthor" name="itemAuthor"
                               value="<?php echo $item->author; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemPages">Number of pages</label>
                        <input class="form-control" type="text" id="itemPages" name="itemPages"
                               value="<?php echo $item->number_pages; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemPrice">Price</label>
                        <input class="form-control" type="number" id="itemPrice" name="itemPrice"
                               value="<?php echo $item->price; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemStock">Stock</label>
                        <input class="form-control" type="number" id="itemStock" name="itemStock"
                               value="<?php echo $item->stock; ?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemImage">Image</label>
                        <input class="form-control" type="file" id="itemImage" name="itemImage"/>
                    </div>
                    <div class="form-group">
                        <label for="itemDescription">Description</label>
                        <textarea class="form-control" id="itemDescription" name="itemDescription"
                                  required><?php echo $item->description; ?></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Update</button>
                    <?php echo "<p class='text-danger'>$error</p>" ?>
                </form>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

    </div>
    <!-- End of Page Wrapper -->
<?php
include "dashboard_logoutModal.php";
include "dashboard_footer.php";
?>