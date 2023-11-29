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

            <?php $box = isset($_GET['box']) ? $_GET['box'] : "index";

            if ($box == "index") {
                $customer_id = $_SESSION['id'];
                require '../connect.php';

                $sql = "SELECT * FROM fragrances WHERE customer_id = '$customer_id'";
                $result = $conn->query($sql);

            ?>
                <div class="container">
                    <div class="title-header">
                        <h3>عطوراتي المركبة</h3>
                    </div>
                    <div class="btns-container">
                        <a class="main-btn" href="?box=insert">اضافة جديد</a>
                        <a class="main-btn" href="cart.php?box=perfume_billing_management">عرض فواتير العطور المركبة</a>
                        <a class="main-btn" href="compound_perfume_reservations.php">سلة المحجوزات الخاصة بالعطور المركبة</a>
                    </div>
                    <?php
                    if ($result->num_rows > 0) {
                    ?>
                        <div class="table-wrapper">
                            <table class="user-table">
                                <thead>
                                    <th>#</th>
                                    <th>اسم العطر المركب</th>
                                    <th>الخيارات</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {

                                    ?>
                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td>
                                                <a class="second-btn" href="new_fragrance_formula.php?box=delete&&id=<?php echo $row['id']; ?>">حذف</a>
                                                <a class="main-btn" style="padding: 6px 12px;" href="complex_perfume_composition.php?id=<?php echo $row['id'] ?>">عرض</a>
                                            </td>
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
                            <p class="msg">
                                لايوجد بيانات بعد !
                            </p>
                        </div>
                    <?php
                    }
                    $conn->close();
                    ?>
                </div>
                <?php
            } elseif ($box == "insert") {

                $customer_id = $_SESSION['id'];

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $name = "";
                $name_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['name'])) {
                        $name_error = "* ";
                    } else {
                        $name = filter($_POST['name']);
                    }

                    if (!empty($name)) {
                        require '../connect.php';

                        $sql = "SELECT * FROM fragrances WHERE name = '$name' AND customer_id = '$customer_id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
                            echo "name reserved.";
                        } else {
                            $sql = "INSERT INTO fragrances (name, customer_id) VALUES ('$name', '$customer_id')";

                            if ($conn->query($sql) === TRUE) {
                ?>
                                <div class="container">
                                    <p class="msg">
                                        تمت العملية بنجاح
                                    </p>
                                </div>
                <?php

                                header("refresh:2; url=new_fragrance_formula.php");
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

                ?>

                <div class="container">
                    <div class="title-header">
                        <h3>اضافة عطر مركب جديد</h3>
                    </div>
                    <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert' ?>" method="post">
                        <div class="input-field">
                            <label for="name">اسم العطر: </label>
                            <input class="text-input" type="text" name="name" id="name"> <?php echo $name_error; ?>
                        </div>

                        <input class="main-btn" type="submit" value="Send" name="btn_send">
                    </form>
                </div>

                <?php
            } elseif ($box == "delete") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM fragrances WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                ?>
                    <div class="container">
                        <p class="msg">
                            تمت العملية بنجاح
                        </p>
                    </div>
            <?php
                    header("refresh:2; url=new_fragrance_formula.php");
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
