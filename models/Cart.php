<?php

class Cart
{
    /**
     * The table gonna be searched from database
     * @var string
     */
    private $table = 'cart_items';

    /**
     * Database connection for the class to utilise.
     * @var PDO object
     */
    private $conn;

    /**
     * The user the cart belongs to.
     * @var int
     */
    public $user_id;

    /**
     * The current item being modified in the cart.
     * @var int
     */
    public $item_id;

    /**
     * The number of the current item that is in the cart.
     * @var int
     */
    public $quantity;

    /**
     * Cart constructor.
     * @param $conn Database connection for the class to utilise.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Updates item details for a given user's cart.
     * @return bool|null Whether the cart was updated successfully, When an error occurs with the database.
     */
    public function updateItem()
    {
        if ($this->quantity === 0) {
            return $this->deleteItem();
        } else {
            $query = "INSERT INTO $this->table (user_id, item_id, quantity) VALUES (:user_id, :item_id, :quantity) ON DUPLICATE KEY UPDATE quantity = :quantity";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":item_id", $this->item_id);
            $stmt->bindParam(":quantity", $this->quantity);

            try {
                $stmt->execute();
                return true;
            } catch (PDOException $e) {
                //echo $e->getMessage();
                return null;
            }
        }
    }

    /**
     * Adds an item to the cart, increments the quantity if already exists.
     * @return bool|null Whether the add was successful, null on database error.
     */
    public function addItem()
    {
        $query = "INSERT INTO $this->table (user_id, item_id, quantity) VALUES (:user_id, :item_id, :quantity) ON DUPLICATE KEY UPDATE quantity = quantity + $this->quantity";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":quantity", $this->quantity);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieves all items in a given user's cart.
     * @return false|string|null When invalid json is found, The result of the query, When an error occurs with the database.
     */
    public function getItems()
    {
        $query = "SELECT c.item_id, c.quantity, i.price * c.quantity as price FROM $this->table c, items i WHERE user_id = :user_id AND i.item_id = c.item_id";
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
     * Removes an item from a given user's cart.
     * @return bool|null Whether the item was removed or not, When an error occurs with the database.
     */
    public function deleteItem()
    {
        $query = "DELETE FROM $this->table WHERE user_id = :user_id AND item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":item_id", $this->item_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }
}