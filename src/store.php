<?php
# Including class files
require_once "./lib/Book.php";
require_once "./lib/Page.php";

use lib\Book, lib\Page;


#Open/restore a session
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
# if (isset($_GET["add-to-cart"]))
$list_category = Book::list_category($conn);
if (isset($_GET["code"]))
    $list_books = Book::list_books($conn, "code", $_GET["code"]);
elseif (isset($_GET["find"]))
    $list_books = Book::list_books($conn, "find", $_GET["find"]);
else
    $list_books = Book::list_books($conn);

# Closing the database connection
$conn->close();


# Sending response
if ($list_category) //and the last item if you can do that
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
    Page::load_breadcrumb("store");

    # ------------------------Main content------------------------

    # Making a container for the main content
    echo "<div class='container'><div class='row'>\n";

    # Drawing a column that contains the category
    echo "\t<div class='col-12 col-sm-3'>\n";
    Page::store_list_category($list_category);
    //this would be the place to print out the last item if you can do that
    echo "\t</div>\n";

    # Drawing a column of rows of items
    if  ($list_books)
        Page::store_list_items($list_books);
    else
    {
        echo "\t<div class='col'><div class='row'>\n";
        echo "\t\t<p >No books found.</p>\n";
        echo "\t</div></div>\n";
    }

    # Marking the end of the main content
    echo "</div></div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";


    # Cleaning up the data
    foreach ($list_category as $genre)
    {
        foreach ($genre as $key => $value)
        {
            unset($genre[$key]);
        }
        unset($genre);
    }
    unset($list_category);
    if ($list_books)
    {
        foreach ($list_books as $book)
        {
            foreach ($book as $key => $value)
            {
                unset($book[$key]);
            }
            unset($book);
        }
    }
    unset($list_books);

    session_commit();

    # Terminating the execution of the script
    exit();
}
else
{
    # Cleaning up the data
    unset($list_category);
    if ($list_books)
    {
        foreach ($list_books as $book)
        {
            foreach ($book as $key => $value)
            {
                unset($book[$key]);
            }
            unset($book);
        }
    }
    unset($list_books);

    session_commit();

    # Here send server error
    die();
}






