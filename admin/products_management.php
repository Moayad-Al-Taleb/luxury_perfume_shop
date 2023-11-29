<?php
$page_title = "ادارة المنتجات";

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
                        <h3>عرض جميع المنتجات المتاحة</h3>
                        <a class="primary-btn" href="products_management.php?box=insert&&id=<?php echo $id; ?>">اضافة جديد</a>
                    </div>

                    <?php

                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE sub_category_id = '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {

                    ?>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>

                                        <th>الحالة</th>
                                        <th>الخيارات</th>
                                    </tr>

                                </thead>
                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    $image_url = "uploads/" . $row['image_url'];

                                    if ($row['status'] == 0) {

                                ?>

                                        <tr>
                                            <td>#</td>
                                            <td>
                                                <div class="name-with-img">
                                                    <img src="<?php echo $image_url; ?>" alt="" width="80px" />
                                                    <span>
                                                        <?php echo $row['name']; ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                فعال
                                            </td>
                                            <td>
                                                <div class="btns-controls-container">
                                                    <a class="primary-btn" href="manage_product_details.php?id=<?php echo $row['id']; ?>">عرض</a>
                                                    <a class="danger-btn" href="products_management.php?box=deactivate&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">الغاء التفعيل</a>
                                                </div>
                                            </td>
                                        </tr>

                                    <?php

                                    } elseif ($row['status'] == 1) {


                                    ?>
                                        <tr>
                                            <td>#</td>
                                            <td>
                                                <div class="name-with-img">
                                                    <img src="<?php echo $image_url; ?>" alt="" width="80px" />
                                                    <span>
                                                        <?php echo $row['name']; ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                غير فعال
                                            </td>
                                            <td>
                                                <div class="btns-controls-container">
                                                    <a class="primary-btn" href="manage_product_details.php?id=<?php echo $row['id']; ?>">عرض</a>
                                                    <a class="success-btn" href="products_management.php?box=activation&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">تفعيل</a>
                                                </div>
                                            </td>
                                        </tr>

                                <?php

                                    }
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
                } else {
                    echo "Invalid entry.";
                }
            } elseif ($box == "insert") {
                $id = intval($_GET['id']);
                $sub_category_id = $id;

                function filter($variable)
                {
                    $variable = trim($variable);
                    $variable = htmlspecialchars($variable);
                    return $variable;
                }

                $name = $details = $code = $scent = $size = $expiration_date = $milliliter = $price = $image_url = "";
                $name_error = $details_error = $code_error = $scent_error = $size_error = $expiration_date_error = $error = $image_url_error = "";

                if (isset($_POST['btn_send'])) {
                    if (empty($_POST['name'])) {
                        $name_error = "* ";
                    } else {
                        $name = filter($_POST['name']);
                    }

                    if (empty($_POST['details'])) {
                        $details_error = "* ";
                    } else {
                        $details = filter($_POST['details']);
                    }

                    if (empty($_POST['code'])) {
                        $code_error = "* ";
                    } else {
                        $code = filter($_POST['code']);
                    }

                    if (empty($_POST['scent'])) {
                        $scent_error = "* ";
                    } else {
                        $scent = filter($_POST['scent']);
                    }

                    if (empty($_POST['size'])) {
                        $size_error = "* ";
                    } else {
                        $size = filter($_POST['size']);
                    }

                    if (empty($_POST['expiration_date'])) {
                        $expiration_date_error = "* ";
                    } else {
                        $expiration_date = filter($_POST['expiration_date']);
                    }

                    if (empty($_POST['milliliter']) && empty($_POST['price'])) {
                        $error = "* ";
                    } else {
                        if (!empty($_POST['milliliter']) && !empty($_POST['price'])) {
                            $error = "عذرا، غير مسموح باعطاء سعر العطر كاملا وسعر الميلي ليتر يرجى اخيتيار واحد فقط";
                        } else {
                            if (!empty($_POST['milliliter']) && empty($_POST['price'])) {
                                $milliliter = filter($_POST['milliliter']);
                            }
                            if (!empty($_POST['price']) && empty($_POST['milliliter'])) {
                                $price = filter($_POST['price']);
                            }
                        }
                    }

                    if (empty($_FILES['image_url']['name'])) {
                        $image_url_error = "* ";
                    } else {
                        $targetDir = "uploads/";
                        $fileName = basename($_FILES["image_url"]["name"]);
                        $targetFilePath = $targetDir . $fileName;
                        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
                        if (in_array($fileType, $allowTypes)) {
                            if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFilePath)) {
                                $image_url = $fileName;
                            } else {
                                $image_url_error = "عذرا، هناك خطأفي تحميل الصورة الرجاء اعادة الرفع";
                            }
                        } else {
                            $image_url_error = "عذرا، فقط هذه اللاحقات المسموحة JPG, JPEG, PNG, GIF, & PDF";
                        }
                    }

                    if (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && !empty($milliliter) && empty($price) && !empty($image_url) && !empty($sub_category_id)) {
                        require '../connect.php';

                        $sql = "SELECT * FROM products WHERE name = '$name'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
                ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "INSERT INTO products (name, details, code, scent, size, expiration_date, milliliter, image_url, sub_category_id) VALUES ('$name', '$details', '$code', '$scent', '$size', '$expiration_date', '$milliliter', '$image_url', '$sub_category_id')";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
                    <?php

                                header("refresh:2; url=products_management.php?id=" . $id);
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    } elseif (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && empty($milliliter) && !empty($price) && !empty($image_url) && !empty($sub_category_id)) {
                        require '../connect.php';

                        $sql = "SELECT * FROM products WHERE name = '$name'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
                    ?>
                    <p class="msg error-msg">
                        هذا الاسم موجود بالفعل
                    </p>
                    <?php
                        } else {
                            $sql = "INSERT INTO products (name, details, code, scent, size, expiration_date, price, image_url, sub_category_id) VALUES ('$name', '$details', '$code', '$scent', '$size', '$expiration_date', '$price', '$image_url', '$sub_category_id')";

                            if ($conn->query($sql) === TRUE) {
                    ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
        <?php

                                header("refresh:2; url=products_management.php?id=" . $id);
                            } else {
                                echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        $conn->close();
                    }
                }

        ?>

        <div>
            <div class="content-header">
                <div>
                    <h3>اضافة منتج جديد</h3>
                    <span style="font-size: 15px; color: #df3636;">سعر العطر يحدد اما بادخال سعر العطر كاملا أو سعر الميلي ليتر</span>
                </div>
            </div>

            <form class="form-container full-width-form" action="<?php echo $_SERVER['PHP_SELF'] . '?box=insert&&id=' . $id ?>" method="post" enctype="multipart/form-data">
                <div class="inputs-container">
                    <div class="input-field flexed-input">
                        <div>
                            <label for="name">الاسم: </label>
                            <?php echo $name_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="name" id="name">
                    </div>

                    <div class="input-field flexed-input">
                        <div>
                            <label for="details">التفاصيل: </label> <?php echo $details_error; ?>
                        </div>
                        <textarea class="input-text shadow" name="details" id="details" cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="inputs-container">
                    <div class="input-field flexed-input">
                        <div>
                            <label for="code">الرمز: </label>
                            <?php echo $code_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="code" id="code">
                    </div>

                    <div class="input-field flexed-input">
                        <div>
                            <label for="scent">وصف الرائحة: </label> <?php echo $scent_error; ?>
                        </div>
                        <textarea class="input-text shadow" name="scent" id="scent" cols="30" rows="10"></textarea>

                    </div>
                </div>
                <div class="inputs-container">
                    <div class="input-field flexed-input">
                        <div>
                            <label for="size">الحجم: </label>
                            <?php echo $size_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="size" id="size">
                    </div>

                    <div class="input-field flexed-input">
                        <div>
                            <label for="expiration_date">صلاحية الانتهاء: </label> <?php echo $expiration_date_error; ?>
                        </div>
                        <textarea class="input-text shadow" name="expiration_date" id="expiration_date" cols="30" rows="10"></textarea>
                    </div>
                </div>

                <div class="inputs-container">
                    <div class="input-field flexed-input">
                        <div>
                            <label for="milliliter">السعر في الميلي ليتر: </label>
                            <?php echo $error; ?>
                        </div>
                        <input class="input-text shadow" type="number" name="milliliter" id="milliliter">
                    </div>

                    <div class="input-field flexed-input">
                        <div>
                            <label for="price">السعر: </label>
                            <?php echo $error ?>
                        </div>
                        <input class="input-text shadow" type="number" name="price" id="price">
                    </div>
                </div>

                <div class="input-field flexed-input">
                    <div>
                        <label for="image_url">ارفاق صورة المنتج: </label>
                        <?php echo $image_url_error; ?>
                    </div>
                    <input class="input-text shadow" type="file" name="image_url" id="image_url">
                </div>

                <input class="success-btn shadow" style="width: 150px; margin-top: 5px;" type="submit" value="Send" name="btn_send">
            </form>
        </div>

        <?php
            } elseif ($box == "deactivate") {
                $id = intval($_GET['id']);
                $id2 = intval($_GET['id2']);

                require '../connect.php';

                $sql = "UPDATE products SET status = 1 WHERE id = '$id2'";

                if ($conn->query($sql) === TRUE) {
        ?>
            <p class="msg success-msg">
                تم تعديل الحالة بنجاح
            </p>
        <?php

                    header("refresh:2; url=products_management.php?id=" . $id);
                } else {
                    echo "Error updating record: " . $conn->error;
                }

                $conn->close();
            } elseif ($box == "activation") {
                $id = intval($_GET['id']);
                $id2 = intval($_GET['id2']);

                require '../connect.php';

                $sql = "UPDATE products SET status = 0 WHERE id = '$id2'";

                if ($conn->query($sql) === TRUE) {
        ?>
            <p class="msg success-msg">
                تم تعديل الحالة بنجاح
            </p>
<?php

                    header("refresh:2; url=products_management.php?id=" . $id);
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
    include "includes/footer.php" ?>