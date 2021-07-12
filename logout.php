<!-- Log out function -->
<?php
session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();
unset($_SESSION);
session_destroy();
header("Location: login.php");
exit;
