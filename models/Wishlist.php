<?php

class Wishlist
{
    /**
     * The table gonna be searched from database
     * @var string
     */
    private $table = 'wishlist_items';

    /**
     * Database connection for the class to utilise.
     * @var PDO object
     */
    private $conn;

    /**
     * The user the wishlist belongs to.
     * @var int
     */
    public $user_id;

    /**
     * The current item being modified in the wishlist.
     * @var int
     */
    public $item_id;

    /**
     * Wishlist constructor.
     * @param $conn Database connection for the class to utilise.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Adds an item to the wishlist.
     * @return bool|null Whether the add was successful, null on database error.
     */
    public function addItem()
    {
        $query = "INSERT INTO $this->table (user_id, item_id) VALUES (:user_id, :item_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":item_id", $this->item_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public static function containsItem($conn, $user_id, $item_id) {
        $query = "SELECT * FROM wishlist_items WHERE user_id = :user_id AND item_id = :item_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":item_id", $item_id);

        try {
            $stmt->execute();
            return $stmt->rowCount() == 1;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieves all items in a given user's wishlist.
     * @return array|null The result of the query, When an error occurs with the database.
     */
    public function getItems()
    {
        $query = "SELECT w.item_id, i.name, i.price FROM $this->table w, items i WHERE user_id = :user_id AND i.item_id = w.item_id";
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
     * Removes an item from a given user's wishlist.
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