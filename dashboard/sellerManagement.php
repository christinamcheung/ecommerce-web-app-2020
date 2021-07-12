<?php
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

if ($user->type !== 'admin') { // Make sure the user is an admin.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
}

if (isset($_GET["id"]) && (!empty($_GET['id']) || $_GET['user_id'] == 0)) {
    $user = new User($db->connect());
    $user->user_id = $_GET["id"];
    if ($user->existsById()) { // If the specified user exists.
        $user->type = "consumer";
        $user->changeUserRole(); // Make them a consumer.
        header("Location: displayAllOrders.php");
    } else {
        http_response_code(Response::$NOT_FOUND); // Not Found.
        echo 'The specified user could not be found.';
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
                <h1 class="h3 mb-2 text-gray-800">Sellers Table</h1>
                <p></p>
                <!-- Data Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Sellers Table</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Email Address</th>
                                    <th>Name</th>
                                    <th>User Role</th>
                                    <th>Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //create a new user
                                $user = new User($conn);
                                //get all the users from db
                                $users = $user->getUsers();

                                //for each user in db
                                foreach ($users as $u) {
                                    //set user ID
                                    $user->user_id = $u['user_id'];
                                    //get user information
                                    $user->getUser();
                                    //if user is a seller then print out information
                                    if ($user->type == "seller") {
                                        echo "<tr>
                                <td>$user->user_id</td>
           
                                <td>$user->email</td>
                                <td>$user->first_name&nbsp;$user->last_name</td>
                                <td>$user->type &nbsp;&nbsp;&nbsp;</td>
                                <!-- button for change user back to consumer-->
                                <td>
                                    <a href='sellerManagement.php?id=$user->user_id'><i class=\"fas fa-edit text-primary\"></i>Change To Consumer&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                </td>
                      </tr>
                      ";
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
    </div>
<?php
include "dashboard_logoutModal.php";
include "dashboard_footer.php";
?>