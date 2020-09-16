<?php
# Including class files
require_once "../lib/Admin.php";

use lib\Admin;

# Starting/restoring a session
session_start();


if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="adm")
{
    if ($_POST["item_id"])
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

        $flag_delete = $adm->delete_item($conn, $_POST["item_id"]);

        # Closing the database connection
        $conn->close();

        # Checking if request was handled successfully
        if ($flag_delete)
        {
            echo "<p>Success</p>";

            unset($flag_delete);

            # Redirecting back to the main page (this shame is due to no basic background on JS)
            http_response_code(302);
            header("refresh: 5; url=http://localhost:63342/Test/store.php");
        }
        else
        {
            unset($flag_delete);

            #hanlde error
            die();
        }
    }
    else
    {
        # Redirecting back to the store page
        http_response_code(302);
        header("Location: http://localhost:63342/Test/store.php");
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
