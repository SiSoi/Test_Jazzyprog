<?php

# Including class files
require_once "./lib/Page.php";
require_once "./lib/Admin.php";

use lib\Page, lib\Admin;


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

    # Getting user list
    $adm = new Admin($_SESSION["USER"]["username"]);
    if ($adm->verify_admin($conn))
        $list_users = $adm->get_user_list($conn);
    else
    {
        # Here send server error
        die();
    }

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
    Page::load_breadcrumb("users");

    # ------------------------Main content------------------------

    # Making a container for the main content
    echo "<div class='container'><div class='row'>\n";

    # Main content
    Page::users_list_users($list_users);

    # Marking the end of the main content
    echo "</div></div>\n";

    # ---------------------------Footer---------------------------
    echo file_get_contents("./html/footer.html");

    # --------------------------BODY END--------------------------
    echo "</body>\n";

    # --------------------------HTML END--------------------------
    echo "</html>\n";

    # Cleaning the data
    if ($list_users)
    {
        foreach ($list_users as $user)
        {
            foreach ($user as $key=>$value)
            {
                unset($user["key"]);
            }
            unset($user);
        }
    }
    unset($list_users);
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