<?php
# Including class files
require_once "../lib/User.php";

use lib\User;

# Starting/restoring a session
session_start();

$conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
if (mysqli_connect_errno())
{
    #send_server_error page
    #return to home page
    die();
}

# Creating a User object to create a user in the database
$user = new User($_POST);
$flag_register = $user->create($conn);

# Closing the database connection
$conn->close();

switch ($flag_register[0])
{
    case "ok":
        #Redirecting to the login page
        http_response_code(302);
        header("Location: http://localhost:63342/Test/login.php");
        break;
    case "email":
        echo "email\n";
        echo $flag_register[1]."\n";
        break;
    case "username":
        echo "username\n";
        echo $flag_register[1]."\n";
        break;
    case "server":
        echo "Server fault".$flag_register[1]."\n";
        break;
    default:
        echo "Unhandled\n";
        break;
}

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();