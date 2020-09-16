<?php


namespace lib;


class Book
{
    public static function get_book($conn, $id)
    {
        if ($stmt = $conn->prepare("SELECT detail.*, img.name AS IMG_NAME, img.dir AS IMG_DIR 
                            FROM (SELECT book.id AS BOOK_ID, book.code AS BOOK_CODE, book.name AS BOOK_NAME, book.price AS BOOK_PRICE,
                                    book.quantity AS BOOK_QTY, book.description AS BOOK_DESC, category.name AS BOOK_GENRE
                                    FROM book INNER JOIN category ON book.code = category.code) detail
                            INNER JOIN img ON detail.BOOK_ID = img.book_id
                            WHERE detail.BOOK_ID=?"))
        {
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1)
            {
                $book = $result->fetch_array(MYSQLI_ASSOC);
                $stmt->close();
                return $book;
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

    public static function list_category($conn)
    {
        if ($stmt = $conn->prepare("SELECT code, name FROM category WHERE id>1 ORDER BY name"))
        {
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0)
            {
                $list_category = array();
                $genre_code = null;
                $genre_name = null;
                $stmt->bind_result($genre_code, $genre_name);
                for ($i = 0; $i < $stmt->num_rows; $i++)
                {
                    $stmt->fetch();
                    $list_category[$i]["CODE"] = $genre_code;
                    $list_category[$i]["NAME"] = $genre_name;
                }
                $stmt->free_result();
                $stmt->close();
                unset($genre_code, $genre_name);
                return $list_category;
            }
            else
            {
                $stmt->free_result();
                $stmt->close();
                unset($genre_code, $genre_name);
                return false;
            }
        }
        else
        {
            $stmt->close();
            return false;
        }
    }

    public static function list_books($conn, $flag="all", $param=null)
    {
        switch ($flag)
        {
            case "all":
                $query = "SELECT book.id, book.name, book.price, book.quantity, book.description, img.name, img.dir
                            FROM book INNER JOIN img ON book.id = img.book_id ORDER BY book.name";
                break;
            case "code":
                $query = "SELECT book.id, book.name, book.price, book.quantity, book.description, img.name, img.dir
                            FROM book INNER JOIN img ON book.id = img.book_id WHERE book.code=? ORDER BY book.name";
                break;
            case "find":
                $query = "SELECT book.id, book.name, book.price, book.quantity, book.description, img.name, img.dir
                            FROM book INNER JOIN img ON book.id = img.book_id WHERE book.name LIKE ? ORDER BY book.name";
                $param = "%".$param."%";
                break;
            default:
                die();
        }
        if ($stmt = $conn->prepare($query))
        {
            if ($param)
                $stmt->bind_param("s", $param);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0)
            {
                $list_book= array();
                $book_id = null;
                $book_name = null;
                $book_price = null;
                $book_qty = null;
                $book_desc = null;
                $img_name = null;
                $img_dir = null;
                $stmt->bind_result($book_id, $book_name, $book_price, $book_qty, $book_desc, $img_name, $img_dir);
                for ($i = 0; $i < $stmt->num_rows; $i++)
                {
                    $stmt->fetch();
                    $list_book[$i]["BOOK_ID"] = $book_id;
                    $list_book[$i]["BOOK_NAME"] = $book_name;
                    $list_book[$i]["BOOK_PRICE"] = $book_price;
                    $list_book[$i]["BOOK_QTY"] = $book_qty;
                    $list_book[$i]["BOOK_DESC"] = $book_desc;
                    $list_book[$i]["IMG_NAME"] = $img_name;
                    $list_book[$i]["IMG_DIR"] = $img_dir;
                }
                $stmt->free_result();
                $stmt->close();
                unset($book_id, $book_name, $book_price, $book_qty, $book_qty, $book_desc, $img_name, $img_dir);
                return $list_book;
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
}