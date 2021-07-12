<?php

class User
{
    /**
     * The table gonna be searched from database
     * @var string
     */
    private $table = 'users';

    /**
     * Database connection for the class to utilise.
     * @var PDO object
     */
    private $conn;

    /**
     * The ID of this user in the database.
     * @var int
     */
    public $user_id;

    /**
     * The forename of the user.
     * @var string
     */
    public $first_name;

    /**
     * The surname of the user.
     * @var string
     */
    public $last_name;

    /**
     * The email of the user.
     * @var string
     */
    public $email;

    /**
     * The password of the user.
     * @var string
     */
    public $password;

    /**
     * The type of user.
     * @var string
     */
    public $type;

    /**
     * The Users IP address
     * @var string
     */
    public $ip;

    /**
     * User constructor.
     * @param $conn Database connection for the class to utilise.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Creates a user.
     * Handles the job adding a user to the database.
     * @return int|null The ID of the created user, Indicates an error creating the user, usually because the user exists.
     */
    public function create()
    {
        $query = "INSERT INTO $this->table (first_name, last_name, email, password, type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute(array($this->first_name, $this->last_name, $this->email, password_hash($this->password, PASSWORD_DEFAULT), $this->type));
            $this->user_id = $this->conn->lastInsertId(); // Get the ID of the new user.
            return $this->user_id;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * Checks whether a user exists.
     *
     * Uses the users email to check their existence.
     *
     * @return bool|null Whether the user exists, an error occurred during database interaction.
     */
    public function existsByEmail()
    {
        $query = "SELECT email FROM $this->table WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute(array($this->email));
            return ($stmt->rowCount() > 0); // See whether any users where found.
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * Checks whether a user exists.
     *
     * Uses the users ID to check their existence.
     *
     * @return bool|null Whether the user exists, an error occurred during database interaction.
     */
    public function existsById()
    {
        $query = "SELECT user_id FROM $this->table WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute(array($this->user_id));
            return ($stmt->rowCount() > 0); // See whether any users where found.
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * Checks whether a set of credentials is correct.
     *
     * Uses the given email and password to check if the provided login details are correct.
     *
     * @return bool|null Whether the users details are correct, an error occurred during database interaction.
     */
    public function checkLogin()
    {
        $query = "SELECT user_id, email, password  FROM $this->table WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute(array($this->email));
            if ($stmt->rowCount() == 1) {
                $stmt->bindColumn('password', $hashed_password); // Extract the hashed password of the found user.
                $stmt->bindColumn('user_id', $user_id);
                $stmt->fetch(PDO::FETCH_BOUND);
                // Check the given password matches the found password.
                if (password_verify($this->password, $hashed_password)) {
                    $this->user_id = $user_id;
                    return true;
                }
                return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for get user object.
     *
     * Can get a specific user with its ID and set all the user information from database
     *
     * @return bool|null Whether the user details were found, an error occurred during database interaction.
     */
    public function getUser()
    {
        $query = "SELECT first_name, last_name, email, type FROM $this->table WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        try {
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $stmt->bindColumn('first_name', $this->first_name);
                $stmt->bindColumn('last_name', $this->last_name);
                $stmt->bindColumn('email', $this->email);
                $stmt->bindColumn('type', $this->type);
                $stmt->fetch(PDO::FETCH_BOUND);
                return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * This function gets all users from database with all the information
     *
     * data are saved in array
     *
     * @return array|null All users, an error occurred during database interaction.
     */
    public function getUsers()
    {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for delete user from admin's dashboard from specific ID
     *
     * User can be delete from Account management Table
     *
     *@return bool|null Whether the users ID is correct, an error occurred during database interaction.
     */
    public function deleteUser()
    {
        $query = "DELETE FROM $this->table WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for user to apply to be a seller
     *
     * After user click on the apply to be a seller it will send the request to 'seller_requests' table
     *
     * @return bool|null Whether the users ID is not correct, an error occurred during database interaction.
     */
    public function applyToBeSeller()
    {
        $query = "SELECT user_id FROM seller_requests WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        try {
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                $query = "INSERT INTO seller_requests(user_id) VALUES (:user_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $this->user_id);
                $stmt->execute();
                return true;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is display users who applied to be seller
     *
     * After user click on the apply to be a seller it will be
     * displayed in "Apply To Be Seller List" page in dashboard
     *
     * @return bool|null Whether the users details is not correct, an error occurred during database interaction.
     */
    public function displayAppliedUser()
    {
        $query = "SELECT * FROM seller_requests";
        $stmt = $this->conn->prepare($query);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * This function is delete records from seller_requests list
     *
     * After click on the "delete" button on the acceptApplying page
     *
     * Records will be deleted from database
     *
     * @return bool|null Whether the users ID is not correct, an error occurred during database interaction.
     */
    public function deleteUserFromSellerApllyList()
    {
        $query = "DELETE FROM seller_requests WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * This function is for changing user role from user ID
     *
     * could be change to 'admin' 'consumer' 'seller'
     *
     *
     * @return bool|null Whether the users ID is not correct, an error occurred during database interaction.
     */
    public function changeUserRole()
    {
        $query = "UPDATE users SET type = '$this->type' WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for getting all the items sold by a specific user
     *
     *
     * @return bool|null Whether the users ID is not correct, an error occurred during database interaction.
     */
    public function getItems()
    {
        $query = "SELECT * FROM items WHERE seller_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * This function adds the users IP to a table of addresses to measure login attempts
     * 
     * 
     * @return bool|null True if login attempts has reached limit
     */
    public function lockAccount()
    {
        $query = "INSERT INTO `ip` (`address` ,`timestamp`)VALUES ('$this->ip',CURRENT_TIMESTAMP)";
        $stmt = $this->conn->prepare($query);
        try{
            $stmt->execute();
        } catch (PDOException $e){
            return null;
        }

        $result = "SELECT COUNT(*) FROM `ip` WHERE `address` LIKE '$this->ip' AND `timestamp` > (now() - interval 10 minute)";
        $stmt = $this->conn->prepare($result);
        try{
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_NUM);

            if($count[0]>3){
                return true;
            }
            else{
                return false;
            }
        } catch (PDOException $e){
            return null;
        }
    }

    /**
     *This function removes the ip address and unlocks the account
     *
     * 
     * @return bool|null True if completed
     */
    public function unlockAccount(){
        $query = "DELETE FROM `ip` WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $stmt = $this->conn->prepare($query);
        try{
            $stmt->execute();
            return true;
        } catch (PDOException $e){
            return false;
        }
    }
}
