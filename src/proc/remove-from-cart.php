<?php
# Starting/restoring a session
session_start();

#Removing an item with $_GET["id"] as its id
if (isset($_GET["item_id"]))
{
    $list_ids = array_column($_SESSION["CART"]["items"], "id");
    $find_id = array_filter($list_ids, function($v){return $v==$_GET["item_id"];});
    if (!empty($find_id))
    {
        foreach ($find_id as $k=>$v)
        {
            $_SESSION["CART"]["total"]["qty"] -= $_SESSION["CART"]["items"][$k]["qty"];
            $_SESSION["CART"]["total"]["amount"] -=
                $_SESSION["CART"]["items"][$k]["price"] * $_SESSION["CART"]["items"][$k]["qty"];
            array_splice($_SESSION["CART"]["items"], $k, 1);
        }
    }
    if ($_SESSION["CART"]["total"]["qty"]=== 0)
    {
        echo "a";
        $_SESSION["CART"] = array();
        unset($_SESSION["CART"]);
    }
}

# Redirecting back to the previous page (this shame is due to no basic background on JS)
http_response_code(302);
header("Location: ".$_SESSION["CUR_URI"]);

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();