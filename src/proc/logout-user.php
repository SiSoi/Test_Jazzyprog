<?php
# Starting/restoring a session
session_start();

# Cleaning up the whole session data and regenerating new session id
foreach ($_SESSION as $key=>$value)
{
    $_SESSION[$key] = array();
    unset($_SESSION[$key]);
}
session_regenerate_id(true);

# Writing (storing) session data and ending the current session
session_commit();

# Redirecting back to the main page
http_response_code(302);
header("Location: http://localhost:63342/Test/store.php");

# Terminating the execution of the script
exit();