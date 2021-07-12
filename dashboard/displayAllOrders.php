<?php
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

$user = new User($conn); // Connection comes header.
$user->user_id = $_SESSION['id'];
$user->getUser();
if ($user->type !== 'admin') { // Make sure the user is an admin.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
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
                <h1 class="h3 mb-2 text-gray-800">Order List</h1>
                <p></p>
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Order List</h6>
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

                                //create a order obj
                                $order = new Order($conn);
                                //get all the orders from db
                                $orders = $order->getOrders();
                                //for each order in orders
                                foreach ($orders as $o) {
                                    //set order id
                                    $order->order_id = $o['order_id'];
                                    //get order information
                                    $order->getOrder();
                                    //print order id , time
                                    echo "<tr>";
                                    echo "<td>$order->order_id</td>";
                                    echo "<td>$order->order_time</td>";
                                    //view the detial of the order
                                    echo "<td><a href='orderDetail.php?id=$order->order_id'><i class=\"fas fa - trash text - danger\"></i>View</a></td>";

                                    echo "<td>
                                                <a href='displayAllOrders.php?id=$order->order_id&type=delete'><i class=\"fas fa - trash text - danger\"></i>Delete</a>
                                         </td>";
                                    echo "</tr>";
                                }
                                ?>
                                </tbody>
                            </table>
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