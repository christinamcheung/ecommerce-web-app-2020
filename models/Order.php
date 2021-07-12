<?php

class Order
{
    /**
     * The table gonna be searched from database
     * @var string
     */
    private $table = 'orders';

    /**
     * The table gonna be searched from database
     * @var string
     */
    private $sold = 'sold_items';

    /**
     * Database connection for the class to utilise.
     * @var PDO object
     */
    private $conn;

    /**
     * The ID of this order in the database.
     * @var int
     */
    public $order_id;

    /**
     * The time of this order in the database.
     * @var timestamp
     */
    public $order_time;

    /**
     * The ID of the user in the database.
     * @var int
     */
    public $user_id;

    /**
     * The ID of this item in the database.
     * @var int
     */
    public $item_id;

    /**
     * The Shipping info of this order in the database.
     * @var String
     */
    public $shipping_info;


    /**
     * Order constructor.
     * @param $conn Database connection for the class to utilise.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
    }

    /** Function to add new order to the 'orders' table and add shipping information to 'shipping_information' and items to 'sold_items'
     *
     * @return bool|null Whether there was a error when connecting to the database and could find all tables
     */
    public function addToOrder()
    {
        $query = "INSERT INTO $this->table (user_id, shipping_info) VALUES (:user_id, :shipping_info)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":shipping_info", $this->shipping_info);
        try {
            $stmt->execute();
            $new_order = $this->conn->lastInsertId();
            $this->order_id = $new_order;
            $cart = new Cart($this->conn);
            $cart->user_id = $this->user_id;
            $query = "INSERT INTO $this->sold VALUES (:item_id, :order_id, :quantity)";
            foreach ($cart->getItems() as $product) {
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":item_id", $product["item_id"]);
                $stmt->bindParam(":order_id", $new_order);
                $stmt->bindParam(":quantity", $product["quantity"]);
                $stmt->execute();
                $cart->item_id = $product["item_id"];
                $cart->quantity = 0;
                $cart->updateItem();
                $item = new Item($this->conn);
                $item->item_id = $product['item_id'];
                $item->getItem();
                $item->stock -= $product['quantity'];
                $item->update();
            }
            $this->order_id = $new_order;
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }

    /**
     * This function is get all orders from 'orders' table
     *
     * @return bool|null Whether the order details are correct, an error occurred during database interaction.
     */
    public function getOrders()
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
     * This function is get a order from 'orders' table from a specific order number
     *
     * @return bool|null Whether the order details are correct, an error occurred during database interaction.
     */
    public function getOrder()
    {
        $query = "SELECT * FROM $this->table WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);
        try {
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $stmt->bindColumn('user_id', $this->user_id);
                $stmt->bindColumn('order_time', $this->order_time);
                $stmt->bindColumn('shipping_info', $this->shipping_info);
                $stmt->fetch(PDO::FETCH_BOUND);
                return false;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is get a order from 'orders' table from a specific User ID
     *
     * @return bool|null Whether the order details are correct, an error occurred during database interaction.
     */
    public function getOrdersForUser() {
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id";
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
     * This function is get a item from 'Items' table from a specific Seller ID
     *
     * @return bool|null Whether the order details are correct, an error occurred during database interaction.
     */
    public function isOwnedByUser() {
        $query = "SELECT * FROM $this->table WHERE order_id = :order_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":user_id", $this->user_id);

        try {
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for get the Item_ID from Order_ID in 'sold_items' table
     *
     * @return false|string|null When invalid json is found, The result of the query, When an error occurs with the database.
     */
    public function getSoldItems()
    {
        $query = "SELECT item_id, order_id, quantity FROM sold_items WHERE order_id = :order_id ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $this->order_id);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for delete a order from order id in 'orders table'
     * @return bool|null Whether the order details were found, an error occurred during database interaction.
     */
    public function deleteOrder()
    {
        $query = "DELETE FROM sold_items WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);
        try {
            $stmt->execute();
            $query = "DELETE FROM orders WHERE order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $this->order_id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is for check a order from order weather it is exists
     *
     * @return bool|null Whether the order details were found, an error occurred during database interaction.
     */
    public function exists() {
        $query = "SELECT * FROM $this->table WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $this->order_id);

        try {
            $stmt->execute();
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }
}