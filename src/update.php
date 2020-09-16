<?php

# Including class files
require_once "./lib/Page.php";
require_once "./lib/Book.php";

use lib\Page, \lib\Book;


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

if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"] === "adm")
{
    # Creating connection to the database
    $conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
    if (mysqli_connect_errno())
    {
        # Here send server error
        die();
    }

    $list_category = Book::list_category($conn);
    if (isset($_GET["item_id"]))
    {
        if ($book = Book::get_book($conn, $_GET["item_id"]))
            # Creating temporary session data
            $_SESSION["ITEM_TO_UPDATE"] = $book;
    }
    # else sending error page?, $book = FALSE?

    # Closing the database connection
    $conn->close();


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

    # ------------------------Main content------------------------

    # Making a container for the main content
    echo "<div class='container'><div class='row'>\n";

    # Main content
    Page::update_preview_image($book);
    Page::update_show_form($book, $list_category);

    # Marking the end of the main content
    echo "</div></div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";

    # Cleaning the data
    if (isset($book))
    {
        foreach ($book as $key=>$value)
        {
            unset($book["key"]);
        }
    }
    if ($list_category)
    {
        foreach ($list_category as $category)
        {
            foreach ($category as $key=>$value)
            {
                unset($category["key"]);
            }
            unset($category);
        }
    }
    unset($book, $list_category);
}
else
{
    # Redirecting to the main page
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();