<?php
//add header and side bar
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

$user = new User($conn); // Connection comes from dashboard header.
$user->user_id = $_SESSION['id'];
$user->getUser();
if ($user->type !== 'admin') { // Make sure the user is an admin.
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    echo 'You do not have permission to access this page.';
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET["id"]) || !isset($_GET["type"]) || (empty($_GET['id'] && $_GET['id'] != 0)) || empty($_GET['type'])) {
        http_response_code(Response::$BAD_REQUEST); // Bad request.
        echo 'ID and type are required.';
        exit;
    } else {
        if ($_GET["type"] == 'delete') {
            $user = new User($conn);
            $user->user_id = $_GET["id"];
            $user->deleteUserFromSellerApllyList();
            header("Location: acceptApplying.php");
        } else {
            $user = new User($conn);
            $user->user_id = $_GET["id"];
            $user->type = "seller";
            $user->changeUserRole();
            $user->deleteUserFromSellerApllyList();
            header("Location: acceptApplying.php");
        }
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
                <h1 class="h3 mb-2 text-gray-800">Apply To Be Seller List</h1>
                <p></p>
                <!-- Data Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary text-center">Apply To Be Seller List</h6>
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
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //create a user obj
                                $user = new User($conn);
                                //get a array of users who applied to be a seller in 'request_seller' table
                                $users = $user->displayAppliedUser();
                                //for each user in this array
                                foreach ($users as $u) {
                                    //set the user obj's id
                                    $user->user_id = $u['user_id'];
                                    //get the user information with the id
                                    $user->getUser();
                                    //print out all the information of the user
                                    echo "<tr>
                                <td>$user->user_id</td>
                                <td>$user->email</td>
                                <td>$user->first_name&nbsp;$user->last_name</td>
                                <td>$user->type &nbsp;&nbsp;&nbsp;
                                <!--accept user to be seller-->
                                <a href='acceptApplying.php?id=$user->user_id&type=accept'><i class=\"fas fa-edit text-primary\"></i>Accept&nbsp;&nbsp&nbsp;&nbsp</a>
                                <!--reject and delete this request-->
                                <a href='acceptApplying.php?id=$user->user_id&type=delete'><i class=\"fas fa-trash text-danger\"></i>Delete</a>
                                </td>
                      </tr>
                      ";
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
//when logout ask user to comfirm
include "dashboard_logoutModal.php";
include "dashboard_footer.php";
?>