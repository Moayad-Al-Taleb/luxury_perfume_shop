<?php
$page_title = "ادارة الفئات الرئيسية";

include "includes/header.php";

session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {

        $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

        if ($box == "index") {


            require '../connect.php';

            $sql = "SELECT * FROM main_categorys";
            $result = $conn->query($sql);



?>
            <div>
                <div class="content-header">
                    <h3>عرض جميع الفئات الرئيسية الموجودة</h3>
                    <a class="primary-btn" href="main_categories.php?box=insert">اضافة جديد</a>
                </div>
                <?php
                if ($result->num_rows > 0) { ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الخيارات</th>
                                </tr>
                            </thead>

                            <?php
                            while ($row = $result->fetch_assoc()) {

                            ?>
                                <tr>
                                    <td>#</td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td>
                                        <div class="btns-controls-container">
                                            <a class="primary-btn" href="sub_categories.php?id=<?php echo $row['id'] ?>">عرض</a>
                                            <a class="warning-btn" href="main_categories.php?box=update&&id=<?php echo $row['id']; ?>">تعديل</a>
                                            <a class="danger-btn" href="main_categories.php?box=delete&&id=<?php echo $row['id']; ?>">حذف</a>
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

                        $sql = "SELECT * FROM main_categorys WHERE name = '$name'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
            ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "INSERT INTO main_categorys (name) VALUES ('$name')";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
        <?php

                                header("refresh:2; url=main_categories.php");
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>

        <div>
            <h3 class="form-title">اضافة فئة رئيسية جديدة</h3>

            <div class="form-container">
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert' ?>" method="post">
                    <div class="input-field">
                        <div>
                            <label for="name">اسم الفئة: </label>
                            <?php echo $name_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="name" id="name"> <br>
                    </div>

                    <input class="success-btn" type="submit" value="اضافة" name="btn_send">
                </form>
            </div>
        </div>

        <?php
            } elseif ($box == "update") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "SELECT * FROM main_categorys WHERE id = '$id'";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

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

                        $sql = "SELECT * FROM main_categorys WHERE name = '$name' AND id != '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
        ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "UPDATE main_categorys SET name = '$name' WHERE id = '$id'";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">تم التعديل بنجاح </p>
        <?php
                                header("refresh:2; url=main_categories.php");
                            } else {
                                echo "Error updating record: " . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>
        <div>
            <h3 class="form-title">تعديل بيانات الفئة الرئيسية</h3>

            <div class="form-container">
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?box=update&&id=' . $id ?>" method="post">
                    <div class="input-field">
                        <div>
                            <label for="name">اسم الفئة: </label>
                            <?php echo $name_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="name" id="name" value="<?php echo $row['name']; ?>"><br>
                    </div>

                    <input class="success-btn" type="submit" value="اضافة" name="btn_send">
                </form>
            </div>
        </div>

        <?php
            } elseif ($box == "delete") {
                $id = intval($_GET['id']);

                require '../connect.php';

                $sql = "DELETE FROM main_categorys WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
        ?>

            <p class="msg success-msg ">تم الحذف بنجاح </p>

<?php

                    header("refresh:2; url=main_categories.php");
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