<?php
# Including class files
require_once "./lib/User.php";
require_once "./lib/Page.php";

use lib\User, lib\Page;

#Open/restore a session
session_start();
if (isset($_SESSION["CUR_URI"]))
    $_SESSION["PREV_URI"] = $_SESSION["CUR_URI"];
$_SESSION["CUR_URI"] = $_SERVER["REQUEST_URI"];


if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="usr")
{
    # Creating connection to the database
    $conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
    if (mysqli_connect_errno())
    {
        # Here send server error
        die();
    }

    # Processing HTTP GET requests, reload the whole page for every GET request
    # To reload only a part of a page, need to know JS
    $orders = User::get_order_payment($conn, $_SESSION["USER"]["username"]);
    # else sending error page?, $orders = FALSE?

    # Closing the database connection
    $conn->close();


    # Sending response
    # STATUS CODE: 200 "OK"

    # -------------------------HTML START-------------------------
    echo "<!DOCTYPE html>\n";
    echo "<html lang='en'>\n";

    # ----------------------------HEAD----------------------------
    echo file_get_contents("./html/head.html");

    # ----------------------------BODY----------------------------
    echo "<body>\n";

    # ---------------------------Header---------------------------
    Page::load_header();

    # ---------------------------Banner---------------------------
    echo file_get_contents("./html/banner.html");

    # -------------------------Breadcrumb-------------------------
    Page::load_breadcrumb("history");

    # ------------------------Main content------------------------
    # Making a container for the main content
    echo "<div class='container mb-4'><div class='row'>\n";

    # Printing the main content
    if ($orders)
    {
        Page::history_list_orders($orders);
    }
    if ($orders === 0)
    {
        Page::cart_show_empty();
    }

    # Marking the end of the container
    echo "</div></div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";

    # Cleaning up the data
    if ($orders)
    {
        foreach ($orders as $order)
        {
            foreach ($order as $key => $value)
            {
                unset($order[$key]);
            }
            unset($order);
        }
    }
    unset($orders);
}
else
{
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}


session_commit();

# Terminating the execution of the script
exit();