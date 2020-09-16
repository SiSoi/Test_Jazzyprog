<?php


namespace lib;


class Page
{
    public static function load_header()
    {
        if (isset($_SESSION["CART"]["total"]))
            $total = $_SESSION["CART"]["total"]["qty"];
        else
            $total = 0;

        echo "<header>
                <nav class='navbar navbar-expand-md navbar-dark bg-dark'>
                    <div class='container'>
                        <a class='navbar-brand' href='./store.php'>Jazzyprog Store</a>
                        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarsExampleDefault' aria-controls='navbarsExampleDefault' aria-expanded='false' aria-label='Toggle navigation'>
                            <span class='navbar-toggler-icon'></span>
                        </button>
                        <div class='collapse navbar-collapse justify-content-end' id='navbarsExampleDefault'>";

        # Profile dropdown here, if not any session => login, register
        if (isset($_SESSION["USER"]))
            self::load_profile_dropdown();
        else
            self::load_user_navigation();

        echo                "<form class='form-inline my-2 my-lg-0' action='./store.php' method='get'>
                                <div class='input-group input-group-sm'>
                                    <input type='text' class='form-control' placeholder='Search in the store...' name='find'>
                                    <div class='input-group-append'>
                                        <button type='submit' class='btn btn-secondary btn-number'>
                                            <i class='fa fa-search'></i>
                                        </button>
                                    </div>
                                </div>
                             </form>";
        if (!isset($_SESSION["USER"]) || $_SESSION["USER"]["group"]==="usr")
        {
            echo            "<a class='btn btn-success btn-sm ml-3' href='./cart.php'><i class='fa fa-shopping-cart'></i> Cart
                                <span class='badge badge-light'>".$total."</span>
                             </a>";
        }
        echo           "</div>
                    </div>
                </nav>
              </header>";
        unset($total);
    }

    public static function load_user_navigation()
    {
        echo "<ul class='navbar-nav m-auto'>
                <li class='nav-item'>
                    <a class='nav-link' href='./login.php'>Log in</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='./register.php'>Register</a>
                </li>
              </ul>";
    }

    public static function load_profile_dropdown()
    {
        echo "<ul class='navbar-nav m-auto'>
                <li class='nav-item dropdown'>
                    <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                        ".$_SESSION["USER"]["username"]."
                    </a>
                    <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
                        <a class='dropdown-item' href='./profile.php'>Profile</a>";
        if ($_SESSION["USER"]["group"]==="usr")
            echo       "<a class='dropdown-item' href='./history.php'>Order history</a>
                        <a class='dropdown-item' href='./order.php'>Order search</a>";
        if ($_SESSION["USER"]["group"]==="adm")
            echo       "<a class='dropdown-item' href='./upload.php'>Add a new book</a>
                         <a class='dropdown-item' href='./users.php'>Manage users</a>";
        echo           "<div class='dropdown-divider'></div>
                        <a class='dropdown-item' href='./proc/logout-user.php'>Log out</a>
                    </div>
                </li>
              </ul>";

    }

    public static function load_breadcrumb($page, $kwargs=false)
    {
        echo "<div class='container'>
                <div class='row'>
                    <div class='col'>
                        <nav aria-label='breadcrumb'>
                            <ol class='breadcrumb'>";
        switch ($page)
        {
            case "store":
                echo            "<li class='breadcrumb-item active' aria-current='page'>Store</li>";
                break;
            case "item":
                echo            "<li class='breadcrumb-item'><a href = './store.php'>Store</a></li>";
                if (isset($kwargs["BOOK_NAME"]))
                    echo        "<li class='breadcrumb-item active' aria-current='page'>".$kwargs["BOOK_NAME"]."</li>";
                else
                    echo        "<li class='breadcrumb-item active' aria-current='page'>Item</li>";
                break;
            case "profile":
                echo            "<li class='breadcrumb-item active' aria-current='page'>Profile</li>";
                break;
            case "edit":
                echo            "<li class='breadcrumb-item active' aria-current='page'>Edit profile</li>";
                break;
            case "history":
                echo            "<li class='breadcrumb-item active' aria-current='page'>Order history</li>";
                break;
            case "order":
                echo            "<li class='breadcrumb-item active' aria-current='page'>Order details</li>";
                break;
            case "users":
                echo            "<li class='breadcrumb-item active' aria-current='page'>User list</li>";
                break;
        }
        echo                "</ol>
                        </nav>
                    </div>
                </div>
              </div>";
    }

    public static function cart_list_items()
    {
        echo "<div class='col-12'>
                <div class='table-responsive'>
                    <table class='table table-striped'>
                        <thead>
                        <tr>
                            <th scope='col'> </th>
                            <th scope='col'>Product</th>
                            <th scope='col'>Available</th>
                            <th scope='col' class='text-center'>Quantity</th>
                            <th scope='col' class='text-right'>Price</th>
                            <th> </th>
                        </tr>
                        </thead>
                        <tbody>";
        foreach ($_SESSION["CART"]["items"] as $item)
        {
            echo       "<tr>
                            <td><img src='".$item["img"]."' alt='".$item["name"]."' style='max-width: 30%'></td>
                            <td><a href='./item.php?id=".$item['id']."' title='View item'>".$item["name"]."</a></td>";
            if ($item["stock"]===0)
                echo       "<td>Out of stock</td>";
            else
                echo       "<td>In stock</td>";
            echo           "<td class='text-center'>".$item["qty"]."</td>
                            <td class='text-right'>$".$item["price"]*$item["qty"]."</td>
                            <td class='text-right'>
                                <a class='btn btn-sm btn-danger' href='./proc/remove-from-cart.php?item_id=".$item["id"]."'>
                                    <i class='fa fa-trash'></i>
                                </a>
                            </td>
                        </tr>";
        }
        echo           "<tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Total</strong></td>
                            <td class='text-right'><strong>$".$_SESSION["CART"]["total"]["amount"]."</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
              </div>";
        echo "<div class='col mb-2'>
                <div class='row'>
                    <div class='col-sm-12  col-md-6'>
                        <a class='btn btn-block btn-light' href='./store.php'>Continue shopping</a>
                    </div>
                    <div class='col-sm-12 col-md-6 text-right'>
                        <a class='btn btn-lg btn-block btn-success text-uppercase' href='./proc/process-order.php'>Checkout</a>
                    </div>
                </div>
              </div>";
    }

    public static function cart_show_empty()
    {
        echo "<div class='col-12'>
                <p class='text-center'>Empty</p>
              </div>
              <div class='col mb-2 text-center'>
                <a class='btn btn-success text-uppercase' href='./store.php'>Go shopping right now</a>
              </div>";
    }

    public static function edit_show_forms($kwargs)
    {
        echo "<div class='card col-md-12 col-12'>
                <div class='card-body'>";
        foreach ($kwargs as $key=>$value)
        {
            echo        "<form action='./proc/edit-profile.php' method='post'>
                            <div class='row'>
                                <h4 text-left'>Change ".$key."</h4>
                            </div>
                            <div class='form-row'>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='".$key."' style='font-weight:bold;'>Current ".$key.":</label>
                                    <input class='col-md-7 col-7' type='text' id='".$key."' name='".$key."' readonly value='".$value."'>
                                </div>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='pwd_".$key."' style='font-weight:bold;'>Password:</label>
                                    <input class='col-md-7 col-7' type='password' id='pwd_".$key."' name='pwd' required>
                                </div>
                            </div>
                            <div class='form-row'>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='new_".$key."' style='font-weight:bold;'>New ".$key.":</label>
                                    <input class='col-md-7 col-7' type='text' id='new_".$key."' name='new_".$key."' required>
                                </div>
                                <div class='form-group col-md-6 col-6 text-center'>
                                    <button class='btn btn-success' type='submit'>Change</button>
                                </div>
                             </div> 
                            </form>
                            <hr>
                        ";
        }
        echo           "<form action='./proc/edit-profile.php' method='post'>
                            <div class='row'>
                                <h4 text-left'>Change password</h4>
                            </div>
                            <div class='form-row'>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='new_pwd' style='font-weight:bold;'>New password:</label>
                                    <input class='col-md-7 col-7' type='password' id='new_pwd' name='new_pwd' required onchange='check_pwd()'>
                                </div>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='pwd' style='font-weight:bold;'>Current password:</label>
                                    <input class='col-md-7 col-7' type='password' id='pwd' name='pwd' required>
                                </div>
                            </div>
                            <div class='form-row'>
                                <div class='form-group col-md-6 col-6'>
                                    <label class='col-md-4 col-4' for='renew_pwd' style='font-weight:bold;'>Re-enter new password:</label>
                                    <input class='col-md-7 col-7' type='password' id='renew_pwd' name='renew_pwd' required onchange='check_pwd()'>
                                </div>
                                <span class='col-md-3 col-3' id='message'></span>
                                <div class='form-group col-md-3 col-3 text-center'>
                                    <button class='btn btn-success' type='submit'>Change</button>
                                </div>
                             </div> 
                            </form>
                            <hr>
                            <script type='text/javascript'>
                                function check_pwd() {
                                    if document.getElementById('new_pwd').value === document.getElementById('renew_pwd').value {
                                        document.getElementById('message').innerHTML = \"Passwords matched.\";
                                    }
                                    else {
                                        document.getElementById('message').innerHTML = \"Passwords not matched.\";
                                    }
                                };
                            </script>";
        echo           "
                </div>
              </div>";
    }

    public static function history_list_orders($kwargs)
    {
        echo "<div class='col-12'>
                <div class='table-responsive'>
                    <table class='table table-striped'>
                        <thead>
                        <tr>
                            <th scope='col' class ='col-0.5 text-center'>Order ID</th>
                            <th scope='col' class ='col-1.5 text-center'>Created on</th>
                            <th scope='col' class ='col-1.5 text-center'>Last modified</th>
                            <th scope='col' class ='col-1.5 text-center'>Total amount</th>
                            <th scope='col' class ='col-1.5 text-center'>Payment method</th>
                            <th scope='col' class ='col-1.5 text-center'>Order status</th>
                            <th scope='col' class ='col-3 text-center'>Description</th>
                            
                        </tr>
                        </thead>
                        <tbody>";
        foreach ($kwargs as $order)
        {
            echo       "<tr>
                            <td class ='text-right'><a href='./order.php?id=".$order["Order ID"]."' title='View item'>".$order["Order ID"]."</a></td>
                            <td class='text-center'>".$order["Created on"]."</td>
                            <td class='text-center'>".$order["Last modified"]."</td>
                            <td class='text-right'>$".$order["Total amount"]."</td>
                            <td class='text-center'>".$order["Payment method"]."</td>
                            <td class='text-center'>".$order["Order status"]."</td>
                            <td class='text-left'>".$order["Description"]."</td>
                        </tr>";
        }
        echo           "</tbody>
                    </table>
                </div>
              </div>";
    }

    public static function item_show_image($kwargs, $timestamp=null)
    {
        echo "<div class='col-12 col-lg-6'>
                <div class='card bg-light mb-3'>
                    <div class='card-body'>
                        <div class='text-center'>
                            <img src='".$kwargs["IMG_DIR"].$kwargs["IMG_NAME"]."' alt='".$kwargs["IMG_NAME"]."'><hr>
                        </div>";
        if ($timestamp)
        {
            echo       "<div class='row'>
                            <div class='col-4 offset-1 text-left'><label style='font-weight: bold'>Created:</label></div>
                            <div class='col-7 text-center'><p>".$timestamp["CREATED"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4 offset-1 text-left'><label style='font-weight: bold'>Last updated:</label></div>
                            <div class='col-7 text-center'><p>".$timestamp["UPDATED"]."</p></div>
                        </div>";
        }
        echo       "</div>
                </div>
              </div>";
    }

    public static function item_show_add_to_cart($kwargs)
    {
        echo "<div class='col-12 col-lg-6 add_to_cart_block'>
                <div class='card bg-light mb-3'>
                    <div class='card-body'>
                        <p class='price'>$".$kwargs["BOOK_PRICE"]."</p><br>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Genre:</label></div>
                            <div class='col-8'><p>".$kwargs["BOOK_GENRE"]."</p></div>
                        </div>
                        <form action='./proc/add-to-cart.php' method='post'>
                            <div class='form-group'>
                                <label style='font-weight: bold'>Quantity:</label>
                                <div class='input-group mb-3'>
                                    <div class='input-group-prepend'>
                                        <button type='button' class='quantity-left-minus btn btn-danger btn-number' data-type='minus' data-field=''>
                                            <i class='fa fa-minus'></i>
                                        </button>
                                    </div>
                                    <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                    <input type='text' class='form-control' id='quantity' name='item_qty' min='1' max=".$kwargs["BOOK_QTY"]." value=1>
                                    <input type='hidden' name='item_name' value='".$kwargs["BOOK_NAME"]."'>
                                    <input type='hidden' name='item_price' value=".$kwargs["BOOK_PRICE"].">
                                    <input type='hidden' name='item_stock' value=".$kwargs["BOOK_QTY"].">
                                    <input type='hidden' name='item_img' value='".$kwargs["IMG_DIR"].$kwargs["IMG_NAME"]."'>
                                    <div class='input-group-append'>
                                        <button type='button' class='quantity-right-plus btn btn-success btn-number' data-type='plus' data-field=''>
                                            <i class='fa fa-plus'></i>
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                            <button type ='submit' class='btn btn-success btn-lg btn-block text-uppercase'>
                                <i class='fa fa-shopping-cart'></i> Add to cart
                            </button>
                        </form>";
        echo file_get_contents("./html/script-item-quantity.html");
        echo      "</div>
                </div>
              </div>";
    }

    public static function item_show_details($kwargs, $timestamp)
    {
        echo "<div class='col-12 col-lg-6'>
                <div class='card bg-light mb-3'>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>ID:</label></div>
                            <div class='col-8'><p>".$kwargs["BOOK_ID"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Genre (code):</label></div>
                            <div class='col-8'><p>".$kwargs["BOOK_GENRE"]." (".$kwargs["BOOK_CODE"].")</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Name:</label></div>
                            <div class='col-8'><p>".$kwargs["BOOK_NAME"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Price:</label></div>
                            <div class='col-8'><p>$".$kwargs["BOOK_PRICE"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>In stock:</label></div>
                            <div class='col-8'><p>".$kwargs["BOOK_QTY"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Created:</label></div>
                            <div class='col-8'><p>".$timestamp["CREATED"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col-4'><label style='font-weight: bold'>Last updated:</label></div>
                            <div class='col-8'><p>".$timestamp["UPDATED"]."</p></div>
                        </div>
                        <div class='row'>
                            <div class='col'>
                                <form action='./proc/delete-item.php' method='post'>
                                    <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                    <button type ='submit' class='btn btn-danger btn-lg btn-block text-uppercase'>
                                        <i class='fa fa-trash-o'></i> Delete
                                    </button>
                                </form>    
                            </div>
                            <div class='col'>
                                <form action='./update.php' method='get'>
                                    <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                    <button type ='submit' class='btn btn-success btn-lg btn-block text-uppercase'>
                                        <i class='fa fa-pencil'></i> Edit
                                    </button>
                                </form>    
                            </div>
                        </div>
                    </div>
                </div>
              </div>";
    }

    public static function item_show_description($kwargs)
    {
        echo "<div class='col-12'>
                <div class='card border-light mb-3'>
                    <div class='card-header bg-primary text-white text-uppercase'>
                        <i class='fa fa-align-justify'></i> Description
                    </div>
                    <div class='card-body'>";
        if ($kwargs["BOOK_DESC"])
            echo       "<div class='row'>
                            <p>".$kwargs["BOOK_DESC"]."</p>
                        </div>";
        else
            echo       "<div class='row'>
                            <p>Updating soon.</p>
                        </div>";
        echo        "</div>
                </div>
              </div>";
    }

    public static function order_list_items($kwargs)
    {
        echo "<div class='col-12'>
                <div class='table-responsive'>
                    <table class='table table-striped'>
                        <thead>
                        <tr>
                            <th scope='col' class ='col-6 text-left'>Product name</th>
                            <th scope='col' class ='col-2 text-right'>Quantity</th>
                            <th scope='col' class ='col-2 text-right'>Amount</th>
                        </tr>
                        </thead>
                        <tbody>";
        $sum_amount = 0;
        foreach ($kwargs as $item)
        {
            echo       "<tr>
                            <td class='text-left'>".$item["Product name"]."</td>
                            <td class='text-right'>".$item["Quantity"]."</td>
                            <td class='text-right'>$".$item["Amount"]."</td>
                        </tr>";
            $sum_amount += $item["Amount"];
        }
        echo           "<tr>
                            <td></td>
                            <td class='text-right' style='font-weight: bold'>Total: </td>
                            <td class='text-right'>$".$sum_amount."</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
              </div>";
        unset($sum_amount);
    }

    public static function profile_show_info($kwargs)
    {
        echo "<div class='card'>
                <div class='card-body'>
                    <div class='card-title mb-4'>
                        <div class='userData ml-3 d-block'>
                                <label style='font-size: 1.5rem; font-weight: bold'>".$_SESSION["USER"]["username"]."</label>
                                <a class='btn btn-success btn-sm' href='./edit.php'><i class='fa fa-pencil'></i></a>
                        </div>
                        <br>
                    </div>";
        echo       "<div class='row'>
                        <div class='col-12'>";
        foreach ($kwargs as $key=>$value)
        {
            if ($key==="Total orders" && $_SESSION["USER"]["group"] === "adm")
                break;
            echo           "<div class='row'>
                                <div class='col-sm-3 col-md-2 col-5'>
                                    <label style='font-weight:bold;'>".$key."</label>
                                </div>
                                <div class='col-md-8 col-6'>
                                    <p>".$value."</p>
                                </div>
                             </div>
                             <hr>";
        }
        echo           "</div>
                    </div>
                </div>
              </div>";
    }

    public static function store_list_category($args_kwargs)
    {
        echo "<div class='card bg-light mb-3'>
                <div class='card-header bg-primary text-white text-uppercase'>
                    <i class='fa fa-list'></i> Category
                </div>
                <ul class='list-group category_block'>";
        echo        "<li class='list-group-item'>
                        <a href='./store.php'>All</a>
                     </li>";
        foreach ($args_kwargs as $kwargs)
        {
            echo    "<li class='list-group-item'>
                        <a href='?code=".$kwargs["CODE"]."'>".$kwargs["NAME"]."</a>
                     </li>";
        }
        echo        "<li class='list-group-item'>
                        <a href='?code=UND'>Miscellaneous</a>
                     </li>
                </ul>
              </div>";
    }

    public static function store_list_items($args_kwargs)
    {
        echo "<div class='col'>
                <div class='row'>";
        foreach ($args_kwargs as $kwargs)
        {
            echo "<div class='col-12 col-md-6 col-lg-4'>
                    <div class='card'>
                        <img class='card-img-top' src='".$kwargs["IMG_DIR"].$kwargs["IMG_NAME"]."' alt='".$kwargs["BOOK_NAME"]."'>
                        <div class='card-body'>
                            <h5 class='card-title'><a href='./item.php?id=".$kwargs["BOOK_ID"]."' title='View item'>".$kwargs["BOOK_NAME"]."</a></h5>
                            <p class='card-text'>".$kwargs["BOOK_DESC"]."</p>";
            if (!isset($_SESSION["USER"]) || $_SESSION["USER"]["group"]==="usr")
            {
                echo       "<div class='row'>
                                <div class='col'>
                                    <p class='btn btn-danger btn-block'>$".$kwargs["BOOK_PRICE"]."</p>
                                </div>
                                <div class='col'>
                                    <form action='./proc/add-to-cart.php' method='post'>
                                        <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                        <input type='hidden' name='item_qty' value=1>
                                        <input type='hidden' name='item_name' value='".$kwargs["BOOK_NAME"]."'>
                                        <input type='hidden' name='item_price' value=".$kwargs["BOOK_PRICE"].">
                                        <input type='hidden' name='item_stock' value=".$kwargs["BOOK_QTY"].">
                                        <input type='hidden' name='item_img' value='".$kwargs["IMG_DIR"].$kwargs["IMG_NAME"]."'>
                                        <button type='submit' class='btn btn-success btn-block'>Add to cart</button>
                                    </form>
                                </div>
                            </div>";
            }
            if (isset($_SESSION["USER"]) && $_SESSION["USER"]["group"]==="adm")
            {
                echo       "<div class='row'>
                                <div class='col'>
                                    <form action='./proc/delete-item.php' method='post'>
                                        <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                        <button type='submit' class='btn btn-danger btn-block'>Delete</button>
                                    </form>
                                </div>
                                <div class='col'>
                                    <form action='./update.php' method='get'>
                                        <input type='hidden' name='item_id' value=".$kwargs["BOOK_ID"].">
                                        <button type='submit' class='btn btn-success btn-block'>Edit</button>
                                    </form>
                                </div>
                            </div>";
            }
            echo       "</div>
                    </div>
                  </div>";
        }
        echo   "</div>
              </div>";
    }

    public static function update_preview_image($kwargs)
    {
        echo "<div class='col-md-5'>
                <div class='card bg-light mb-3'>
                    <div class='card-body'>
                        <div class='text-center'>
                            <img id='preview' src='".$kwargs["IMG_DIR"].$kwargs["IMG_NAME"]."' alt='".$kwargs["IMG_NAME"]."'>
                            <hr>
                            <form id='formIMG' action='./proc/update-item.php' method='post' enctype='multipart/form-data'>
                                <div class='form-row form-group'>
                                    <input type='file' accept='image/jpeg, image/png, image/gif' onchange='loadFile(event)' required id='img' name='img'>
                                </div>
                                <div class='form-row form group'>
                                    <button type='submit' class='btn btn-success btn-block'>Change</button>
                                </div>
                            </form>
                            <script>
                                var loadFile = function(event) {
                                    var preview = document.getElementById('preview');
                                   preview.src = URL.createObjectURL(event.target.files[0]);
                                   preview.onload = function() {
                                       URL.revokeObjectURL(preview.src); // free memory
                                   }
                                };
                            </script>
                        </div>
                    </div>
                </div>
              </div>";
    }

    public static function update_show_form($item, $category)
    {
        echo "<div class='col-md-7'>
                <div class='card'>
                    <div class='card-body'>
                        <div class='update-title'>
                            <h4 class='text-center'>Update</h4>
                        </div>
                        <div class='update-form mt-4'>
                            <form id='formDetails' action='./proc/update-item.php' method='post' enctype='multipart/form-data'>
                                <div class='row'>
                                    <label class ='col-md-2 text-left' style='font-weight: bold;'>ID:</label>
                                    <p class='col-md-10'>".$item["BOOK_ID"]."</p>
                                </div>
                                <div class='form-row form-group'>
                                    <label class='col-md-2 text-left' for='name' style='font-weight: bold;'>Name:</label>
                                    <input class='form-control col-md-10' type='text' pattern='^[A-Z0-9][\w\s-^_]{3,255}$' value='".$item["BOOK_NAME"]."' required id='name' name='name'>
                                </div>
                                <div class='form-row form-group'>
                                    <label class='col-md-2 text-left' for='genre' style='font-weight: bold;'>Genre (code):</label>
                                    <select class='form-control  col-md-10' id='genre' name='code'>";
        foreach ($category as $genre)
        {
            if ($genre["CODE"]===$item["BOOK_CODE"])
                echo "<option value='".$genre["CODE"]."' selected>".$genre["NAME"]." (".$genre["CODE"].")</option>";
            else
                echo "<option value='".$genre["CODE"]."'>".$genre["NAME"]." (".$genre["CODE"].")</option>";
        }
        echo                      "</select>
                                </div>
                                <div class='form-row'>
                                    <div class='form-group col-md-6 text-left'>
                                        <label for='price' style='font-weight: bold;'>Price:</label>
                                        <input class='form-control' type='text' pattern='^(0|[1-9][0-9]{0,5})\.[0-9]{2}$' value='".$item["BOOK_PRICE"]."' required id='price' name='price'>
                                    </div>
                                    <div class='form-group col-md-6 text-left'>
                                        <label for='qty' style='font-weight: bold;'>Quantity:</label>
                                        <input class='form-control' type='number' min=0 max=32767 value=".$item["BOOK_QTY"]." required id='qty' name='quantity'>
                                    </div>
                                </div>
                                <div class='form-row form-group text-left'>
                                    <label for='description' style='font-weight: bold;'>Description:</label>
                                    <textarea class='form-control' rows=10 maxlength=65535 id='description' name='description'>".$item["BOOK_DESC"]."</textarea>
                                </div>
                                <div class='form-row form group'>
                                    <button type='submit' class='btn btn-success btn-block'>Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> 
              </div>";
    }

    public static function upload_show_category($kwargs)
    {
        echo "<div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <div class='category-title'>
                            <h4 class='text-center'>Available category</h4>
                        </div>
                        <div class='table-responsive'>
                            <table class='table table-striped'>
                                <thead>
                                <tr>
                                <th class='text-center' scope='col'>Code</th>
                                <th class='text-left' scope='col'>Name</th>
                                </tr>
                                </thead>
                                <tbody>";
        foreach ($kwargs as $genre)
        {
            echo               "<tr>
                                <td class='text-center'>".$genre["CODE"]."</td>
                                <td class='text-left'>".$genre["NAME"]."</td>
                                </tr>";
        }
        echo                   "</tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>";
    }

    public static function upload_empty_category()
    {
        echo "<div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <div class='category-title'>
                            <h4 class='text-center'>Category</h4>
                        </div>
                        <div>
                            <p class='text-center'>Nothing found.</p>
                        </div>
                    </div>
                </div>
              </div>";
    }

    public static function users_list_users($kwargs)
    {
        if ($kwargs === false)
        {
            echo "<h4 class='col text-center' style='font-weight: bold;'>No user found.</h4>";
            return;
        }
        echo "<div class='col-md-12'>
                <div class='card'>
                    <div class='card-body'>
                        <div class='table-responsive'>
                            <table class='table table-striped'>
                                <thead>
                                <tr>
                                    <th class='text-center col-md-1'>ID</th>
                                    <th class='text-left col-md-4'>Username</th>
                                    <th class='text-center col-md-3'>Role</th>
                                    <th class='col-md-2'></th>
                                    <th class='col-md-2'></th>
                                </tr>
                                </thead>
                                <tbody>";
        foreach ($kwargs as $user)
        {
            if ($user["username"]===$_SESSION["USER"]["username"])
                continue;
            echo               "<tr>
                                <td class='text-center'>".$user["id"]."</td>
                                <td class='text-left'>".$user["username"]."</td>
                                <form action='./proc/change-user-role.php' method='post'>
                                    <input type='hidden' name='id' value='".$user["id"]."'>
                                    <td class='text-center'>
                                        <select name='code' id='role'>";
            if ($user["code"]==="adm")
                echo                       "<option value='adm' selected>Admin</option>
                                            <option value='usr'>User</option>";
            else
                echo                       "<option value='adm'>Admin</option>
                                            <option value='usr' selected>User</option>";
            echo                       "</select>
                                    </td>
                                    <td class='text-center'>
                                        <button type='submit' class='btn btn-success btn-block'>Change role</button>
                                    </td>
                                </form>
                                <form action='./proc/delete-user.php' method='post'>
                                    <input type='hidden' name='id' value='".$user["id"]."'>
                                    <td class='text-center'>
                                        <button type='submit' class='btn btn-sm btn-danger'>
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </td>
                                </form>
                                </tr>";
        }
        echo                   "</tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>";
    }
}