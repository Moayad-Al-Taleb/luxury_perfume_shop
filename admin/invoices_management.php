<?php
$page_title = "ادارة الفواتير";

include "includes/header.php";
ob_start();

session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {

        $box = (isset($_GET['box'])) ? $_GET['box'] : "Invoices_requested";

?>
        <div>

            <div class="content-header">
                <div>
                    <?php
                    require '../connect.php';

                    $sql = "SELECT id, first_name, last_name, user_name FROM customers WHERE type = 1";
                    $result = $conn->query($sql);

                    ?>

                    <form class="search-form" action="<?php echo $_SERVER['PHP_SELF'] . '?box=specific_customer' ?>" method="post">
                        <div class="input-field">
                            <label for="specific_customer">البحث عن زبون معين: </label>
                            <select class="input-text shadow" name="specific_customer" id="specific_customer">
                                <option value="">-</option>
                                <?php

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {

                                ?>

                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['user_name'] . ' ' . '(' . $row['first_name'] . ' ' . $row['last_name'] . ')' ?></option>

                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <input class="warning-btn shadow" type="submit" value="بحث" name="btn_search">
                    </form>

                </div>

                <div>
                    <a class="primary-btn" href="invoices_management.php?box=Invoices_requested">فواتير غير مدفوعة</a>
                    <a class="success-btn" href="invoices_management.php?box=invoices_paid">فواتير مدفوعة</a>
                </div>
            </div>




            <?php
            if ($box == "Invoices_requested") {
            ?>

                <h2>الفواتير الغير مدفوعة</h2>
                <?php

                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, shipments.method, customers.user_name FROM invoices, addresses, shipments, customers WHERE invoices.status = 0 AND invoices.id IN ( SELECT customers_products.invoice_id FROM customers_products ) AND invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id ORDER BY invoices.id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                ?>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>تاريخ الحجز</th>
                                    <th>المدينة</th>
                                    <th>اس الشارع</th>
                                    <th>طريقة الشحن</th>
                                    <th>اسم المستخدم</th>
                                    <th>الخيارات</th>
                                </tr>
                            </thead>
                            <?php

                            while ($row = $result->fetch_assoc()) {
                            ?>

                                <tr>
                                    <td>#</td>
                                    <td><?php echo $row['reservation_date'] ?></td>
                                    <td><?php echo $row['city'] ?></td>
                                    <td><?php echo $row['street_name'] ?></td>
                                    <td><?php echo $row['method'] ?></td>
                                    <td><?php echo $row['user_name'] ?></td>
                                    <td><a class="primary-btn" href="invoices_management.php?box=view_details&&id=<?php echo $row['id']; ?>">عرض </a></td>
                                </tr>
                            <?php
                            }

                            ?>
                        </table>
                    </div>

                <?php

                } else {
                ?>

                    <p class="msg warning-msg">لايوجد بيانات لعرضها</p>

                <?php
                }
                $conn->close();

                ?>

            <?php
            } elseif ($box == "invoices_paid") {
            ?>

                <h2>الفواتير المدفوعة</h2>
                <?php

                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, shipments.method, customers.user_name FROM invoices, addresses, shipments, customers WHERE invoices.status = 1 AND invoices.id IN ( SELECT customers_products.invoice_id FROM customers_products ) AND invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id ORDER BY invoices.id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                ?>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>تاريخ الحجز</th>
                                    <th>المدينة</th>
                                    <th>اس الشارع</th>
                                    <th>طريقة الشحن</th>
                                    <th>اسم المستخدم</th>
                                    <th>الخيارات</th>
                                </tr>
                            </thead>

                            <?php

                            while ($row = $result->fetch_assoc()) {
                            ?>

                                <tr>
                                    <td>#</td>
                                    <td><?php echo $row['reservation_date'] ?></td>
                                    <td><?php echo $row['city'] ?></td>
                                    <td><?php echo $row['street_name'] ?></td>
                                    <td><?php echo $row['method'] ?></td>
                                    <td><?php echo $row['user_name'] ?></td>
                                    <td><a class="primary-btn" href="invoices_management.php?box=view_details&&id=<?php echo $row['id']; ?>">عرض </a></td>
                                </tr>

                            <?php
                            }

                            ?>
                        </table>
                    </div>

                <?php

                } else {
                ?>

                    <p class="msg warning-msg">لايوجد بيانات لعرضها</p>

                <?php
                }
                $conn->close();

                ?>

                <?php
            } elseif ($box == "specific_customer") {
                $specific_customer = (!empty($_POST['specific_customer'])) ? $_POST['specific_customer'] : null;

                if ($specific_customer != null) {
                ?>

                    <h1>الفواتير لزبون معين</h1>

                    <?php

                    require '../connect.php';

                    $sql = "SELECT invoices.*, addresses.city, addresses.street_name, shipments.method, customers.user_name FROM invoices, addresses, shipments, customers WHERE invoices.customer_id = '$specific_customer' AND invoices.id IN ( SELECT customers_products.invoice_id FROM customers_products ) AND invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id ORDER BY invoices.status ASC, invoices.id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {

                    ?>

                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>تاريخ الحجز</th>
                                        <th>المدينة</th>
                                        <th>اسم الشارع</th>
                                        <th>طريقة الشحن</th>
                                        <th>اسم المستخدم</th>
                                        <th>الحالة</th>
                                        <th>الخيارات</th>
                                    </tr>
                                </thead>

                                <?php

                                while ($row = $result->fetch_assoc()) {
                                ?>

                                    <tr>
                                        <td>#</td>
                                        <td><?php echo $row['reservation_date'] ?></td>
                                        <td><?php echo $row['city'] ?></td>
                                        <td><?php echo $row['street_name'] ?></td>
                                        <td><?php echo $row['method'] ?></td>
                                        <td><?php echo $row['user_name'] ?></td>
                                        <td><?php echo ($row['status'] == 0) ? "الفاتورة لم تدفع بعد" : "الفاتورة مدفوعة" ?></td>
                                        <td><a class="primary-btn" href="invoices_management.php?box=view_details&&id=<?php echo $row['id']; ?>">عرض </a></td>
                                    </tr>

                                <?php
                                }

                                ?>
                            </table>
                        </div>

                    <?php

                    } else {
                    ?>

                        <p class="msg warning-msg">لايوجد بيانات لعرضها</p>

                    <?php
                    }
                    $conn->close();

                    ?>

                <?php
                } else {

                ?>

                    <p class="warning-msg msg">يرجى اختيار زبون معين لعرض فواتيره</p>

                <?php
                }
                ?>

            <?php
            } elseif ($box == "view_details") {
                $id = intval($_GET['id']);
                $invoice_id = $id;

                require '../connect.php';

                $sql = "SELECT invoices.*, addresses.city, addresses.street_name, addresses.phone, shipments.method, customers.first_name, customers.last_name, customers.user_name, customers.phone FROM invoices, addresses, shipments, customers WHERE invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id AND invoices.id = '$invoice_id'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $conn->close();

                $status = ($row['status'] == 0) ? 0 : 1
            ?>

                <div class="content-header">
                    <h2>عرض تفاصيل الفاتورة</h2>

                    <?php
                    if ($status == 0) {
                    ?>
                        <a class="success-btn shadow" href="invoices_management.php?box=confirm&id=<?php echo $id; ?>">تأكيد عملية الدفع</a>
                    <?php
                    } elseif ($status == 1) {
                    ?>
                        <a class="danger-btn shadow" href="print_pdf.php?id=<?php echo $id; ?>">طباعة الفاتورة</a>
                    <?php
                    }
                    ?>
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

                <div class="content-header">
                    <h2>منتجات الفاتورة</h2>
                </div>
                <?php
                require '../connect.php';

                $sql = "SELECT customers_products.*, products.* FROM customers_products, products WHERE customers_products.product_id = products.id AND customers_products.invoice_id = '$invoice_id';";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $price_1 = 0;
                ?>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم العطر</th>
                                    <th>التفاصيل</th>
                                    <th>الرمز</th>
                                    <th>الرائحة</th>
                                    <th>الحجم</th>
                                    <th>الصلاحية</th>
                                    <th>السعر في الميلي</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                </tr>
                            </thead>

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

                        </table>
                    </div>

                    <p class="msg success-msg">
                        السعر الاجمالي: <?php echo $price_1; ?>
                    </p>
        </div>

    <?php
                } else {
    ?>

        <p class="msg warning-msg">لايوجد بيانات لعرضها</p>

    <?php
                }
                $conn->close();


    ?>

    <?php

            } elseif ($box == "confirm") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $delivery_date = date("Y-m-d");

                $sql = "UPDATE invoices SET status = 1, delivery_date = '$delivery_date' WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
    ?>

        <p class="msg success-msg">
            تمت العملية بنجاح
        </p>

<?php
                    header("refresh:2; url=invoices_management.php?box=view_details&id=" . $id);
                } else {
                    echo "Error updating record: " . $conn->error;
                }

                $conn->close();
            }
        } else {
            echo "Unauthorized entry.";
            header("refresh:2; url=../login.php");
        }
    }

    ob_end_flush();
    include "includes/footer.php"
?>