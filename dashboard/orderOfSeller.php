<?php
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

if ($user->type !== 'seller') { // Make sure the user is a seller.
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
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Time</th>
                                <th>View</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            //create a order obj
                            $order = new Order($conn);
                            //get all the orders
                            $orders = $order->getOrders();
                            //create a item obj
                            $item = new Item($conn);

                            //for each order in orders
                            foreach ($orders as $o) {
                                //set order id
                                $order->order_id = $o['order_id'];
                                //get order information
                                $order->getOrder();
                                //a boolean to check if the order has a product of seller
                                $hasProduct = false;
                                //get all items from the order
                                $soldItems = $order->getSoldItems();
                                //for each item in this order
                                foreach ($soldItems as $i) {
                                    //set item id
                                    $item->item_id = $i['item_id'];
                                    //get item information
                                    $item->getItem();
                                    //if the item seller equals seller's id then set hasProduct to true
                                    if ($item->seller_id == $_SESSION['id']) {
                                        $hasProduct = true;
                                    }
                                }

                                //if the order has a product of seller then print order information
                                if ($hasProduct) {
                                    echo "<tr>";
                                    echo "<td>$order->order_id</td>";
                                    echo "<td>$order->order_time</td>";
                                    echo "<td><a href='orderDetail.php?id=$order->order_id'><i class=\"fas fa - trash text - danger\"></i>View</a></td>";
                                    echo "</tr>";
                                }
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
<?php
include "dashboard_logoutModal.php";
include "dashboard_footer.php";
?>


