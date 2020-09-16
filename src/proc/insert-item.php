<?php
# Including class files
require_once "../lib/Admin.php";

use lib\Admin;

# Starting/restoring a session
session_start();


if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="adm")
{
    if ($_POST && $_FILES["img"] && $_FILES["img"]["error"]===UPLOAD_ERR_OK)
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

        $flag_upload = false;
        if ($adm->verify_admin($conn))
        {
            $flag_upload = $adm->check_category($conn, $_POST["code"], $_POST["genre"]);
            switch ($flag_upload)
            {
                case 1:
                    $flag_upload = true;
                    break;
                case 0:
                    $flag_upload = $adm->insert_category($conn, $_POST["code"], $_POST["genre"]);
                    break;
                case -1:
                    $flag_upload = false;
                    break;
            }
            if ($flag_upload)
                $flag_upload = $adm->insert_item($conn, array("item"=>$_POST, "img"=>$_FILES["img"]));
        };

        # Closing the database connection
        $conn->close();

        # Checking if request was handled successfully
        if ($flag_upload)
        {
            echo "<p>Success</p>";

            unset($flag_upload);

            # Redirecting back to the main page (this shame is due to no basic background on JS)
            http_response_code(302);
            header("refresh: 5; url=http://localhost:63342/Test/store.php");
        }
        else
        {
            unset($flag_upload);

            #hanlde error
            die();
        }
    }
    else
    {
        # Redirecting back to the upload page (this shame is due to no basic background on JS)
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

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();
