<?php
ob_start();

include "./includes/headers.php";
if (isset($_SESSION['id'])) {

    if ($_SESSION['type'] == 1) {

        if ($_SESSION['status'] == 0) {

?>
            <nav class="container cart-nav">
                <a class="cart-nav-link" href="cart.php">سلة المحجوزات</a>
                <a class="cart-nav-link" href="new_fragrance_formula.php">سلة العطور المركبة</a>
                <a class="cart-nav-link" href="cart.php?box=address_management">ادارة العناوين</a>
                <a class="cart-nav-link" href="cart.php?box=bills">ادارة الفواتير</a>
            </nav>

            <?php

            $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

            if ($box == "index") {

                $id = intval($_GET['id']);
                $fragrance_id = $id;
                require '../connect.php';

                $sql = "SELECT perfume_compositions.*, products.name, products.milliliter, (perfume_compositions.volume * products.milliliter) AS 'Price' FROM perfume_compositions, products WHERE perfume_compositions.product_id = products.id AND perfume_compositions.fragrance_id = '$fragrance_id'";
                $result = $conn->query($sql);

            ?>
                <div class="container">
                    <div class="title-header">
                        <h3>تركيبات العطر</h3>
                    </div>
                    <a class="main-btn" href="complex_perfume_composition.php?box=insert&&id=<?php echo $id; ?>">اضافة</a>

                    <?php
                    if ($result->num_rows > 0) {

                    ?>
                        <div class="table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>اسم العطر</th>
                                    <th>الكمية</th>
                                    <th>السعر في الميلي</th>
                                    <th>السعر </th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php

                                    $price = 0;

                                    while ($row = $result->fetch_assoc()) {

                                        $price += $row['Price'];
                                    ?>

                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['volume']; ?></td>
                                            <td><?php echo $row['milliliter']; ?></td>
                                            <td><?php echo $row['Price']; ?></td>
                                            <td><a class="second-btn" href="complex_perfume_composition.php?box=delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف</a></td>
                                        </tr>
                                    <?php

                                    }

                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <h4>السعر الكلي: <?php echo $price; ?></h4>


                    <?php
                    } else {
                    ?>
                        <p class="msg">لايوجد بيانات لعرضها</p>
                    <?php
                    }
                    $conn->close();
                    ?>
                </div>
                <?php
            } elseif ($box == "insert") {

                $fragrance_id  = $_GET['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $volume = $product_id = "";
                $volume_error = $product_id_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['volume'])) {
                        $volume_error = "* ";
                    } else {
                        if ($_POST['volume'] <= 0) {
                            $volume_error = "The entry is wrong";
                        } else {
                            $volume = filter($_POST['volume']);
                        }
                    }

                    if (empty($_POST['product_id'])) {
                        $product_id_error = "* ";
                    } else {
                        $product_id = filter($_POST['product_id']);
                    }

                    if (!empty($volume) && !empty($product_id) && !empty($fragrance_id)) {
                        require '../connect.php';

                        $sql = "INSERT INTO perfume_compositions (volume , product_id, fragrance_id) VALUES ('$volume', '$product_id', '$fragrance_id')";

                        if ($conn->query($sql) === TRUE) {
                ?>
                            <div class="container">
                                <p class="msg">تمت العملية بنجاح</p>
                            </div>
                <?php

                            header("refresh:2; url=complex_perfume_composition.php?id=" . $fragrance_id);
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }

                        $conn->close();
                    }
                }

                ?>


                <div class="container">
                    <div class="title-header">
                        <h3>اتمام عملية الاضافة</h3>
                    </div>
                    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert&&id=' . $fragrance_id ?>" method="post">
                        <div class="input-field">
                            <label for="volume">الكمية في الميلي: </label>
                            <input class="text-input" type="number" name="volume" id="volume"> <?php echo $volume_error; ?>
                        </div>

                        <div class="input-field">
                            <label for="product_id">اختيار المنتج: </label>
                            <select class="text-input" name="product_id" id="product_id">
                                <option value="">-</option>
                                <?php
                                require '../connect.php';
                                $sql = "SELECT products.id, products.name, products.milliliter FROM products WHERE products.price IS NULL";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name'] .  " - " . $row['milliliter']; ?></option>
                                <?php
                                }
                                $conn->close();
                                ?>
                            </select> <?php echo $product_id_error; ?>
                        </div>

                        <input class="main-btn" type="submit" value="اضافة" name="btn_send">
                    </form>
                </div>

                <?php

            } elseif ($box == "delete") {
                $id = intval($_GET['id']);
                $id2 = intval($_GET['id2']);

                require '../connect.php';

                $sql = "DELETE FROM perfume_compositions WHERE id = '$id2'";

                if ($conn->query($sql) === TRUE) {
                ?>
                    <div class="container">
                        <p class="msg">
                            تمت العملية بنجاح
                        </p>
                    </div>
            <?php
                    header("refresh:2; url=complex_perfume_composition.php?id=" . $id);
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
