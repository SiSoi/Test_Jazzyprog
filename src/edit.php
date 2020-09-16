<?php
# Including class files
require_once  "./lib/User.php";
require_once "./lib/Page.php";

use lib\User, lib\Page;


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

if (isset($_SESSION["USER"]))
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
    $mail = User::get_email($conn, $_SESSION["USER"]["username"]);
    # else sending error page?, $user= FALSE?

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

    # -------------------------Breadcrumb-------------------------
    Page::load_breadcrumb("edit");

    # ------------------------Main content------------------------

    # Making a container for the main content
    echo "<div class='container'>
            <div class='row'>\n";

    Page::edit_show_forms(array('email'=>$mail, 'username'=>$_SESSION["USER"]["username"]));

    # Marking the end of the main content
    echo   "</div>
          </div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";

    #Cleaning up the data
    unset($mail);
}
else
{
    # Redirecting to the login page
    http_response_code(302);
    header("Location: http://localhost:63342/Test/login.php");
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();