<?php

class Item
{
    /**
     * The table gonna be searched from database
     * @var string
     */
    private $table = 'items';

    /**
     * Database connection for the class to utilise.
     * @var PDO object
     */
    private $conn;

    /**
     * The ID of this item in the database.
     * @var int
     */
    public $item_id;

    /**
     * The seller_ID of this item in the database.
     * @var int
     */
    public $seller_id;

    /**
     * The ID of this order in the database.
     * @var int
     */
    public $name;

    /**
     * The author of this item in the database.
     * @var int
     */
    public $author;

    /**
     * The price of this item in the database.
     * @var int
     */
    public $price;

    /**
     * The stock of this item in the database.
     * @var int
     */
    public $stock;

    /**
     * The image of this item in the database.
     * @var int
     */
    public $image;

    /**
     * The description of this item in the database.
     * @var int
     */
    public $description;

    /**
     * The number of pages of this item in the database.
     * @var int
     */
    public $number_pages;

    /**
     * Boolean which indicates if the user owns the item
     * @var bool
     */
    public $owned;

    /**
     * Order constructor.
     * @param $conn Database connection for the class to utilise.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
    }


    /**
     * This function is add a time to "items" table
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function addItem()
    {
        $query = "INSERT INTO $this->table (seller_id, name, author, number_pages, price, stock, image, description) VALUES (:seller_id, :name, :author, :number_pages, :price, :stock, :image, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $this->seller_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':number_pages', $this->number_pages);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':description', $this->description);

        try {
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is get top 10 items has been sold the most from 'sold_items' table
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function getTopTen()
    {
        $query = "SELECT item_id, SUM(quantity) as total_ordered FROM sold_items GROUP BY item_id ORDER BY total_ordered DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is check if the item'id or item's name existed in the 'items' table
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function exists()
    {
        $query = "SELECT * FROM $this->table WHERE item_id = :item_id OR name = :name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":item_id", $this->item_id);
        $stmt->bindParam(":name", $this->name);

        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is delete a specific item from its item_id
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function deleteItem()
    {
        $query = "DELETE FROM $this->table WHERE item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":item_id", $this->item_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is get a item object from its item_id
     *
     * and set all the information to this item object
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function getItem()
    {
        $query = "SELECT item_id, seller_id, name, author, price, stock, image, description, number_pages FROM $this->table WHERE item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":item_id", $this->item_id);

        try {
            $stmt->execute();
            $stmt->bindColumn('seller_id', $this->seller_id);
            $stmt->bindColumn('name', $this->name);
            $stmt->bindColumn('author', $this->author);
            $stmt->bindColumn('price', $this->price);
            $stmt->bindColumn('stock', $this->stock);
            $stmt->bindColumn('image', $this->image);
            $stmt->bindColumn('description', $this->description);
            $stmt->bindColumn('number_pages', $this->number_pages);
            if ($stmt->rowCount() === 1) {
                $stmt->fetch(PDO::FETCH_BOUND);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return null;
        }
    }


    /**
     * This function is update the information of a specific item from its ID
     *
     * @return bool|null Whether the item details are correct, an error occurred during database interaction.
     */
    public function update()
    {
        $query = "UPDATE $this->table SET name = :name, author = :author, number_pages = :num_pages, price = :price, stock = :stock, image = :image, description = :description WHERE item_id = :item_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':num_pages', $this->number_pages);
        $stmt->bindParam(":item_id", $this->item_id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            // $e->getMessage();
            return null;
        }
    }

    public function getItems()
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

    public function db_construct($record)
    {
        $this->name = $record['name'];
        $this->image = $record['image'];
        $this->description = $record['description'];
        $this->item_id = $record['item_id'];
    }

    public function displayCard($item_added = 0, $added_to_wishlist = false)
    {
        $content = <<<EOD
        <div class="card col-xl-2 col-lg-2 col-md-3 col-sm-4 col-">
            <img src="data/product-images/{$this->image}" class="card-img-top" alt="{$this->name}" style="width: 100%;">
            <div class="card-body" style="padding-left: 0; padding-right:0;">
                <h6 class="card-title lead">{$this->name}</h6>
                <div class="buttons">
                    
EOD;
        if ($this->owned == True) {
            $content .= "<form action=\"read.php\" method=\"get\" style=\"margin-top: 20%;\">
                            <input type=\"hidden\" name=\"id\" value=\"$this->item_id\"/>
                            <button type=\"submit\" class=\"btn btn-primary\">Read Comic</button>
                        </form>";
        } else {
            $content .= "<form action=\"page.php?id={$this->item_id}\" method=\"get\">
                            <input type=\"hidden\" name=\"id\" value=\"$this->item_id\"/>
                            <button type=\"submit\" class=\"btn btn-primary\">View</button>
                        </form>";


            if (!Wishlist::containsItem($this->conn, $_SESSION['id'], $this->item_id)) {
                $content .= "<form action=\"index.php\" method=\"post\">
                            <input type=\"hidden\" name=\"item_id\" value=\"$this->item_id\"/>
                            <button type=\"submit\" name= \"add_to_wishlist_submit\" class=\"btn btn-primary\">Add to Wishlist</button>
                        </form>";
            }

            if ($this->stock > 0) {
                $content .= "<form action=\"cart.php\" method=\"post\">
                                <input type=\"hidden\" name=\"item_id\" value=\"$this->item_id\">
                                <button type=\"submit\" name='buy_now_submit' class=\"btn btn-primary\" style=\"left: 5rem;\">Buy Now</button>
                            </form>";
            }

            if ($added_to_wishlist && $this->item_id == $item_added) {
                $content .= "
                           <form>
                           <input type='hidden' class='is-valid' />
                            <div class='valid-feedback'>
                                Item added to Wishlist!
                            </div></form>";
            }
        }

        $content .= "</div></div></div>";
        echo $content;
    }
}
