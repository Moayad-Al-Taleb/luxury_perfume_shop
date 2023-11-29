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
                <a class="cart-nav-link" href="cart.php?box=address_management">ادارة العناوين</a>
                <a class="cart-nav-link" href="cart.php?box=bills">ادارة الفواتير</a>
            </nav>

            <?php

            $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

            if ($box == "index") {

            ?>

                <div class="container">
                    <div class="title-header">
                        <h3>حجز عطر مركب</h3>
                    </div>

                    <a class="main-btn" href="compound_perfume_reservations.php?box=insert">اضافة</a>

                    <?php

                    $customer_id = $_SESSION['id'];

                    require '../connect.php';

                    $sql = "SELECT invoices_fragrances.*, fragrances.id AS ID,fragrances.name FROM invoices_fragrances, fragrances WHERE invoices_fragrances.fragrance_id = fragrances.id AND invoices_fragrances.invoice_id IS NULL AND invoices_fragrances.customer_id = '$customer_id'";
                    $result = $conn->query($sql);
                    ?>

                    <?php

                    if ($result->num_rows > 0) {
                        $price_1 = 0;
                    ?>

                        <div class="table-wrapper">


                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>اسم العطر</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الخيارات</th>
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
                                            <td>
                                                <a class="second-btn" href="compound_perfume_reservations.php?box=delete&&id=<?php echo $row['id']; ?>">حذف</a>
                                                <a class="main-btn" style="padding: 6px 12px;" href="compound_perfume_reservations.php?box=compositions&&id=<?php echo $row['ID']; ?>">عرض</a>
                                            </td>
                                        </tr>

                                    <?php

                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2rem;">
                            <p>السعر الكلي: <?php echo $price_1; ?></p>

                            <p>تأكيد طلب الفاتورة: <a class="main-btn" href=" compound_perfume_reservations.php?box=order_confirmation">تأكيد الطلب</a></p>
                        </div>


                    <?php

                    } else {
                    ?>
                        <div style="margin-top: 22px;">
                            <p class="msg">
                                لايوجد عطور مركبة محجوزة بعد
                            </p>
                        </div>
                    <?php
                    }
                    $conn->close();


                    ?>
                </div>

                <?php

            } elseif ($box == "insert") {

                $customer_id  = $_SESSION['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $quantity = $fragrance_id = "";
                $quantity_error = $fragrance_id_error = "";

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

                    if (empty($_POST['fragrance_id'])) {
                        $fragrance_id_error = "* ";
                    } else {
                        $fragrance_id = filter($_POST['fragrance_id']);
                    }

                    if (!empty($quantity) && !empty($fragrance_id) && !empty($customer_id)) {
                        require '../connect.php';

                        $sql = "INSERT INTO invoices_fragrances (quantity , fragrance_id, customer_id) VALUES ('$quantity', '$fragrance_id', '$customer_id')";

                        if ($conn->query($sql) === TRUE) {
                ?>
                            <div class="container">
                                <p class="msg">
                                    تمت العملية بنجاح
                                </p>
                            </div>
                <?php

                            header("refresh:2; url=compound_perfume_reservations.php");
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        $conn->close();
                    }
                }

                ?>


                <div class="container">
                    <div class="title-header">
                        <h3>اضافة البيانات المطلوبة لاتمام العملية</h3>
                    </div>

                    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert&&id=' . $fragrance_id ?>" method="post">

                        <div class="input-field">
                            <label for="fragrance_id">اختيار العطر المركب: </label>
                            <select class="text-input" name="fragrance_id" id="fragrance_id">
                                <option value="">-</option>
                                <?php
                                require '../connect.php';
                                $sql = "SELECT fragrances.id, fragrances.name FROM fragrances WHERE fragrances.customer_id = '$customer_id'";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php
                                }
                                $conn->close();
                                ?>
                            </select> <?php echo $fragrance_id_error; ?>
                        </div>
                        <div class="input-field">
                            <label for="quantity">الكمية: </label>
                            <input class="text-input" type="number" name="quantity" id="quantity"> <?php echo $quantity_error; ?>
                        </div>
                        <input type="submit" class="main-btn" value="اضافة" name="btn_send">
                    </form>
                </div>

                <?php

                ?>

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
                            // echo "New record created successfully";

                            $invoice_id = $conn->insert_id;


                            $sql = "UPDATE invoices_fragrances SET invoices_fragrances.invoice_id = '$invoice_id' WHERE invoices_fragrances.customer_id = '$customer_id' AND invoices_fragrances.invoice_id IS NULL";

                            if ($conn->query($sql) === TRUE) {
                ?>
                                <div class="container">
                                    <p class="msg">
                                        تمت العملية بنجاح
                                    </p>
                                </div>
                <?php

                                header("refresh:2; url=compound_perfume_reservations.php");
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
                    <div class="title-header">
                        <h3>ادخال بيانات التوصيل لاتمام العملية</h3>
                    </div>

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


                        <input type="submit" class="main-btn" value="اتمام العملية" name="btn_send">
                    </form>
                </div>

                <?php

            } elseif ($box == "compositions") {

                $id = intval($_GET['id']);
                $fragrance_id = $id;


                require '../connect.php';

                $sql = "SELECT perfume_compositions.*, products.name, products.milliliter, (perfume_compositions.volume * products.milliliter) AS 'Price' FROM perfume_compositions, products WHERE perfume_compositions.product_id = products.id AND perfume_compositions.fragrance_id = '$fragrance_id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                ?>

                    <center>
                        <table border="1" style="width: 75%;">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Volume</th>
                                <th>Milliliter</th>
                                <th>Price</th>
                            </tr>

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

                        </table>
                    </center>

            <?php
                }
            } elseif ($box == "delete") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM invoices_fragrances WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record deleted successfully";

                    header("refresh:2; url=compound_perfume_reservations.php");
                } else {
                    echo "Error deleting record: " . $conn->error;
                }

                $conn->close();
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
