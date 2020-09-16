<?php
# Including class files
require_once "../lib/Admin.php";

use lib\Admin;

# Starting/restoring a session
session_start();

var_dump($_POST);

if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="adm")
{
    if ($_POST || ($_FILES["img"] && $_FILES["img"]["error"]===UPLOAD_ERR_OK))
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
        $adm = new Admin($_SESSION["USER"]["username"]);

        $flag_update = false;
        if ($adm->verify_admin($conn))
        {
            if ($_POST)
                $flag_update = $adm->update_item($conn, $_SESSION["ITEM_TO_UPDATE"]["BOOK_ID"], $_POST);
            else
                $flag_update = $adm->update_item_img($conn, $_SESSION["ITEM_TO_UPDATE"]["BOOK_ID"], $_FILES["img"]);
        }

        # Closing the database connection
        $conn->close();

        # Checking if request was handled successfully
        if ($flag_update)
        {
            echo "<p>Success</p>";

            unset($flag_update);

            # Redirecting back to the main page (this shame is due to no basic background on JS)
            http_response_code(302);
            header("refresh: 5; url=http://localhost:63342/Test/item.php?id=".$_SESSION["ITEM_TO_UPDATE"]["BOOK_ID"]);
        }
        else
        {
            unset($flag_update);

            #hanlde error
            die();
        }

    }
    else
    {
        # Redirecting back to the update page
        http_response_code(302);
        header("Location: http://localhost:63342/Test/upload.php");
    }
}
else
{
    # Redirecting back to the store page
    http_response_code(302);
    header("Location: http://localhost:63342/Test/store.php");
}

# Cleaning temporary session data
$_SESSION["ITEM_TO_UPDATE"] = array();
unset($_SESSION["ITEM_TO_UPDATE"]);

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();
