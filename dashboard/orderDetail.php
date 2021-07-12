<?php
//includes the hearders
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php
        //display the topbar
        require_once "dashboard_topbar.php";
        ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">Order Detail</h1>
            <p></p>
            <!-- Data Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">Order Detail</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            //create a order obj
                            $order = new Order($conn);
                            //create a item obj
                            $item = new Item($conn);
                            //total price of a order
                            $total = 0;

                            //get the order id from url
                            if (isset($_GET["id"])) {
                                //set order's id from url
                                $order->order_id = $_GET["id"];
                                //get items from order
                                $soldItems = $order->getSoldItems();
                                //for each item in order
                                foreach ($soldItems as $i) {
                                    //print each item information
                                    $item->item_id = $i['item_id'];
                                    $quantity = $i['quantity'];
                                    $item->getItem();
                                    if ($user->type === 'seller' && $item->seller_id != $_SESSION['id']) {
                                        continue;
                                    }
                                    echo "<tr>";
                                    echo "<td>$item->item_id</td>";
                                    echo "<td>$item->name</td>";
                                    echo "<td>$item->price</td>";
                                    echo "<td>$quantity</td>";
                                    $total = $total + $item->price * $quantity;
                                }
                            }

                            echo "<tr><td colspan='4' class='text-center'>Total: $total</td></tr>"
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


