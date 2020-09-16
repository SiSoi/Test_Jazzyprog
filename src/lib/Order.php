<?php


namespace lib;


class Order
{
    public static function add_order_payment($conn, $user_id)
    {
        $time_est = date("Y-m-d H:i:s");
        if ($stmt = $conn->prepare("INSERT INTO order_payment (user_id, amount) VALUES (?,?)"))
        {
            $stmt->bind_param("ss", $user_id, $_SESSION["CART"]["total"]["amount"]);
            $stmt->execute();
            $stmt->close();
            return self::get_order_payment($conn, array("user_id"=>$user_id, "time_est"=>$time_est));
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public static function get_order_payment($conn, $kwargs)
    {
        #if (isset($kwargs["id"]))
        #{
        #    echo "a";
        #}
        if (isset($kwargs["user_id"]) && isset($kwargs["time_est"]))
        {
            if ($stmt = $conn->prepare("SELECT id FROM order_payment WHERE (user_id = ?) && (time_created >= ?)"))
            {
                $stmt->bind_param("ss", $kwargs["user_id"], $kwargs["time_est"]);
                $stmt->execute();
                $id = null;
                $stmt->bind_result($id);
                if (!$stmt->fetch())
                {
                    $stmt->free_result();
                    $stmt->close();
                    return false; #Cannot fetch
                }
                else
                {
                    $stmt->free_result();
                    $stmt->close();
                    return $id; #Return id
                }
            }
            else
            {
                $stmt->close();
                return false;
            }
        }
        else
            return false;
    }



    public static function add_order_list($conn, $order_id)
    {
        $count = 0;
        foreach ($_SESSION["CART"]["items"] as $item)
        {
            if ($stmt = $conn->prepare("INSERT INTO order_list (order_id, book_name, quantity, amount) VALUES (?,?,?,?)"))
            {
                $item_amount = $item["price"] * $item["qty"];
                $stmt->bind_param("ssss", $order_id, $item["name"], $item["qty"], $item_amount);
                $executed = $stmt->execute();
                if ($executed && Order::update_book_qty($conn, $item))
                {
                    $count += 1;
                }
                $stmt->close();
            }
        }
        if ($count === count($_SESSION["CART"]["items"]))
            return true;
        else
            return false;
    }

    private static function update_book_qty($conn, $book)
    {
        if ($stmt = $conn->prepare("UPDATE book SET quantity=quantity-? WHERE id=?"))
        {
            $stmt->bind_param("ss", $book["qty"], $book["id"]);
            $executed = $stmt->execute();
            if ($executed)
                return true;
            else
                return false;
        }
        else
            return false;
    }
}