<?php
require_once 'config/Database.php';
require_once 'models/User.php';
require 'util/Response.php';

session_set_cookie_params("Session", "/", null, true, true);
session_name("MANGALOGIN");
session_start();

// If the user is already logged in, take them to the homepage.
if (isset($_SESSION['Logged']) && $_SESSION['Logged'] == true) {
    http_response_code(Response::$UNAUTHORIZED); // Unauthorized.
    header("Location: index.php");
    exit;
}

$verify_value = null;

$first_name_error = null;
$last_name_error = null;
$email_error = null;
$password_error = null;
$verify_error = null;

$form_submitted = $_SERVER["REQUEST_METHOD"] == "POST";
$db = new Database();
$user = new User($db->connect());

// Check if the the form been submitted.
if ($form_submitted) {
    if (!isset($_POST['first_name']) || empty($_POST['first_name'])) { // Check first name was entered.
        $first_name_error = "Required";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $user->first_name = $_POST['first_name'];
    }

    if (!isset($_POST['last_name']) || empty($_POST['last_name'])) { // Check last name was entered.
        $last_name_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $user->last_name = $_POST['last_name'];
    }

    if (!isset($_POST['email']) || empty($_POST['email'])) { // Check email was entered.
        $email_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $user->email = $_POST['email'];
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) { // Check email is valid.
            $email_error = "Invalid email address provided.";
            http_response_code(Response::$BAD_REQUEST);
        } else if ($user->existsByEmail()) { // Check email is not taken.
            $email_error = "This email is already in use.";
            http_response_code(Response::$CONFLICT);
        }

    }

    if (!isset($_POST['password']) || empty($_POST['password'])) { // Check password was entered.
        $password_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $user->password = $_POST['password'];
        // Check password has 14 charcters minimum, 1 uppercase and lowecase and symbol.
        if (!preg_match("\"^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{14,})\"", $user->password)) {
            $password_error = "Must meet the strength requirement.";
            http_response_code(Response::$BAD_REQUEST);
        }
    }

    if (!isset($_POST['verify']) || empty($_POST['verify'])) { // Check verify password was entered.
        $verify_error = "Required.";
        http_response_code(Response::$BAD_REQUEST);
    } else {
        $verify_value = $_POST['verify'];
        if ($verify_value != $user->password) { // Check verify is the same as the given password.
            $verify_error = "Does not match the entered password.";
        }
    }

    // If no errors, register the user.
    if ($first_name_error == null && $last_name_error == null && $email_error == null && $password_error == null && $verify_error == null) {
        $user->type = "consumer";
        // Create a new account
        if ($user->create() != null) {
            $_SESSION['id'] = $user->user_id;
            $_SESSION['userType'] = $user->type;
            $_SESSION['user_first_name'] = $user->first_name;
            $_SESSION['user_last_name'] = $user->last_name;
            $_SESSION['Logged'] = true;
            header("Location: index.php");
        } else {
            http_response_code(Response::$INTERNAL_SERVER_ERROR); // Server error.
            $errorMsg = "Something went wrong on our end.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp Page</title>
    <!--Import Stylesheets-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" media="screen"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
</head>

<body>
<?php require "header.php"; ?>

<div class="page-container">
    <h2>Sign Up</h2>

    <form class="forms" action="signup.php" method="post">
        <div id="form-inputs">
            <div id="left-forms">
                <div class="form-group">
                    <div id="firstname">
                        <label for="InputFirstName">First Name</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $first_name_error != null ? 'is-invalid' : 'is-valid'?>" id="InputFirstName" name="first_name"
                               placeholder="First name" value="<?php echo $user->first_name?>" required>
                        <div class="invalid-feedback">
                            <?php echo $first_name_error?>
                        </div>
                    </div>
                    <div id="lastname">
                        <label for="InputLastName">Last Name</label>
                        <input type="text" class="form-control <?php if ($form_submitted) echo $last_name_error != null ? 'is-invalid' : 'is-valid'?>" id="InputLastName" name="last_name"
                               placeholder="Last name" value="<?php echo $user->last_name?>" required>
                        <div class="invalid-feedback">
                            <?php echo $last_name_error?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div id="email">
                        <label for="InputEmail">Email address</label>
                        <input type="email" class="form-control <?php if ($form_submitted) echo $email_error != null ? 'is-invalid' : 'is-valid'?>" id="InputEmail" name="email"
                               aria-describedby="emailHelp" placeholder="Email" value="<?php echo $user->email?>" required>
                        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                            else.</small>
                        <div class="invalid-feedback">
                            <?php echo $email_error?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div id="password">
                        <label for="InputPassword">Password</label>
                        <input type="password" class="form-control <?php if ($form_submitted) echo $password_error != null ? 'is-invalid' : 'is-valid'?>" id="InputPassword" name="password"
                               placeholder="Password" value="<?php echo $user->password?>" required>
                        <meter max="4" id="password-strength-meter"></meter>
                        <p id="password-strength-text" aria-describedby="strength-help"></p>
                        <small id="strengthHelp" class="form-text text-muted">Password must contain 1 uppercase/lowercase letter, a symbol, and be 14 or more characters.
                        </small>
                        <div class="invalid-feedback">
                            <?php echo $password_error?>
                        </div>
                    </div>
                    <div id="verify">
                        <label for="InputVerifyPassword">Verify Password</label>
                        <input type="password" class="form-control <?php if ($form_submitted) echo $verify_error != null  || $password_error != null ? 'is-invalid' : 'is-valid'?>" id="InputVerifyPassword" name="verify"
                               placeholder="Re-enter Password" value="<?php echo $verify_value?>" required>
                        <div class="invalid-feedback">
                            <?php echo $verify_error?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" id="signup-button">Submit</button>
        <p class="text-danger"><?php if (isset($errorMsg)) echo $errorMsg;?></p>
    </form>
</div>

<!--JavaScript at end of body for optimized loading-->
<script src="js/jquery-3.4.1.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--CDN Link for Password Strength Checker Tool-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
<script src="js/strength.js"></script>
</body>
</html>