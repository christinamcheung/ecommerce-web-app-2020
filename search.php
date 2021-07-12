<?php
include 'config/Database.php';
include 'models/Item.php';
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $value = htmlspecialchars($_POST['search']);
} else {
    $value = "";
}

$mysql = new Database();
$conn = $mysql->connect();
$query = "SELECT * from `items` where UPPER(name) like UPPER(:name) "; // Get all items like search query.
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
<?php require "header.php"; //load header to top of page ?>
<div class="container col-12">
    <h2>Search Results for: <?php echo htmlspecialchars($value) ?></h2>
    <div class="items">
        <?php
        $stmt = $conn->prepare($query);
        $stmt->bindValue(":name", "%{$value}%");
        $stmt->execute();
        $result = $stmt->fetchAll();
        //for each result in the query, print the item details
        if (sizeOf($result) != 0) {
            foreach ($result as $row) {
                $item = new Item($conn);
                $item->db_construct($row);
                $item->getItem();
                $item->displayCard();
            }
        } else {
            echo "No results. Try a different search";
        }
        ?>
    </div>
</div>
<!--JavaScript at end of body for optimized loading-->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>