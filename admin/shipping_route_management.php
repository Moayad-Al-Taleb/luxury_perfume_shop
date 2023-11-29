<?php


$page_title = "ادارة طرق الشحن";

include "includes/header.php";


session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {

        $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

        if ($box == "index") {

?>
            <div>
                <div class="content-header">
                    <h3>
                        عرض جميع طرق الشحن المتاحة
                    </h3>
                    <a class="primary-btn" href="shipping_route_management.php?box=insert">اضافة جديد</a>
                </div>
                <?php

                require '../connect.php';

                $sql = "SELECT * FROM shipments";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>طريقة الشحن</th>
                                    <th>الخيارات</th>
                                </tr>
                            </thead>

                            <?php
                            while ($row = $result->fetch_assoc()) {

                            ?>
                                <tr>
                                    <td>#</td>
                                    <td><?php echo $row['method']; ?></td>
                                    <td>
                                        <div class="btns-controls-container">
                                            <a class="warning-btn" href="shipping_route_management.php?box=update&&id=<?php echo $row['id']; ?>">تعديل</a>
                                            <a class="danger-btn" href="shipping_route_management.php?box=delete&&id=<?php echo $row['id']; ?>">حذف</a>
                                        </div>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>
                        </table>
                    </div>
            </div>
        <?php

                } else {
        ?>

            <p class="msg warning-msg">لايوجد بيانات لعرضها</p>

            <?php
                }
                $conn->close();
            } elseif ($box == "insert") {

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $method = "";
                $method_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['method'])) {
                        $method_error = "* ";
                    } else {
                        $method = filter($_POST['method']);
                    }

                    if (!empty($method)) {
                        require '../connect.php';

                        $sql = "SELECT * FROM shipments WHERE method = '$method'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
            ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "INSERT INTO shipments (method) VALUES ('$method')";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
        <?php

                                header("refresh:2; url=shipping_route_management.php");
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>

        <div>
            <h3>اضافة طريقة شحن جديدة</h3>

            <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert' ?>" method="post">
                <div class="input-field">
                    <div>
                        <label for="method">طريقة الشحن: </label>
                        <?php echo $method_error; ?>
                    </div>
                    <input class="input-text shadow" type="text" name="method" id="method"> <br>
                </div>

                <input class="success-btn shadow" type="submit" value="اضافة" name="btn_send">
            </form>
        </div>

        <?php
            } elseif ($box == "update") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "SELECT * FROM shipments WHERE id = '$id'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $method = "";
                $method_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['method'])) {
                        $method_error = "* ";
                    } else {
                        $method = filter($_POST['method']);
                    }

                    if (!empty($method)) {

                        $sql = "SELECT * FROM shipments WHERE method = '$method' AND id != '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
        ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "UPDATE shipments SET method = '$method' WHERE id = '$id'";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">
                            تم التعديل بنجاح
                        </p>
        <?php

                                header("refresh:2; url=shipping_route_management.php");
                            } else {
                                echo "Error updating record: " . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>

        <div>
            <h3>تعديل بيانات طريقة الشحن</h3>

            <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=update&&id=' . $id ?>" method="post">
                <div class="input-field">
                    <div>
                        <label for="method">طريقة الشحن: </label>
                        <?php echo $method_error; ?>
                    </div>
                    <input class="input-text shadow" type="text" value="<?php echo $row['method'] ?>" name="method" id="method"> <br>
                </div>

                <input class="success-btn shadow" type="submit" value="تعديل" name="btn_send">
            </form>
        </div>

        <?php
            } elseif ($box == "delete") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM shipments WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
        ?>
            <p class="msg success-msg">
                تم الحذف بنجاح
            </p>
<?php
                    header("refresh:2; url=shipping_route_management.php");
                } else {
                    echo "Error deleting record: " . $conn->error;
                }

                $conn->close();
            }
        } else {
            echo "Unauthorized entry.";
            header("refresh:2; url=../login.php");
        }
    }
    include "includes/footer.php" ?>