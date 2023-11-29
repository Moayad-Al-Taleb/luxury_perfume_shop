<?php
ob_start();

include "./includes/headers.php";
if (isset($_SESSION['id'])) {

    if ($_SESSION['type'] == 1) {

        if ($_SESSION['status'] == 0) {
?>
            <nav class="container cart-nav">
                <a class="cart-nav-link" href="cart.php">سلة المحجوزات</a>
                <a class="cart-nav-link" href="new_fragrance_formula.php">عطوراتي المركبة</a>
                <a class="cart-nav-link" href="?box=address_management">ادارة العناوين</a>
                <a class="cart-nav-link" href="?box=bills">ادارة الفواتير</a>
            </nav>

            <?php $box = isset($_GET['box']) ? $_GET['box'] : "index";

            if ($box == "index") {
                $customer_id = $_SESSION['id'];

            ?>
                <div class="container">
                    <div class="cart-title-header">
                        <h3>عرض سلة المحجوزات</h3>
                    </div>
                    <?php

                    require '../connect.php';

                    $sql = "SELECT customers_products.id, customers_products.quantity , products.name, products.size, products.milliliter, products.price, products.image_url, sub_categorys.name AS 'category' FROM customers_products, products, sub_categorys WHERE customers_products.customer_id = '$customer_id' AND customers_products.invoice_id IS NULL AND customers_products.product_id = products.id AND products.sub_category_id = sub_categorys.id";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $price_1 = 0;
                    ?>
                        <div class="user-table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>العطر</th>
                                    <th>الحجم</th>
                                    <th>الفئة</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>السعر النهائي</th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $image_url = "../admin/uploads/" . $row['image_url'];

                                        $price_2 = 0;

                                        if (empty($row['milliliter']) && !empty($row['price'])) {
                                            $price_2 = ($row['price'] * $row['quantity']);
                                        } elseif (!empty($row['milliliter']) && empty($row['price'])) {
                                            $price_2 = ((intval($row['size']) * $row['milliliter']) * $row['quantity']);
                                        }

                                        $price_1 += $price_2;
                                    ?>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <div class="cart-img-table">
                                                    <img src="<?php echo $image_url; ?>" alt="">
                                                    <span><?php echo $row['name']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $row['size']; ?></td>
                                            <td><?php echo $row['category']; ?></td>
                                            <td>
                                                <?php
                                                if (empty($row['milliliter']) && !empty($row['price'])) {
                                                    echo $row['price'] . " widget";
                                                } elseif (!empty($row['milliliter']) && empty($row['price'])) {
                                                    echo $row['milliliter'] . " milliliter";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $row['quantity']; ?></td>
                                            <td><?php echo $price_2; ?></td>
                                            <td><a class="second-btn" href="cart.php?box=delete_a_reserved_product&&id=<?php echo $row['id']; ?>">حذف</a></td>
                                        </tr>
                                    <?php
                                    }

                                    ?>

                                </tbody>
                            </table>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2rem;">
                            <p>
                                السعر الكلي: <?php echo $price_1; ?> <br>
                            </p>
                            <p>
                                تأكيد طلب الفاتورة: <a class="main-btn" href="cart.php?box=order_confirmation">تأكيد الطلب</a>
                            </p>
                        </div>

                    <?php

                    } else {
                    ?>
                        <p class="msg">
                            لايوجد منتجات محجوزة بعد !
                        </p>
                    <?php
                    }
                    $conn->close();

                    ?>
                </div>
            <?php
            } elseif ($box == "address_management") {
                require '../connect.php';

                $customer_id = $_SESSION['id'];

                $sql = "SELECT * FROM addresses WHERE customer_id = '$customer_id'";
                $result = $conn->query($sql);
            ?>
                <div class="container">
                    <div class="cart-title-header">
                        <h3>عرض العناوين</h3>
                    </div>
                    <a class="main-btn" href="?box=add_address">أضافة عنوان جديد</a>
                    <?php
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="user-table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>المدينة</th>
                                    <th>اسم الشارع</th>
                                    <th>رقم الهاتف</th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['city']; ?></td>
                                            <td><?php echo $row['street_name']; ?></td>
                                            <td><?php echo $row['phone']; ?></td>
                                            <td><a class="second-btn" href="cart.php?box=delete_address&&id=<?php echo $row['id']; ?>">حذف العنوان</a></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class=" container">
                            <p class="msg">لايوجد عناوين بعد !</p>
                        </div>
                    <?php
                    }
                    $conn->close();

                    ?>
                </div>
                <?php
            } elseif ($box == "add_address") {
                $customer_id = $_SESSION['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $city = $street_name = $phone = "";
                $city_error = $street_name_error = $phone_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['city'])) {
                        $city_error = "* ";
                    } else {
                        $city = filter($_POST['city']);
                    }

                    if (empty($_POST['street_name'])) {
                        $street_name_error = "* ";
                    } else {
                        $street_name = filter($_POST['street_name']);
                    }

                    if (empty($_POST['phone'])) {
                        $phone_error = "* ";
                    } else {
                        $phone = filter($_POST['phone']);
                    }

                    if (!empty($city) && !empty($street_name) && !empty($phone) && !empty($customer_id)) {
                        require '../connect.php';

                        $sql = "INSERT INTO addresses (city, street_name, phone, customer_id) VALUES ('$city', '$street_name', '$phone', '$customer_id')";

                        if ($conn->query($sql) === TRUE) {
                ?>
                            <div class="container">
                                <p class="msg">تمت العملية بنجاح</p>
                            </div>
                <?php

                            header("refresh:2; url=cart.php?box=address_management");
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        $conn->close();
                    }
                }

                ?>
                <div class="container">
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?box=add_address' ?>" method="post" class="form-container">
                        <h3>اضافة عنوان جديد</h3>
                        <div class="input-field">
                            <label for="city">المدينة</label>
                            <input type="text" name="city" id="city" class="text-input">
                        </div>
                        <div class="input-field">
                            <label for="street_name">اسم الشارع</label>
                            <input type="text" name="street_name" id="street_name" class="text-input">
                        </div>
                        <div class="input-field">
                            <label for="phone">رقم الهاتف</label>
                            <input type="number" name="phone" id="phone" class="text-input">
                        </div>
                        <input type="submit" value="اضافة" name="btn_send" class="main-btn">
                    </form>
                </div>
                <?php
            } elseif ($box == "delete_address") {

                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM addresses WHERE id = '$id'";

                if ($conn->query($sql) === true) {
                ?>
                    <div class="container">
                        <p class="msg">تم الحذف بنجاح</p>
                    </div>
                <?php

                    header("refresh:2; url=cart.php?box=address_management");
                } else {
                    echo "Error deleting record: " . $conn->error;
                }

                $conn->close();
            } elseif ($box == "bills") {
                $customer_id = $_SESSION['id'];

                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, shipments.method, customers.user_name FROM invoices, addresses, shipments, customers WHERE invoices.customer_id = '$customer_id' AND invoices.id IN ( SELECT customers_products.invoice_id FROM customers_products ) AND invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id ORDER BY invoices.status ASC, invoices.id DESC";
                $result = $conn->query($sql);

                ?>
                <div class="container">
                    <div class="cart-title-header">
                        <h3>عرض الفواتير</h3>
                    </div>
                    <?php
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="user-table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>تاريخ الطلب</th>
                                    <th>المدينة</th>
                                    <th>اسم الشارع</th>
                                    <th>طريقة الشحن</th>
                                    <th>الحالة</th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php

                                    while ($row = $result->fetch_assoc()) {
                                    ?>

                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['reservation_date'] ?></td>
                                            <td><?php echo $row['city'] ?></td>
                                            <td><?php echo $row['street_name'] ?></td>
                                            <td><?php echo $row['method'] ?></td>
                                            <td><?php echo ($row['status'] == 0) ? "فاتورة مطلوبة ولكن لم يتم الدفع" : "الفاتورة مدفوعة" ?></td>
                                            <td><a class="second-btn" href="cart.php?box=view_details&&id=<?php echo $row['id']; ?>">عرض</a></td>
                                        </tr>

                                    <?php
                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php

                    } else {
                    ?>
                        <div class="container">
                            <p class="msg">لايوجد فواتير مطلوبة الى الان !</p>
                        </div>
                    <?php
                    }
                    $conn->close();

                    ?>
                </div>
                <?php
            } elseif ($box == "add_to_cart") {
                $product_id = intval($_GET['id']);
                $customer_id = $_SESSION['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $quantity = "";
                $quantity_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['quantity'])) {
                        $quantity_error = "* ";
                    } else {

                        if ($_POST['quantity'] <= 0) {
                            $quantity_error = "The entry is wrong";
                        } else {
                            $quantity = filter($_POST['quantity']);
                        }
                    }

                    if (!empty($product_id) && !empty($quantity) && !empty($customer_id)) {
                        require '../connect.php';

                        $sql = "INSERT INTO customers_products(product_id, quantity, customer_id) VALUES ('$product_id', '$quantity', '$customer_id')";

                        if ($conn->query($sql) === TRUE) {
                ?>
                            <p class="msg">تم اتمام العملية بنجاح</p>
                <?php
                            header("refresh:2; url=cart.php");
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        $conn->close();
                    }
                }

                ?>
                <div class="container">
                    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=add_to_cart&&id=' . $product_id ?>" method="post">
                        <h3>يرجى اكمال عملية الحجز</h3>
                        <div class="input-field">
                            <div>
                                <label for="quantity">الكمية</label>
                                <?php echo $quantity_error; ?>
                            </div>
                            <input type="number" name="quantity" id="quantity" class="text-input">
                        </div>
                        <input type="submit" value="اتمام العملية" class="main-btn" name="btn_send">
                    </form>
                </div>
                <?php
            } elseif ($box == "order_confirmation") {
                $customer_id = $_SESSION['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $address_id = $shipment_id = "";
                $address_id_error = $shipment_id_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['address_id'])) {
                        $address_id_error = "* ";
                    } else {
                        $address_id = filter($_POST['address_id']);
                    }

                    if (empty($_POST['shipment_id'])) {
                        $shipment_id_error = "* ";
                    } else {
                        $shipment_id = filter($_POST['shipment_id']);
                    }

                    if (!empty($address_id) && !empty($shipment_id) && !empty($customer_id)) {
                        require '../connect.php';

                        $sql = "INSERT INTO invoices (address_id ,shipment_id, customer_id) VALUES ('$address_id', '$shipment_id', '$customer_id')";

                        if ($conn->query($sql) === TRUE) {
                            $invoice_id = $conn->insert_id;


                            $sql = "UPDATE customers_products SET customers_products.invoice_id = '$invoice_id' WHERE customers_products.customer_id = '$customer_id' AND customers_products.invoice_id IS NULL";

                            if ($conn->query($sql) === TRUE) {
                ?>
                                <div class="container">
                                    <p class="msg">
                                        تمت العملية بنجاح
                                    </p>
                                </div>
                <?php

                                header("refresh:2; url=cart.php");
                            } else {
                                echo "Error updating record: " . $conn->error;
                            }
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        $conn->close();
                    }
                }
                ?>
                <div class="container">

                    <h2>يرجى ادخال البيانات لتأكيد طلب الفاتورة</h2>
                    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=order_confirmation' ?>" method="post">
                        <div class="input-field">
                            <label for="address_id">العنوان: </label>
                            <select class="text-input" name="address_id" id="address_id">
                                <option value="">-</option>
                                <?php
                                require '../connect.php';
                                $sql = "SELECT * FROM addresses WHERE customer_id = '$customer_id'";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['city'] .  " - " . $row['street_name'] .  " - " . $row['phone']; ?></option>
                                <?php
                                }
                                $conn->close();
                                ?>
                            </select> <?php echo $address_id_error; ?>
                        </div>

                        <div class="input-field">
                            <label for="shipment_id">طريقة الشحن: </label>
                            <select class="text-input" name="shipment_id" id="shipment_id">
                                <option value="">-</option>
                                <?php
                                require '../connect.php';
                                $sql = "SELECT * FROM shipments";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['method']; ?></option>
                                <?php
                                }
                                $conn->close();
                                ?>
                            </select> <?php echo $shipment_id_error; ?>
                        </div>


                        <input class="main-btn" type="submit" value="تأكيد" name="btn_send">
                    </form>
                </div>
                <?php
            } elseif ($box == "delete_a_reserved_product") {

                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM customers_products WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                ?>
                    <p class="msg">
                        تم الحذف بنجاح
                    </p>
                <?php

                    header("refresh:2; url=cart.php");
                } else {
                    echo "Error deleting record: " . $conn->error;
                }

                $conn->close();
            } elseif ($box == "view_details") {
                $id = intval($_GET['id']);
                $invoice_id = $id;
                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, addresses.phone, shipments.method, customers.first_name, customers.last_name, customers.user_name, customers.phone FROM invoices, addresses, shipments, customers WHERE invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id AND invoices.id = '$invoice_id'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                $conn->close();

                $status = ($row['status'] == 0) ? 0 : 1;
                ?>
                <div class="container">
                    <div class="title-header">
                        <h3>بيانات الفاتورة</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin: 16px 8px;">
                        <p>
                            تاريخ الحجز: <?php echo $row['reservation_date']; ?>
                        </p>
                        <?php ?>
                        <p>الحالة: <?php echo ($row['status'] == 0) ? "فاتورة مطلوبة ولكن غير مدفوعة" : "فاتورة مدفوعة" ?></p>

                        <p>المدينة: <?php echo $row['city']; ?></p>
                        <p>اسم الشارع: <?php echo $row['street_name']; ?></p>
                        <p>طريقة الشحن: <?php echo $row['method']; ?></p>

                        <p>اسم الزبون: <?php echo $row['first_name'] . ' ' . $row['last_name']; ?></p>
                        <p>اسم المستخدم: <?php echo $row['user_name']; ?></p>

                        <p><?php echo ($status == 1) ? "تاريخ التوصيل: " . $row['delivery_date'] : "-" ?></p>
                    </div>
                    <div>
                        <div class="title-header">
                            <h3>منتجات الفاتورة</h3>
                        </div>
                        <?php
                        require '../connect.php';

                        $sql = "SELECT customers_products.*, products.* FROM customers_products, products WHERE customers_products.product_id = products.id AND customers_products.invoice_id = '$invoice_id';";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $price_1 = 0;
                        ?>
                            <div class="table-wrapper">
                                <table class="user-table">
                                    <thead>
                                        <th>#</th>
                                        <th>العطر</th>
                                        <th>التفاصيل</th>
                                        <th>الرمز</th>
                                        <th>الرائحة</th>
                                        <th>الحجم</th>
                                        <th>صلاحية الانتهاء</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>السعر النهائي</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            $price_2 = 0;

                                            if (empty($row['milliliter']) && !empty($row['price'])) {
                                                $price_2 = ($row['price'] * $row['quantity']);
                                            } elseif (!empty($row['milliliter']) && empty($row['price'])) {
                                                $price_2 = ((intval($row['size']) * $row['milliliter']) * $row['quantity']);
                                            }

                                            $price_1 += $price_2;
                                        ?>

                                            <tr>
                                                <td>#</td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['details']; ?></td>
                                                <td><?php echo $row['code']; ?></td>
                                                <td><?php echo $row['scent']; ?></td>
                                                <td><?php echo $row['size']; ?></td>
                                                <td><?php echo $row['expiration_date']; ?></td>
                                                <td><?php
                                                    if (empty($row['milliliter']) && !empty($row['price'])) {
                                                        echo $row['price'] . " widget";
                                                    } elseif (!empty($row['milliliter']) && empty($row['price'])) {
                                                        echo $row['milliliter'] . " milliliter";
                                                    }
                                                    ?></td>
                                                <td><?php echo $row['quantity']; ?></td>
                                                <td><?php echo $price_2; ?></td>
                                            </tr>

                                        <?php

                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <h3>السعر الكلي: <?php echo $price_1; ?></h3>
                        <?php
                        } else {
                            echo "0 results";
                        }
                        $conn->close();

                        ?>
                    </div>
                </div>
            <?php
            } elseif ($box == "perfume_billing_management") {
                $customer_id = $_SESSION['id'];

                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, shipments.method, customers.user_name FROM invoices, addresses, shipments, customers WHERE invoices.customer_id = '$customer_id' AND invoices.id IN ( SELECT invoices_fragrances.invoice_id FROM invoices_fragrances ) AND invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id ORDER BY invoices.status ASC, invoices.id DESC";
                $result = $conn->query($sql);

            ?>
                <div class="container">
                    <div class="cart-title-header">
                        <h3>عرض الفواتير</h3>
                    </div>
                    <?php
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="user-table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>تاريخ الطلب</th>
                                    <th>المدينة</th>
                                    <th>اسم الشارع</th>
                                    <th>طريقة الشحن</th>
                                    <th>الحالة</th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php

                                    while ($row = $result->fetch_assoc()) {
                                    ?>

                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['reservation_date'] ?></td>
                                            <td><?php echo $row['city'] ?></td>
                                            <td><?php echo $row['street_name'] ?></td>
                                            <td><?php echo $row['method'] ?></td>
                                            <td><?php echo ($row['status'] == 0) ? "فاتورة مطلوبة ولكن لم يتم الدفع" : "الفاتورة مدفوعة" ?></td>
                                            <td><a class="second-btn" href="?box=view_2&&id=<?php echo $row['id']; ?>">عرض</a></td>
                                        </tr>

                                    <?php
                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php

                    } else {
                    ?>
                        <div class="container">
                            <p class="msg">لايوجد فواتير مطلوبة الى الان !</p>
                        </div>
                    <?php
                    }
                    $conn->close();
                    ?>
                </div>
            <?php
            } elseif ($box == "view_2") {
                $id = intval($_GET['id']);
                $invoice_id = $id;
                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, addresses.phone, shipments.method, customers.first_name, customers.last_name, customers.user_name, customers.phone FROM invoices, addresses, shipments, customers WHERE invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id AND invoices.id = '$invoice_id'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                $conn->close();

                $status = ($row['status'] == 0) ? 0 : 1;
            ?>
                <div class="container">
                    <div class="title-header">
                        <h3>بيانات الفاتورة</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin: 16px 8px;">
                        <p>
                            تاريخ الحجز: <?php echo $row['reservation_date']; ?>
                        </p>
                        <?php ?>
                        <p>الحالة: <?php echo ($row['status'] == 0) ? "فاتورة مطلوبة ولكن غير مدفوعة" : "فاتورة مدفوعة" ?></p>

                        <p>المدينة: <?php echo $row['city']; ?></p>
                        <p>اسم الشارع: <?php echo $row['street_name']; ?></p>
                        <p>طريقة الشحن: <?php echo $row['method']; ?></p>

                        <p><?php echo ($status == 1) ? "تاريخ التوصيل: " . $row['delivery_date'] : "-" ?></p>
                    </div>
                    <div>
                        <div class="title-header">
                            <h3>منتجات الفاتورة</h3>
                        </div>
                        <?php
                        require '../connect.php';
                        $sql = "SELECT invoices_fragrances.*, fragrances.id AS ID,fragrances.name FROM invoices_fragrances, fragrances WHERE invoices_fragrances.fragrance_id = fragrances.id AND invoices_fragrances.invoice_id = '$invoice_id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $price_1 = 0;
                        ?>
                            <div class="table-wrapper">
                                <table class="user-table">
                                    <thead>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>View</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = $result->fetch_assoc()) {
                                            $fragrance_id = $row['ID'];

                                            $sql2 = "SELECT perfume_compositions.*, products.milliliter, (perfume_compositions.volume * products.milliliter) AS 'X' FROM perfume_compositions, products WHERE perfume_compositions.product_id = products.id AND perfume_compositions.fragrance_id = '$fragrance_id'";
                                            $result2 = $conn->query($sql2);

                                            $price_2 = 0;

                                            while ($row2 = $result2->fetch_assoc()) {
                                                $price_2 += $row2['X'];
                                            }

                                            $price_2 = ($row['quantity'] * $price_2);

                                            $price_1 += $price_2;
                                        ?>

                                            <tr>
                                                <td>#</td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['quantity']; ?></td>
                                                <td><?php echo $price_2; ?></td>
                                                <td><a class="second-btn" href="cart.php?box=compositions&&id=<?php echo $row['id']; ?>">عرض</a></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <h3>السعر الكلي: <?php echo $price_1; ?></h3>
                        <?php
                        } else {
                            echo "0 results";
                        }
                        $conn->close();

                        ?>
                    </div>
                </div>
            <?php
            } elseif ($box == "compositions") {
                $id = intval($_GET['id']);
                $fragrance_id = $id;


                require '../connect.php';

                $sql = "SELECT perfume_compositions.*, products.name, products.milliliter, (perfume_compositions.volume * products.milliliter) AS 'Price' FROM perfume_compositions, products WHERE perfume_compositions.product_id = products.id AND perfume_compositions.fragrance_id = '$fragrance_id'";
                $result = $conn->query($sql);
            ?>
                <div class="container">
                    <div class="title-header">
                        <h3>تركيب العطر</h3>
                    </div>
                    <?php
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>اسم العطر</th>
                                    <th>الكمية في الميلي</th>
                                    <th>السعر في الميلي</th>
                                    <th>السعر</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['volume']; ?></td>
                                            <td><?php echo $row['milliliter']; ?></td>
                                            <td><?php echo $row['Price']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    <?php
                    } else {
                    ?>
                    <?php
                    } ?>
                </div>
            <?php
            }
        } else {

            ?>


            <div class="container">
                <p class="msg">
                    Your account status is frozen.
                </p>
            </div>

        <?php
        }
    } else {

        ?>
        <div class="container">
            <p class="msg">
                You cannot login to the site as an admin account. Create an account as a user first. <a href="../register.php">REGISTER</a>
            </p>
        </div>


    <?php
    }
} else {
    ?>

    <div class="container">
        <p class="msg">
            يرجى تسجيل الدخول أولا <a href="../login.php">تسجيل الدخول</a>
        </p>
    </div>
<?php
}
include "./includes/footer.php";
ob_end_flush();
