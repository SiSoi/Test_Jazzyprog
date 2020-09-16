<?php

namespace lib;

class User
{
    private string $email;
    private string $user;
    private string $group;
    private string $pw;

    public function __construct($kwargs)
    {
        foreach ($kwargs as $key=>$value)
        {
            $this->{$key} = $value;
        }
    }

    public function __destruct()
    {
        foreach ($this as $key=>$value)
        {
           unset($this->key);
        }
    }

    public function get_username()
    {
        return $this->user;
    }

    public function get_group()
    {
        return $this->group;
    }

    public function create($conn)
    {
        #check email
        $flag_email = $this->check_email($conn);
        if ($flag_email != 0)
            if ($flag_email != -1)
                return array("email", $flag_email);
            else return array("server", -11);

        #check username
        $flag_username = $this->check_username($conn);
        if ($flag_username != 0)
            if ($flag_username != -1)
                return array("username", $flag_username);
            else return array("server", -12);

        #hash pw
        if (!$hash_pw = password_hash($this->pw, PASSWORD_BCRYPT))
            return array("server", -13);

        #SQL INSERT query
        if ($stmt = $conn->prepare("INSERT INTO user (username, email, authentication_string) VALUES (?,?,?)"))
        {
            $stmt->bind_param("sss", $this->user, $this->email, $hash_pw);
            $stmt->execute();
            $stmt->close();
            unset($hash_pw, $flag_username, $flag_email);
            return array("ok", 0);
        }
        else
        {
            $stmt->close();
            return array("server", -10);
        }
    }
    
    public function check_email($conn)
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            return 2; #Invalid email
        if ($stmt = $conn->prepare("SELECT id FROM user WHERE email=?"))
        {
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return 0; #Email available.
            }
            else
            {
                $stmt->free_result();
                $stmt->close();
                return 1; #Email already existed.
            }
        }
        else
        {
            $stmt->close();
            return -1; #Cannot create prepared statement.
        }
    }

    public function check_username($conn)
    {
        if (preg_match("/\w{5,15}/", $this->user) != 1)
            return 2; #Invalid username
        if ($stmt = $conn->prepare("SELECT id FROM user WHERE username=?"))
        {
            $stmt->bind_param("s", $this->user);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return 0; #Username available.
            }
            else
            {
                $stmt->free_result();
                $stmt->close();
                return 1; #Username already existed.
            }
        }
        else
        {
            $stmt->close();
            return -1; #Cannot create prepared statement.
        }
    }

    public function check_password($conn)
    {
        if ($stmt = $conn->prepare("SELECT code, authentication_string FROM user WHERE username=?"))
        {
            $stmt->bind_param("s", $this->user);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return 1; #Username does not exist
            }
            $group = null;
            $hash_pw = null;
            $stmt->bind_result($group, $hash_pw);
            if (!$stmt->fetch())
            {
                $stmt->free_result();
                $stmt->close();
                return -1; #Cannot fetch
            }
            else
            {
                $stmt->free_result();
                $stmt->close();
                $this->group = $group;
                $flag_password = password_verify($this->pw, $hash_pw);
                unset($group, $hash_pw);
            }
            if ($flag_password)
                return 0; #Correct password
            else
                return 1; #Incorrect password
        }
        else
        {
            $stmt->close();
            return -1; #Cannot create prepared statement.
        }
    }

    public function update_email($conn, $new)
    {
        if ($stmt = $conn->prepare("UPDATE user SET email=? WHERE username=?"))
        {
            $stmt->bind_param("ss", $new, $this->user);
            if ($stmt->execute())
            {
                $stmt->close();
                return true;
            }
            $stmt->close();
            return false;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public function update_username($conn, $new)
    {
        if ($stmt = $conn->prepare("UPDATE user SET username=? WHERE username=?"))
        {
            $stmt->bind_param("ss", $new, $this->user);
            if ($stmt->execute())
            {
                $stmt->close();
                return true;
            }
            $stmt->close();
            return false;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public function update_password($conn, $new)
    {
        if (($stmt = $conn->prepare("UPDATE user SET authentication_string=? WHERE username=?")) && ($hashnew = password_hash($new, PASSWORD_BCRYPT)))
        {
            $stmt->bind_param("ss", $hashnew, $this->user);
            if ($stmt->execute())
            {
                $stmt->close();
                return true;
            }
            $stmt->close();
            return false;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public static function find_id($conn, $user)
    {
        if ($stmt = $conn->prepare("SELECT id FROM user WHERE username=?"))
        {
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return 0; #Not found.
            }
            $id = null;
            $stmt->bind_result($id);
            if (!$stmt->fetch())
            {
                $stmt->free_result();
                $stmt->close();
                return -1; #Cannot fetch
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
            return -1; #Cannot create prepared statement.
        }
    }

    public static function get_email($conn, $username)
    {
        if ($stmt = $conn->prepare("SELECT email FROM user WHERE username=?"))
        {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1)
            {
                $email = null;
                $stmt->bind_result($email);
                if ($stmt->fetch())
                {
                    $stmt->free_result();
                    $stmt->close();
                    return $email;
                }
            }
            $stmt->free_result();
            $stmt->close();
            return false;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public static function get_info($conn, $username)
    {
        $user = array();
        if ($stmt = $conn->prepare("SELECT id, code, email, time_created, time_modified FROM user WHERE username=?"))
        {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return false; #Not found.
            }
            $stmt->bind_result($user["ID"], $user["Role"], $user["Email"], $user["Member since"], $user["Last updated"]);
            if (!$stmt->fetch())
            {
                $stmt->free_result();
                $stmt->close();
                return false; #Cannot fetch
            }
            else
            {
                $stmt->free_result();
                if ($stmt = $conn->prepare("SELECT count(id), max(time_created) FROM order_payment WHERE user_id=?"))
                {
                    $stmt->bind_param("s", $user["ID"]);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows === 0)
                    {
                        $stmt->free_result();
                        $stmt->close();
                        return false; #Not found.
                    }
                    else
                    {
                        $stmt->bind_result($user["Total orders"], $user["Last order"]);
                        if (!$stmt->fetch())
                        {
                            $stmt->free_result();
                            $stmt->close();
                            return false; #Cannot fetch
                        }
                        else
                        {
                            if ($user["Total orders"] === 0)
                                $user["Last order"] = "No information";
                            $stmt->free_result();
                            $stmt->close();
                            unset($user["ID"]);
                            if ($user["Role"] === "usr")
                                $user["Role"] = "User";
                            if ($user["Role"] === "adm")
                                $user["Role"] = "Admin";
                            return $user; #Return user info.
                        }
                    }
                }
                else
                {
                    $stmt->close();
                    return false; #Cannot create prepared statement.
                }
            }
        }
        else
        {
            $stmt->close();
            return false; #Cannot create prepared statement.
        }
    }

    public static function get_order_payment($conn, $username)
    {
        $order_all = array();
        if ($stmt = $conn->prepare("SELECT id AS 'Order ID', amount AS 'Total amount', method AS 'Payment method',
                                            status AS 'Order status', description AS 'Description',
                                            time_created AS 'Created on', time_modified AS 'Last modified' FROM
                                    (SELECT user.username, order_payment.* FROM user
                                    INNER JOIN order_payment ON user.id = order_payment.user_id
                                    WHERE username=?) order_all ORDER BY time_modified DESC"))
        {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                while ($order = $result->fetch_array(MYSQLI_ASSOC))
                {
                    unset($order['username'], $order['user_id']);
                    array_push($order_all, $order);
                }
                $stmt->close();
                return $order_all;
            }
            else
            {
                $stmt->close();
                return 0;
            }
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public static function get_order_list($conn, $order_id, $username)
    {
        if ($stmt = $conn->prepare("SELECT id FROM
                                    (SELECT order_payment.id AS id, user.username FROM order_payment
                                    INNER JOIN user ON order_payment.user_id = user.id
                                    WHERE order_payment.id=? AND username=?) authority"))
        {
            $stmt->bind_param("ss", $order_id, $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return false;
            }
            $stmt->free_result();
        }
        else
        {
            $stmt->close();
            return false; #Cannot create prepared statement.
        }
        $order_list = array();
        if ($stmt = $conn->prepare("SELECT book_name, quantity, amount FROM order_list WHERE order_id=? ORDER BY book_name"))
        {
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0)
            {
                $book_name = null;
                $quantity = null;
                $amount = null;
                $stmt->bind_result($book_name, $quantity, $amount);
                for ($i = 0; $i < $stmt->num_rows; $i++)
                {
                    $stmt->fetch();
                    $order_list[$i]["Product name"] = $book_name;
                    $order_list[$i]["Quantity"] = $quantity;
                    $order_list[$i]["Amount"] = $amount;
                }
                $stmt->free_result();
                $stmt->close();
                unset($book_name, $quantity, $amount);
                return $order_list;
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
            return false; #Cannot create prepared statement.
        }
    }
}