<?php
# Including class files
require_once "./lib/Page.php";

use lib\Page;


# Starting/restoring a session
session_start();
if (isset($_SESSION["CUR_URI"]))
    $_SESSION["PREV_URI"] = $_SESSION["CUR_URI"];
$_SESSION["CUR_URI"] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION["USER"]))
{
    # Redirecting to the main page
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}
else
{
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
    echo "<div class='container'>\n";

    echo file_get_contents("./html/form-register.html");

    # Marking the end of the main content
    echo "</div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();