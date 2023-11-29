<?php
$page_title = "ادارة الفئات الفرعية";

include "includes/header.php";

session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {

        $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

        if ($box == "index") {

            $id = (isset($_GET['id'])) ? intval($_GET['id']) : null;

            if ($id != null) {

?>
                <div>
                    <div class="content-header">
                        <h3>عرض جميع الفئات الفرعية الموجودة</h3>
                        <a class="primary-btn" href="sub_categories.php?box=insert&&id=<?php echo $id; ?>">اضافة جديد</a>
                    </div>
                    <?php

                    require '../connect.php';

                    $sql = "SELECT * FROM sub_categorys WHERE main_category_id = '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {

                    ?>
                        <div class="table-wrappe">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم </th>
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
                                            <div class="btns-controls container">
                                                <a class="primary-btn" href="products_management.php?id=<?php echo $row['id'] ?>">عرض</a>
                                                <a class="warning-btn" href="sub_categories.php?box=update&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">تعديل</a>
                                                <a class="danger-btn" href="sub_categories.php?box=delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف</a>
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
                <p class="msg warning-msg">
                    لايوجد بيانات لعرضها
                </p>
                <?php
                    }
                    $conn->close();
                } else {
                    echo "Invalid entry.";
                }
            } elseif ($box == "insert") {

                $id = intval($_GET['id']);
                $main_category_id = $id;

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

                    if (!empty($name) && !empty($main_category_id)) {
                        require '../connect.php';

                        $sql = "SELECT * FROM sub_categorys WHERE name = '$name'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
                ?>
                    <p class="msg error-msg">هذا الاسم موجود بالفعل</p>
                    <?php
                        } else {
                            $sql = "INSERT INTO sub_categorys (name, main_category_id) VALUES ('$name', '$main_category_id')";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">تم الاضافة بنجاح</p>
        <?php

                                header("refresh:2; url=sub_categories.php?id=" . $id);
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>
        <div>
            <h3 class="form-title">اضافة فئة فرعية جديدة</h3>

            <div class="form-container">
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert&&id=' . $id ?>" method="post">
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
                $id2 = intval($_GET['id2']);

                require '../connect.php';

                $sql = "SELECT * FROM sub_categorys WHERE id = '$id2'";
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

                        $sql = "SELECT * FROM main_categorys WHERE name = '$name' AND id != '$id2'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
        ?>
                    <p class="msg error-msg">هذا الاسم موجود بالفعل</p>
                    <?php
                        } else {
                            $sql = "UPDATE sub_categorys SET name = '$name' WHERE id = '$id2'";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">تم التعديل بنجاح</p>
        <?php

                                header("refresh:2; url=sub_categories.php?id=" . $id);
                            } else {
                                echo "Error updating record: " . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>

        <div>
            <h3 class="form-title">تعديل بيانات الفئة الفرعية</h3>

            <div class="form-container">
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?box=update&&id=' . $id . '&&id2=' . $id2 ?>" method="post">
                    <div class="input-field">
                        <div>
                            <label for="name">اسم الفئة: </label>
                            <?php echo $name_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="name" id="name" value="<?php echo $row['name']; ?>"> <br>
                    </div>

                    <input class="success-btn" type="submit" value="تعديل" name="btn_send">
                </form>
            </div>
        </div>

        <?php
            } elseif ($box == "delete") {
                $id = intval($_GET['id']);
                $id2 = intval($_GET['id2']);

                require '../connect.php';

                $sql = "DELETE FROM sub_categorys WHERE id = '$id2'";

                if ($conn->query($sql) === TRUE) {
        ?>
            <p class="msg success-msg">تم الحذف بنجاح</p>
<?php

                    header("refresh:2; url=sub_categories.php?id=" . $id);
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