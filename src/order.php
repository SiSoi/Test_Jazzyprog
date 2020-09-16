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
    if (isset($_GET["id"]))
        $order_list = User::get_order_list($conn, $_GET["id"], $_SESSION["USER"]["username"]);
    # else sending error page?, $order_list = FALSE?

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
    Page::load_breadcrumb("order");

    # ------------------------Main content------------------------
    # Making a container for the main content
    echo "<div class='container mb-4'>\n";

    # Printing the main content
    if (isset($order_list) && $order_list)
    {
        echo "<div class='row'>\n";
        Page::order_list_items($order_list);
        echo "</div>\n";
    }
    else
    {
        echo file_get_contents("./html/form-order-search.html");
        if ($_GET["id"])
        {
            echo "<br><div class='row'><div class='col text-center'>\n";
            echo "<p style='font-size: 1.5rem; font-style: italic; font-weight: bold;'>No order found.</p>\n";
            echo "</div></div>\n";
        }
    }

    # Marking the end of the container
    echo "</div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";

    # Cleaning up the data
    if (isset($order_list) && $order_list)
    {
        foreach ($order_list as $item)
        {
            foreach ($item as $key => $value)
            {
                unset($item[$key]);
            }
            unset($item);
        }
    }
    unset($order_list);
}
else
{
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}


session_commit();

# Terminating the execution of the script
exit();