<?php
include "dashboard_header.php";
include "dashboard_sidebar.php";
require_once "../models/Item.php";
require_once '../util/Response.php';

$error = '';

$user = new User($conn); // Connection comes from dashboard header.
$user->user_id = $_SESSION['id'];
$user->getUser();
if ($user->type !== 'seller') { // Make sure the user is a seller.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ( // Make sure all fields are filled.
        isset($_POST['itemName']) && isset($_POST['itemAuthor']) && isset($_POST['itemPages']) && isset($_POST['itemPrice']) && isset($_POST['itemStock']) && isset($_POST['itemDescription']) &&
        !empty($_POST['itemName']) && !empty($_POST['itemAuthor']) && !empty($_POST['itemPages']) && !empty($_POST['itemPrice']) && !empty($_POST['itemStock']) && !empty($_POST['itemDescription'])
    ) {
        $item = new Item($conn);
        $item->seller_id = $_SESSION['id'];
        $item->name = $_POST['itemName'];

        // Make sure an image was uploaded.
        if (file_exists($_FILES['itemImage']['tmp_name']) && is_uploaded_file($_FILES['itemImage']['tmp_name'])) {
            if (!$item->exists()) { // Make sure the item name is not taken.
                $item->author = $_POST['itemAuthor'];
                $item->price = $_POST['itemPrice'];
                $item->stock = $_POST['itemStock'];
                $item->description = $_POST['itemDescription'];
                $item->number_pages = $_POST['itemPages'];

                $target_dir = "../data/product-images/"; // Where uploaded images go.
                $unique_filename = uniqid('uploaded-', true) // Generate unique ID for the image.
                    . '.' . strtolower(pathinfo($_FILES['itemImage']['name'], PATHINFO_EXTENSION));
                $target_file = $target_dir . $unique_filename; // Path for the uploaded image to be stored.
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Get the file type.
                $check = getimagesize($_FILES["itemImage"]["tmp_name"]);
                if ($check !== false) { // Make sure the item is an image.
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        http_response_code(415); // Unsupported Media Type.
                        $error = "Sorry, only JPG, JPEG & PNG files are allowed."; // Check the file extension.
                    } else {
                        // Try moving the file to a permanent location.
                        if (move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file)) {
                            $item->image = $unique_filename;
                            $item_id = $item->addItem(); // If the move was successful create the new item.
                            if ($item_id !== null) {
                                header("Location: ../page.php?id=$item_id"); // Go to the item page.
                            } else {
                                http_response_code(Response::$INTERNAL_SERVER_ERROR); // Server error.
                                unlink($target_file); // If creating the item failed, delete the uploaded image.
                                $error = "There was an error adding the item.";
                            }
                        } else {
                            http_response_code(Response::$INTERNAL_SERVER_ERROR); //  Server error.
                            $error = "Sorry, there was an error uploading your file.";
                        }
                    }
                } else {
                    http_response_code(415); // Unsupported Media Type.
                    $error = "File is not an image.";
                }
            } else {
                http_response_code(Response::$CONFLICT); // Conflict.
                $error = 'Item name already exists.';
            }
        } else {
            http_response_code(413); // Payload Too Large.
            $error = 'Image is too big';
        }
    } else {
        http_response_code(Response::$BAD_REQUEST); // Bad Request.
        $error = "All fields not filled.";
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
                    <h1 class="h3 mb-0 text-gray-800">Add Item</h1>
                </div>

                <form action="addItem.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="itemName">Item Name</label>
                        <input class="form-control" type="text" id="itemName" name="itemName" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemAuthor">Author</label>
                        <input class="form-control" type="text" id="itemAuthor" name="itemAuthor" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemPages">Number of pages</label>
                        <input class="form-control" type="text" id="itemPages" name="itemPages" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemPrice">Price</label>
                        <input class="form-control" type="number" id="itemPrice" name="itemPrice" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemStock">Stock</label>
                        <input class="form-control" type="number" id="itemStock" name="itemStock" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemImage">Image</label>
                        <input class="form-control" type="file" id="itemImage" name="itemImage" required/>
                    </div>
                    <div class="form-group">
                        <label for="itemDescription">Description</label>
                        <textarea class="form-control" id="itemDescription" name="itemDescription" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit</button>
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