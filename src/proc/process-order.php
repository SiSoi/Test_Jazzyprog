<?php
# Including class files
require_once "../lib/User.php";
require_once "../lib/Order.php";

use lib\User, lib\Order;

# Starting/restoring a session
session_start();

if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="usr")
{
    # Creating connection to the database
    $conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
    if (mysqli_connect_errno())
    {
        #send_server_error page
        #return to home page
        die();
    }

    # Connecting to the database and inserting data into the database
    $user_id = User::find_id($conn, $_SESSION["USER"]["username"]);
    $flag_order = false;
    if ($user_id > 0)
    {
        $order_id = Order::add_order_payment($conn, $user_id);
        $flag_order = Order::add_order_list($conn, $order_id);
    }

    # Closing the database connection
    $conn->close();

    # Checking if request was handled successfully
    if ($flag_order===false)
    {
        #handle error page
        die();
    }
    else
    {
        # Cleaning session data for the cart
        $_SESSION["CART"] = array();
        unset($_SESSION["CART"]);

        # Cleaning variables
        unset($user_id, $order_id, $flag_order);

        echo "<p>Success</p>";

        # Redirecting back to the main page (this shame is due to no basic background on JS)
        http_response_code(302);
        header("refresh: 5; url=http://localhost:63342/Test/store.php");
    }
}
else
{
    # Redirecting back to the login page (this shame is due to no basic background on JS)
    http_response_code(302);
    header("Location: http://localhost:63342/Test/login.php");
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();