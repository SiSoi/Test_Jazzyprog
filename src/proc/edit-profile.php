<?php
# Including class files
require_once "../lib/User.php";

use lib\User;

# Starting/restoring a session
session_start();

var_dump($_POST);

if (isset($_SESSION["USER"]))
{
    if ($_POST && isset($_POST["pwd"]))
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
        $user = new User(array("user"=>$_SESSION["USER"]["username"], "pw"=>$_POST["pwd"]));

        $flag_update = false;
        if ($user->check_password($conn) === 0)
        {
            if (isset($_POST["new_email"]))
                $flag_update = $user->update_email($conn, $_POST["new_email"]);
            if (isset($_POST["new_username"]))
                if ($flag_update = $user->update_username($conn, $_POST["new_username"]))
                    $_SESSION["USER"]["username"] = $_POST["new_username"];
            if (isset($_POST["new_pwd"]))
                $flag_update = $user->update_password($conn, $_POST["new_pwd"]);
        }

        # Closing the database connection
        $conn->close();

        # Checking if request was handled successfully
        if ($flag_update===false)
        {
            unset($flag_update);

            #handle error page
            die();
        }
        else
        {
            # Cleaning variables
            unset($flag_update);

            echo "<p>Success</p>";

            # Redirecting back to the profile page
            http_response_code(302);
            header("refresh: 5; url=http://localhost:63342/Test/profile.php");
        }
    }
    else
    {
        # Redirecting back to the edit page
        http_response_code(302);
        header("Location: http://localhost:63342/Test/edit.php");
    }

}
else
{
    # Redirecting back to the login page
    http_response_code(302);
    header("Location: http://localhost:63342/Test/login.php");
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();