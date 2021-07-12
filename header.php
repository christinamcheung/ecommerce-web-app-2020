<!-- Header and navigation bar for the whole site -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">Manga Store</a>
        <form class="input-group-append col-md-8 col-sm-3 col-xs-3" action="search.php" method="POST" style="margin-bottom:0;">
            <input class="col-" id="form-control" value="<?php if (isset($value)) {
                echo htmlspecialchars($value);
            } ?>" name='search' type="text" placeholder="Search">
            <button class="btn btn-search" id="#search" type="submit">
                <svg id="search-img" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                    <path d="M0 0h24v24H0V0z" fill="none"/>
                    <path d="M15.5 14h-.79l-.28-.27c1.2-1.4 1.82-3.31 1.48-5.34-.47-2.78-2.79-5-5.59-5.34-4.23-.52-7.79 3.04-7.27 7.27.34 2.8 2.56 5.12 5.34 5.59 2.03.34 3.94-.28 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </button>
        </form>
        <div class="col-" id="navbarResponsive">
            <?php
            require_once "config/Database.php";
            require_once "models/User.php";

            // if logged in show specific buttons on navigation bar
            if (isset($_SESSION['Logged']) && $_SESSION['Logged'] == true) {
                $db = new Database();
                $user = new User($db->connect());
                $user->user_id = $_SESSION["id"];
                $user->getUser(); // Get user details to use in the header.
                echo "
                <div class=\"btn-group \">
                    <button type=\"button\" class=\"btn btn-primary col- dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    Hi, $user->first_name
                    </button>
                    <div class=\"dropdown-menu\">
                        <a  class=\"dropdown-item text-black-50\" href=\"library.php\">Library</a>
                        <a class=\"dropdown-item text-black-50\" href=\"orderHistory.php\">Order History</a>
                        <a  class=\"dropdown-item text-black-50\" href=\"wishlist.php\">Wishlist</a>
                        <a  class=\"dropdown-item text-black-50\" href=\"cart.php\">Cart</a>";

                if ($user->type != "consumer") {
                    echo "<a  class=\"dropdown-item text-black-50\" href=\"dashboard/index.php\">Dashboard</a>                         
                                                ";
                };
                if ($user->type == "consumer") {
                    echo "<a  class=\"dropdown-item text-black-50\" href=\"applyBecomeSeller.php\">Apply To Be Seller</a>";
                };

                echo "
                        <div class=\"dropdown-divider\"></div>
                        <a class=\"dropdown-item text-black-50\" href=\"logout.php\">Logout</a>
                    </div>
                </div>";
            } //otherwise show login and sign up buttons
            else {
                echo "<a class=\"nav-item\" style=\"margin-left:.5rem\" href=\"login.php\">Login</a>
                      <a class=\"nav-item\" style=\"margin-left:.5rem\" href=\"signup.php\">Sign Up</a>";
            }
            ?>
        </div>
    </div>
</nav>
