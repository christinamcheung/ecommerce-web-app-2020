<?php
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

if ($user->type !== 'admin') { // Make sure the user is an admin.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
}

if (isset($_GET["id"]) && isset($_GET["type"])) {
    $order = new Order($db->connect());
    $order->order_id = $_GET["id"];
    if ($order->exists()) {
        $order->deleteOrder();
        header("Location: searchOrder.php");
    } else {
        http_response_code(Response::$NOT_FOUND); // Not Found;
        echo 'Could not find the specified order.';
        exit;
    }

}
?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php
            require_once "dashboard_topbar.php";
            ?>

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <!-- Page Heading -->
                <h1 class="h3 mb-2 text-gray-800">Search Order</h1>
                <p></p>
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Search Order</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Time</th>
                                    <th>View</th>
                                    <th>Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //create a order obk
                                $order = new Order($conn);
                                //check if the search box is set
                                if (isset($_GET["searchBox"]) && !empty($_GET["searchBox"])) {
                                    //set the id of the order
                                    $order->order_id = $_GET["searchBox"];
                                    if ($order->exists()) {
                                        //get the order detail
                                        $order->getOrder();
                                        echo "<tr>";
                                        echo "<td>$order->order_id</td>";
                                        echo "<td>$order->order_time</td>";
                                        //display the order detail
                                        echo "<td><a href='orderDetail.php?id=$order->order_id'><i class=\"fas fa - trash text - danger\"></i>View</a></td>";
                                        //delete the order button
                                        echo "<td>
                                                <a href='displayAllOrders.php?id=$order->order_id&type=delete'><i class=\"fas fa - trash text - danger\"></i>Delete</a>
                                         </td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="input-group mb-3">
                                <form name="form" action="searchOrder.php" method="get">
                                    <div class="input-group-append">
                                        <input type="text" name="searchBox" id="searchBox" class="form-control"
                                               placeholder="Order Number" aria-label="Order Number"
                                               aria-describedby="button-addon2">
                                        <button type="submit" class="btn btn-outline-secondary bg-primary text-white"
                                                id="button-addon2">Search
                                        </button>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

    </div>
    <!-- End of Page Wrapper -->
    </div>
<?php
include "dashboard_logoutModal.php";
include "dashboard_footer.php";
?>