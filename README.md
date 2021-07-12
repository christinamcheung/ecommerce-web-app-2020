# Web Project

Group project for CSCI 3172 - Web-Centric Computing

Manga Store Site: https://web.cs.dal.ca/~popoola/project/

```
# LOGIN DETAILS:

Admin account test: 

    ---> email: admin@manga.com
    ---> pw: admin

Seller account test:

    ---> email: seller@manga.com
    ---> pw: seller

Consumer user account test:

    ---> email: user@manga.com
    ---> pw: user
```

# PAGES: 

LOGIN: Enter email and password from an associated account. This will grant access to the site.

SIGNUP: Sign up for a new account. Enter details. Details submitted to the Database, and login page loaded.

INDEX: Main page which includes the catalogue of products available. Product cards have buttons to view the product page or buy the product.

    ---> Best sellers shows most ordered products.
    ---> Search bar in header will allow to search for products.

SEARCH: Displays items which match the search term.

PAGE: This page displays the information for a product. From here the user can add the product to their cart.

CART: This page shows all items that have been added to the users cart. Here you can remove items or checkout.

CHECKOUT: Enter your shipping information and click on the PayPal button to purchase an order.

CONFIRM: Confirms an order was recieved and prints the order summary.

ORDER HISTORY: Displays all orders previously placed by the user.

HEADER: Page header carried across all pages in the site.
--------

# Dashboard: 
   # Shared widget:
   DASHBOARD_HEADER: DASHBOARD_HEADER is the header of the dashboard that carried across all pages in the dashboard.
   DASHBOARD_HEADER: DASHBOARD_FOOTER is the footer of the dashboard that carried across all pages in the dashboard.
   DASHBOARD_SIDEBAR: DASHBOARD_SIDEBAR is the side navigation bar of the dashboard that carried across all pages in the dashboard.
   DASHBOARD_TOPBAR: DASHBOARD_TOPBAR is the top bar of the dashboard that carried across all pages in the dashboard.

   # Admin pages:
   1.ACCOUNTMANAGE: This page belong to user who's type is 'admin', it display all the information of the account in the database.
            includes : 
                - user information
                - Edit button: it direct to USERROLECHANGE page, as a 'admin' can change user type among ('admin'|'seller'|'consumer')
                - Delete button: it calls DELETECONFIRM page, as a 'admin' can delete a user from database
                
   2.SEARCHUSER: This page belong to user who's type is 'admin', after admin typed in a user ID it will display the information of the user
            includes : 
                - user information from searched ID
                - 'Edit' button: it direct to USERROLECHANGE page, as a 'admin' can change user type among ('admin'|'seller'|'consumer')
                - 'Delete' button: it calls DELETECONFIRM page, as a 'admin' can delete a user from database
                
   3.SELLERMANAGEMENT: This page belong to user who's type is 'admin', it displayed all the users who is a 'seller'
            includes:
                - seller information
                - 'Change to consumer' button: it can change the user type to consumer, and remove it from seller list
                
   4.ACCEPTAPPLYING: This page belong to user who's type is 'admin', it displayed a list of request that user applied to be a seller
            includes:
                - applied user information
                - 'accept' button: it will change the user type to seller
                - 'cancel' button: it will remove the request from the request to be seller list
                
   5.DISPLAYALLORDERS: This page belong to user who's type is 'admin', it displays all the orders in the database
            includes:
                - the information of the order
                - 'view' button: it will direct to ORDERDETAIL page, and displays all the items from this order
                - 'delete' button: it will delete this order from the database

   6.SEARCHORDER: This page belong to user who's type is 'admin', it will display a specific order information after input a order ID
            includes:
                - the information of the order
                - 'view' button: it will direct to ORDERDETAIL page, and displays all the items from this order
                - 'delete' button: it will delete this order from the database
                
                
                
   # Seller pages:
   1.MYITEMS: This page belong to user who's type is 'seller', it display all the item belong to logged seller
            includes:
                - the id of the item
                                - the name of the item
                                - 'view' button: it will direct to ITEMMANAGE page, and displays all the information of the item
                        - as a seller could edit the information of the item, after click on the 'update' button, the information of the item will be updated
                                - 'delete' button: it will delete this item from the database


   2.ADDITEM: This page contain a form that seller could create a new item after fill in the information of the item and click on 'submit'
               includes:
                   - a form to fill in the information of the item
                                      - 'submit' button: it will add the new item into database

   3.ORDEROFSELLER: This page will display all the orders that contain at least one product that from the logged seller
                  includes:
                      - the order id and order information
                      - 'view' button: it will direct to orderDetail page that displays all the information of the items that belong to logged seller



​            

​        






