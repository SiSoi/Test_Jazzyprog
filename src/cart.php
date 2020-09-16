<?php
# Including class files
require_once "./lib/Page.php";

use lib\Page;

#Open/restore a session
session_start();
if (isset($_SESSION["CUR_URI"]))
    $_SESSION["PREV_URI"] = $_SESSION["CUR_URI"];
$_SESSION["CUR_URI"] = $_SERVER["REQUEST_URI"];


if (!isset($_SESSION["USER"]) || $_SESSION["USER"]["group"]==="usr")
{
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

    # ------------------------Main content------------------------
    # Making a container for the main content
    echo "<div class='container mb-4'><div class='row'>\n";

    # Printing the main content
    if (isset($_SESSION["CART"]))
    {
        Page::cart_list_items();
    }
    else
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
}
else
{
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}


session_commit();

# Terminating the execution of the script
exit();