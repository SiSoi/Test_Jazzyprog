<?php
# Including class files
require_once "../lib/User.php";

use lib\User;

# Starting/restoring a session
session_start();

# Creating connection to the database
$conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
if (mysqli_connect_errno())
{
    #send_server_error page
    #return to home page
    die();
}

# Creating a User object to check a user validity in the database
$user = new User($_POST);
$flag_login = $user->check_password($conn);

# Closing the database connection
$conn->close();

switch ($flag_login)
{
    case 0:
        #Adding session data for user
        session_regenerate_id();
        $_SESSION["USER"] = array();
        $_SESSION["USER"]["username"] = $user->get_username();
        $_SESSION["USER"]["group"] = $user->get_group();

        #Redirecting to the main page
        http_response_code(302);
        header("Location: http://localhost:63342/Test/store.php");
        break;
    case 1:
        echo "username/password\n";
        break;
    case -1:
        echo "server\n";
        break;
    default:
        echo "Unhandled\n";
        break;
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();

