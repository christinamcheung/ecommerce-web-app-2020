<?php
require_once "dashboard_header.php";
require_once "dashboard_sidebar.php";
require_once '../util/Response.php';

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
            <h1 class="h3 mb-2 text-gray-800">Search User</h1>
            <p></p>
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">Accounts Table</h6>
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
                            $user = new User($conn);

                            // If user has search, show results.
                            if (isset($_POST["searchBox"]) && !empty($_POST["searchBox"])) {
                                $user->user_id = $_POST["searchBox"];
                                $user->getUser();

                                echo "
                                    <td>$user->user_id</td>
                                    <!-- Delete Modal-->
                                    <div class=\"modal fade\" id=\"deleteConfirm$user->user_id\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
                                        <div class=\"modal-dialog\" role=\"document\">
                                            <div class=\"modal-content\">
                                                <div class=\"modal-header\">
                                                    <h5 class=\"modal-title\" id=\"exampleModalLabel\">Do you want to delete it?</h5>
                                                    <button class=\"close\" type=\"button\" data-dismiss=\"modal\" aria-label=\"Close\">
                                                        <span aria-hidden=\"true\">×</span>
                                                    </button>
                                                </div>
                                                <div class=\"modal-body\">Select \"Delete\" below if you want to delete this account.</div>
                                                <div class=\"modal-footer\">
                                                    <button class=\"btn btn-secondary\" type=\"button\" data-dismiss=\"modal\">Cancel</button>
                                                    <a class=\"btn btn-primary\" href=\"deleteConfirm.php?id=$user->user_id\">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <td>$user->email</td>
                                    <td>$user->first_name&nbsp;$user->last_name</td>
                                    <td>$user->type</td> 
                                    <td>
                                        <a href='userRoleChange.php?id=$user->user_id'><i class=\"fas fa-edit text-primary\"></i>Edit&nbsp;&nbsp&nbsp;&nbsp</a>
                                        <a href='dashboard/accountManage.php?id=$user->user_id' data-toggle='modal' data-target='#deleteConfirm$user->user_id'><i class=\"fas fa-trash text-danger\"></i>Delete</a>
                                    </td>
                        </tr>
                        </tbody>
                        ";

                            }

                            ?>

                        </table>
                        <?php
                        echo " <div class=\"input-group mb-3\">
                                <form name=\"form\" action=\"searchUser.php\" method=\"post\">
                                    <div class=\"input-group-append\">
                                        <input type=\"text\" name=\"searchBox\" id=\"searchBox\" class=\"form-control\" placeholder=\"User ID\" aria-label=\"Order Number\" aria-describedby=\"button-addon2\">
                                        <button type=\"submit\" class=\"btn btn-outline-secondary bg-primary text-white\" id=\"button-addon2\">Search</button>
                                    </div>
                                </form>

                            </div>";
                        ?>

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





