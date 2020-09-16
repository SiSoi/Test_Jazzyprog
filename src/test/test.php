<?php
# Adding an item and its quantity to the cart as an associative array in $session["CART"]
function test(&$post, &$session)
{
    if (isset($post["item_id"]) && isset($post["item_qty"]))
    {
        if (!isset($session["CART"]))
        {
            $session["CART"] = array();
            $session["CART"]["items"] = array();
            $session["CART"]["total"] = array();
        }
        if (isset($session["CART"]["total"]["qty"]))
            $session["CART"]["total"]["qty"] += $post["item_qty"];
        else
            $session["CART"]["total"]["qty"] = $post["item_qty"];

        if (!empty($session["CART"]["items"]))
        {
            $list_ids = array_column($session["CART"]["items"], "id");
            $find_id = array_filter($list_ids, function($v){global $post; return $v==$post["item_id"];});
            if (empty($find_id))
                array_push($session["CART"]["items"], array(
                    "id"=>$post["item_id"], "qty"=>$post["item_qty"],
                    "name"=>$post["item_name"], "price"=> $post["item_price"],
                    "stock"=>$post["item_stock"], "img"=>$post["item_img"]));
            else
            {
                foreach ($find_id as $k=>$v)
                {
                    $session["CART"]["items"][$k]["qty"] += $post["item_qty"];
                    #$session["CART"]["items"][$k]["name"] = $post["item_name"];
                    #$session["CART"]["items"][$k]["price"] = $post["item_price"];
                    $session["CART"]["items"][$k]["stock"] = $post["item_stock"];
                    #$session["CART"]["items"][$k]["img"] = $post["item_img"];
                }
            }
            usort($session["CART"]["items"], function($x, $y) {return $x["id"] <=> $y["id"];});
        }
        else
        {
            array_push($session["CART"]["items"], array(
                "id"=>$post["item_id"], "qty"=>$post["item_qty"],
                "name"=>$post["item_name"], "price"=> $post["item_price"],
                "stock"=>$post["item_stock"], "img"=>$post["item_img"]));
        }

    }
}

require_once "../lib/Admin.php";
require_once "../lib/User.php";
use lib\Admin, lib\User;

$conn = new mysqli("localhost", "admin_nginx", "Public@123", "test");
if (mysqli_connect_errno())
{
    # Here send server error
    die();
}

function joke($conn, $id)
{
    if ($stmt = $conn->prepare("SELECT name, dir FROM img WHERE book_id=?"))
    {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $img_list = array();
        if ($stmt->num_rows > 0)
        {
            $name = null;
            $dir = null;
            $stmt->bind_result($name, $dir);
            while ($stmt->fetch())
            {
                array_push($img_list, ".".$dir.$name);
            }
            $stmt->free_result();
            unset($name, $dir);
            $stmt->close();
            return $img_list;
        }
        else
        {
            $stmt->free_result();
            $stmt->close();
            return false;
        }
    }
    else
    {
        $stmt->close();
        return false;
    }
}

$img = "abc.jpg";
$name = strtok($img, ".")."jkl";
echo $name;
echo $img;

# Closing the database connection
$conn->close();


# Terminating the execution of the script
exit();