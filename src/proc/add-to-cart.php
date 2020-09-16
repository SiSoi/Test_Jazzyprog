<?php
# Starting/restoring a session
session_start();

# Adding an item and its quantity to the cart as an associative array in $_SESSION["CART"]
if (isset($_POST["item_id"]) && isset($_POST["item_qty"]))
{
    if (!isset($_SESSION["CART"]))
    {
        $_SESSION["CART"] = array();
        $_SESSION["CART"]["items"] = array();
        $_SESSION["CART"]["total"] = array();
    }

    if (!empty($_SESSION["CART"]["items"]))
    {
        $list_ids = array_column($_SESSION["CART"]["items"], "id");
        $find_id = array_filter($list_ids, function($v){return $v==$_POST["item_id"];});
        if (empty($find_id))
            array_push($_SESSION["CART"]["items"], array(
                "id"=>$_POST["item_id"], "qty"=>$_POST["item_qty"],
                "name"=>$_POST["item_name"], "price"=> $_POST["item_price"],
                "stock"=>$_POST["item_stock"], "img"=>$_POST["item_img"]));
        else
        {
            foreach ($find_id as $k=>$v)
            {
                $_SESSION["CART"]["items"][$k]["qty"] += $_POST["item_qty"];
                #$_SESSION["CART"]["items"][$k]["name"] = $_POST["item_name"];
                #$_SESSION["CART"]["items"][$k]["price"] = $_POST["item_price"];
                $_SESSION["CART"]["items"][$k]["stock"] = $_POST["item_stock"];
                #$_SESSION["CART"]["items"][$k]["img"] = $_POST["item_img"];
            }
        }
        usort($_SESSION["CART"]["items"], function($x, $y) {return $x["id"] <=> $y["id"];});
        #unset arrays list_ids, find_id
    }
    else
    {
        array_push($_SESSION["CART"]["items"], array(
            "id"=>$_POST["item_id"], "qty"=>$_POST["item_qty"],
            "name"=>$_POST["item_name"], "price"=> $_POST["item_price"],
            "stock"=>$_POST["item_stock"], "img"=>$_POST["item_img"]));
    }

    if (!empty($_SESSION["CART"]["total"]))
    {
        $_SESSION["CART"]["total"]["qty"] += $_POST["item_qty"];
        $_SESSION["CART"]["total"]["amount"] += $_POST["item_price"]*$_POST["item_qty"];
    }
    else
    {
        $_SESSION["CART"]["total"]["qty"] = $_POST["item_qty"];
        $_SESSION["CART"]["total"]["amount"] = $_POST["item_price"]*$_POST["item_qty"];
    }


}

# Redirecting back to the previous page (this shame is due to no basic background on JS)
http_response_code(302);
header("Location: ".$_SESSION["CUR_URI"]);

# Writing (storing) session data and ending the current session
session_commit();

# Terminating the execution of the script
exit();