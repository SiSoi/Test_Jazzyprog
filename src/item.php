<?php
# Including class files
require_once "./lib/Admin.php";
require_once "./lib/Book.php";
require_once "./lib/Page.php";

use lib\Admin, lib\Book, lib\Page;


# Starting/restoring a session
session_start();
if (isset($_SESSION["CUR_URI"]))
    $_SESSION["PREV_URI"] = $_SESSION["CUR_URI"];
$_SESSION["CUR_URI"] = $_SERVER["REQUEST_URI"];
if (isset($_SESSION["ITEM_TO_UPDATE"]))
{
    $_SESSION["ITEM_TO_UPDATE"] = array();
    unset($_SESSION["ITEM_TO_UPDATE"]);
}

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
    {
        $book = Book::get_book($conn, $_GET["id"]);
        if (isset($_SESSION["USER"]) &&$_SESSION["USER"]["group"]==="adm")
        {
            $adm = new Admin($_SESSION["USER"]["username"]);
            $item_time = $adm->get_item_time($conn, $_GET["id"]);
            $img_time = $adm->get_img_time($conn, $_GET["id"]);
        }
    }
# else sending error page?, $book = FALSE?

# Closing the database connection
$conn->close();


# Sending response
if (isset($book) && $book) //and the last item if you can do that
{
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
    Page::load_breadcrumb("item", $book);

    # ------------------------Main content------------------------

    # Making a container for the main content
    echo "<div class='container'>\n";

    # The first row contains an image of the content and add-to-cart field
    echo "\t<div class='row'>\n";

    # Showing the image
    if (isset($img_time))
        Page::item_show_image($book, $img_time);
    else
        Page::item_show_image($book);

    # Guest/user interface
    if (!isset($_SESSION["USER"]) || $_SESSION["USER"]["group"]==="usr")
    {
        # Showing the add-to-cart field
        Page::item_show_add_to_cart($book);
    }

    # Admin interface
    if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="adm" && isset($item_time))
    {
        # Show details of the item
        Page::item_show_details($book, $item_time);
    }

    # Marking the end of the first row
    echo "\t</div>\n";

    # The second row contains a description for the item
    echo "\t<div class='row'>\n";

    # Showing the description
    Page::item_show_description($book);

    # Marking the end of the second row
    echo "\t</div>\n";

    # Marking the end of the container
    echo "</div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";


    # Cleaning up the data
    $book = array();
    $item_time = array();
    $img_time = array();
    unset($book, $item_time, $img_time);

    session_commit();

    # Terminating the execution of the script
    exit();
}
else
{
    # Cleaning up the data
    unset($book);

    session_commit();

    # Here send server error
    die();
}