<?php


namespace lib;


class Admin
{
    private string $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function __destruct()
    {
        foreach ($this as $key=>$value)
        {
            unset($this->key);
        }
    }

    public function verify_admin($conn)
    {
        if ($stmt = $conn->prepare("SELECT id FROM user WHERE code='adm' AND username=?"))
        {
            $stmt->bind_param("s", $this->username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1)
            {
                $stmt->free_result();
                $stmt->close();
                return true;
            }
            else
            {
                $stmt->free_result();
                $stmt->close();
                return false; #Not admin
            }
        }
        else
        {
            $stmt->close();
            return false; #Query performed unsuccessfully.
        }
    }

    public function get_user_list($conn)
    {
        if ($stmt = $conn->prepare("SELECT id, username, code FROM user WHERE id>1"))
        {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0)
            {
                $user_list = array();
                while ($user = $result->fetch_array(MYSQLI_ASSOC))
                {
                    array_push($user_list, $user);
                }
                $stmt->close();
                return $user_list;
            }
            else
            {
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

    public function delete_user($conn, $id)
    {
        if ($stmt = $conn->prepare("DELETE FROM user WHERE id=?"))
        {
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $stmt->close();

            return true;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public function update_user_role($conn, $user)
    {
        if ($stmt = $conn->prepare("UPDATE user SET code=? WHERE id=?"))
        {
            $stmt->bind_param("ss", $user["code"], $user["id"]);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public function get_item_id($conn, $name)
    {
        if ($stmt = $conn->prepare("SELECT id FROM book WHERE name=?"))
        {
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 1)
            {
                $book_id = null;
                $stmt->bind_result($book_id);
                if ($stmt->fetch())
                {
                    $stmt->free_result();
                    $stmt->close();
                    return $book_id;
                }
                else
                {
                    $stmt->free_result();
                    $stmt->close();
                    return false;
                }
            }
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public function get_item_time($conn, $id)
    {
        if ($stmt = $conn->prepare("SELECT time_created AS CREATED, time_modified AS UPDATED
                                    FROM book WHERE id=?"))
        {
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1)
            {
                $book_time = $result->fetch_array(MYSQLI_ASSOC);
                $stmt->close();
                return $book_time;
            }
            else
            {
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

    public function get_img_time($conn, $book_id)
    {
        if ($stmt = $conn->prepare("SELECT time_created AS CREATED, time_modified AS UPDATED
                                    FROM img WHERE book_id=?"))
        {
            $stmt->bind_param("s", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1)
            {
                $img_time = $result->fetch_array(MYSQLI_ASSOC);
                $stmt->close();
                return $img_time;
            }
            else
            {
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

    public function delete_item($conn, $id)
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
                $flag_delete = true;
            }
        }
        else
        {
            $stmt->close();
            return false;
        }

        if (isset($flag_delete) && $stmt = $conn->prepare("DELETE FROM book WHERE id=?"))
        {
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $stmt->close();
            foreach ($img_list as $img)
            {
                unlink($img);
                unset($img);
            }
            unset($flag_delete, $img_list);
            return true;
        }
        else
        {
            unset($flag_delete);
            $stmt->close();
            return false;
        }
    }

    public function insert_item($conn, $kwargs)
    {
        if ($stmt = $conn->prepare("INSERT INTO book (code, name, price, quantity, description) VALUES (?,?,?,?,?)"))
        {
            $stmt->bind_param("sssss", $kwargs["item"]["code"], $kwargs["item"]["name"],
                                $kwargs["item"]["price"], $kwargs["item"]["qty"], $kwargs["item"]["description"]);
            $stmt->execute();
        }
        else
        {
            $stmt->close();
            return false;
        }

        if (!$book_id = $this->get_item_id($conn, $kwargs["item"]["name"]))
            return false;

        $ext = null;
        switch ($kwargs["img"]["type"])
        {
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext = ".png";
                break;
            case "image/gif":
                $ext = ".gif";
                break;
            default:
                return false;
        }
        $img_name = strtolower($kwargs["item"]["code"])."_".$book_id.$ext;

        if (move_uploaded_file($kwargs["img"]["tmp_name"], "../img/".$img_name)
            && $stmt=$conn->prepare("INSERT INTO img (book_id, name) VALUES (?,?)"))
        {
            $stmt->bind_param("ss", $book_id, $img_name);
            $stmt->execute();
        }
        else
        {
            $stmt->close();
            return false;
        }

        unset($book_id, $img_name, $ext);
        return true;
    }

    public function update_item($conn, $id, $kwargs)
    {
        foreach ($kwargs as $key=>$value)
        {
            if ($stmt = $conn->prepare("UPDATE book SET ".$key."=? WHERE id=?"))
            {
                $stmt->bind_param("ss", $value, $id);
                $stmt->execute();
                $stmt->close();
            }
            else
            {
                $stmt->close();
                return false;
            }
        }
        return true;
    }

    public static function update_item_img($conn, $book_id, $kwargs)
    {
        if ($stmt = $conn->prepare("SELECT name FROM img WHERE book_id=?"))
        {
            $stmt->bind_param("s", $book_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows!==1)
            {
                $stmt->free_result();
                $stmt->close();
                return false;
            }
            $img_old = null;
            $stmt->bind_result($img_old);
            $stmt->fetch();
            $stmt->free_result();
            $stmt->close();
        }
        else
        {
            $stmt->close();
            return false;
        }

        $ext = null;
        switch ($kwargs["type"])
        {
            case "image/jpeg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext = ".png";
                break;
            case "image/gif":
                $ext = ".gif";
                break;
            default:
                return false;
        }
        $img_new = strtok($img_old, ".").$ext;

        if (md5_file("../img/".$img_old) !== md5_file($kwargs["tmp_name"]))
        {
            if ($stmt = $conn->prepare("UPDATE img SET name=? WHERE book_id=?"))
            {
                $stmt->bind_param("ss", $img_new, $book_id);
                $stmt->execute();
                $stmt->close();
            }
            else
            {
                $stmt->close();
                return false;
            }
            unlink("../img/".$img_old);
            move_uploaded_file($kwargs["tmp_name"], "../img/".$img_new);
        }

        unset($img_old, $img_new, $ext);
        return true;
    }

    public function check_category($conn, &$code, &$name)
    {
        if ($stmt = $conn->prepare("SELECT code, name FROM category WHERE code=? OR name=?"))
        {
            $stmt->bind_param("ss", $code, $name);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0)
            {
                $stmt->free_result();
                $stmt->close();
                return 0; #Not found.
            }
            $ctg_code = null;
            $ctg_name = null;
            $stmt->bind_result($ctg_code, $ctg_name);
            while ($stmt->fetch())
            {
                if ($ctg_name===$name)
                {
                    if ($ctg_code!==$code)
                        $code = $ctg_code;
                    break;
                }
                if ($ctg_code===$code)
                {
                    if ($ctg_name!==$name)
                        $name = $ctg_name;
                    break;
                }
            }
            unset($ctg_name, $ctg_code);
            $stmt->free_result();
            $stmt->close();
            return 1;
        }
        else
        {
            $stmt->close();
            return -1; #Cannot create prepared statement.
        }
    }

    public function insert_category($conn, $code, $name)
    {
        if ($stmt = $conn->prepare("INSERT INTO category (code, name) VALUES (?,?)"))
        {
            $stmt->bind_param("ss", $code, $name);
            $stmt->execute();
            $stmt->close();
            return true;
        }
        else
        {
            $stmt->close();
            return false;
        }
    }
}