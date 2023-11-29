<?php

$page_title = "ادارة بيانات المنتج";

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
                    <!-- 1 -->
                    <div class="content-header">
                        <div>
                            <h3>بيانات المنتج الرئيسية</h3>
                            <span style="font-size: 14px; color: #555;">هنا يتم عرض جميع بيانات المنتج</span>
                        </div>
                        <div class="btns-controls-container">
                            <a class="warning-btn shadow" href="manage_product_details.php?box=product_update&&id=<?php echo $id; ?>">تعديل البيانات</a>
                            <a class="danger-btn shadow" href="manage_product_details.php?box=product_delete&&id=<?php echo $id; ?>">حذف المنتج</a>
                        </div>
                    </div>

                    <?php
                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE id = '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        $image_url = "uploads/" . $row['image_url'];
                    ?>

                        <div class="product-container">
                            <div class="product-img">
                                <img class="product-img-ele " src="<?php echo $image_url; ?>" alt="" width="180px" />
                            </div>
                            <div class="product-content">
                                <ul class="product-content-list">
                                    <li><span>الاسم:</span> <span><?php echo $row['name']; ?></span></li>
                                    <li><span>التفاصيل:</span> <span><?php echo $row['details']; ?></span></li>
                                    <li><span>الرمز:</span> <span><?php echo $row['code']; ?></span></li>
                                    <li><span>وصف الرائحة:</span> <span><?php echo $row['scent']; ?></span></li>
                                    <li><span>الحجم:</span> <span><?php echo $row['size']; ?></span></li>
                                    <li><span>الصلاحية:</span> <span><?php echo $row['expiration_date']; ?></span></li>

                                    <?php
                                    echo (!empty($row['milliliter'])) ? "<li><span>السعر في الميلي ليتر: </span>" . $row['milliliter'] . "</li>" : "<li><span>السعر: </span> " . $row['price'] . "</li>";
                                    ?>

                                    <li><span>حالة المنتج:</span> <?php echo ($row['status'] == 0) ? "ظاهر للزبائن" : "مخفي عن الزبائن"; ?></li>
                                </ul>
                            </div>
                        </div>

                    <?php
                    } else {
                    ?>
                        <p class="msg warning-msg" style="width: 100%;">
                            لايوجد بيانات لعرضها
                        </p>
                    <?php
                    }
                    $conn->close();
                    ?>

                    <!-- 2 -->

                    <div class="content-header">
                        <div>
                            <h3>صور المنتج</h3>
                            <span style="font-size: 14px; color: #555;">هنا يتم عرض جميع صور المنتج</span>
                        </div>
                        <a class="primary-btn" href="manage_product_details.php?box=image_add&&id=<?php echo $id; ?>">اضافة صورة جديدة</a>
                    </div>
                    <div class="product-imgs-gallery">

                        <?php
                        require '../connect.php';

                        $sql = "SELECT * FROM images WHERE product_id = '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $image_url = "uploads/" . $row['image_url'];
                        ?>

                                <div class="product-img-gallery">
                                    <div class="overflow"></div>
                                    <img src="<?php echo $image_url; ?>" alt="" width="100px" height="100px" />
                                    <a class="danger-btn" href="manage_product_details.php?box=image_delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف </a>
                                </div>

                            <?php

                            }
                        } else {
                            ?>
                            <p class="msg warning-msg" style="width: 100%;">
                                لايوجد بيانات لعرضها
                            </p>
                        <?php
                        }
                        $conn->close();
                        ?>
                    </div>

                    <!-- 3 -->
                    <div class="content-header">

                        <div>
                            <h3>ادارة العطور</h3>
                            <span style="font-size: 14px; color: #555;">هنا يتم عرض جميع العطور الداخلة في انشاء العطر</span>
                        </div>
                        <a class="primary-btn" href="manage_product_details.php?box=formulation_add&&id=<?php echo $id; ?>">اضافة رائحة</a>
                    </div>

                    <div class="product-imgs-gallery">
                        <?php
                        require '../connect.php';

                        $sql = "SELECT * FROM formulations WHERE product_id = '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {

                            while ($row = $result->fetch_assoc()) {
                                $image_url = "uploads/" . $row['image_url'];

                        ?>

                                <div class="product-img-gallery">
                                    <div class="overflow"></div>
                                    <img src="<?php echo $image_url; ?>" alt="" style="height: 60%; width: 60%;" />
                                    <span><?php echo $row['formulation'] ?></span>
                                    <a class="danger-btn" style="bottom: 50%; transform: translate(50%, 50%);" href="manage_product_details.php?box=formulation_delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف</a>
                                </div>

                            <?php
                            }
                            ?>

                        <?php

                        } else {
                        ?>
                            <p class="msg warning-msg" style="width: 100%;">
                                لايوجد بيانات لعرضها
                            </p>
                        <?php
                        }
                        $conn->close();
                        ?>
                    </div>

                    <div class="content-header">
                        <div>
                            <h3>آراء المستخدمين حول المنتج </h3>
                            <span style="font-size: 14px; color: #555;">هنا يتم عرض جميع آراء المستخدمين حول المنتج</span>
                        </div>
                    </div>
                    <!-- 4 -->

                    <?php
                    require '../connect.php';

                    $sql = "SELECT * FROM reviews ORDER BY status ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {

                    ?>
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الرأي</th>
                                        <th>حالة الرأي</th>
                                        <th>الخيارات</th>
                                    </tr>
                                </thead>

                                <?php
                                while ($row = $result->fetch_assoc()) {
                                    if ($row['status'] == 0) {

                                ?>
                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['review']; ?></td>
                                            <td>غير فعال</td>
                                            <td>
                                                <div class="btns-controls-container">
                                                    <a class="success-btn" href="manage_product_details.php?box=show&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">عرض الرأي</a>
                                                    <a class="danger-btn" href="manage_product_details.php?box=delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php

                                    } elseif ($row['status'] == 1) {

                                    ?>
                                        <tr>
                                            <td>#</td>
                                            <td><?php echo $row['review']; ?></td>
                                            <td>فعال</td>
                                            <td>
                                                <div class="btns-controls-container">
                                                    <a class="warning-btn" href="manage_product_details.php?box=block&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">اخفاء الرأي</a>
                                                    <a class="danger-btn" href="manage_product_details.php?box=delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف</a>
                                                </div>
                                            </td>
                                        </tr>
                                <?php

                                    }
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
                </div>
                <?php
            } else {
                echo "Invalid entry.";
            }
        } elseif ($box == "product_update") {

            $id = intval($_GET['id']);

            require '../connect.php';

            $sql = "SELECT * FROM products WHERE id = '$id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

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

                if (!empty($_FILES['image_url']['name'])) {
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

                if (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && !empty($milliliter) && empty($price) && !empty($image_url)) {
                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE name = '$name' AND id != '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 1) {
                ?>
                        <p class="msg error-msg">
                            هذا الاسم موجود بالفعل
                        </p>
                        <?php
                    } else {
                        $sql = "UPDATE products SET name = '$name', details = '$details', code = '$code', scent = '$scent', size = '$size', expiration_date = '$expiration_date', milliliter = '$milliliter', price = null, image_url = '$image_url' WHERE id = '$id'";

                        if ($conn->query($sql) === TRUE) {
                        ?>
                            <p class="msg success-msg">
                                تم التعديل بنجاح
                            </p>
                            <?php

                            if (file_exists('uploads/' . $row['image_url'])) {
                                unlink('uploads/' . $row['image_url']);
                            }

                            header("refresh:2; url=manage_product_details.php?id=" . $id);
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    }

                    $conn->close();
                } elseif (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && empty($milliliter) && !empty($price) && !empty($image_url)) {
                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE name = '$name' AND id != '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 1) {
                        echo "name reserved.";
                    } else {
                        $sql = "UPDATE products SET name = '$name', details = '$details', code = '$code', scent = '$scent', size = '$size', expiration_date = '$expiration_date', milliliter = null, price = '$price', image_url = '$image_url' WHERE id = '$id'";

                        if ($conn->query($sql) === TRUE) {
                            ?>
                            <p class="msg success-msg">
                                تم التعديل بنجاح
                            </p>
                        <?php

                            if (file_exists('uploads/' . $row['image_url'])) {
                                unlink('uploads/' . $row['image_url']);
                            }

                            header("refresh:2; url=manage_product_details.php?id=" . $id);
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    }

                    $conn->close();
                } elseif (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && !empty($milliliter) && empty($price)) {
                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE name = '$name' AND id != '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 1) {
                        ?>
                        <p class="msg success-msg">
                            هذا الاسم موجود بالفعل
                        </p>
                        <?php
                    } else {
                        $sql = "UPDATE products SET name = '$name', details = '$details', code = '$code', scent = '$scent', size = '$size', expiration_date = '$expiration_date', milliliter = '$milliliter', price = null WHERE id = '$id'";

                        if ($conn->query($sql) === TRUE) {
                        ?>
                            <p class="msg success-msg">
                                تم التعديل بنجاح
                            </p>
                        <?php

                            header("refresh:2; url=manage_product_details.php?id=" . $id);
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    }

                    $conn->close();
                } elseif (!empty($name) && !empty($details) && !empty($code) && !empty($scent) && !empty($size) && !empty($expiration_date) && empty($milliliter) && !empty($price)) {
                    require '../connect.php';

                    $sql = "SELECT * FROM products WHERE name = '$name' AND id != '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows == 1) {
                        ?>
                        <p class="msg error-msg">
                            هذا الاسم موجود بالفعل
                        </p>
                        <?php
                    } else {
                        $sql = "UPDATE products SET name = '$name', details = '$details', code = '$code', scent = '$scent', size = '$size', expiration_date = '$expiration_date', milliliter = null, price = '$price' WHERE id = '$id'";

                        if ($conn->query($sql) === TRUE) {
                        ?>
                            <p class="msg success-msg">
                                تم التعديل بنجاح
                            </p>
            <?php
                            header("refresh:2; url=manage_product_details.php?id=" . $id);
                        } else {
                            echo "Error updating record: " . $conn->error;
                        }
                    }

                    $conn->close();
                }
            }

            ?>
            <div>
                <div class="content-header">
                    <div>
                        <h3>تعديل بيانات المنتج</h3>
                        <span style="font-size: 15px; color: #df3636;">اذا كنت لاتريد تغيير صورة المنتج لاتقم بارفاق صورة جديدة </span> <br>
                        <span style="font-size: 15px; color: #df3636;">سعر العطر يحدد اما بادخال سعر العطر كاملا أو سعر الميلي ليتر</span>
                    </div>
                </div>

                <form class="form-container full-width-form" action="<?php echo $_SERVER['PHP_SELF'] . '?box=product_update&&id=' . $id ?>" method="post" enctype="multipart/form-data">
                    <div class="inputs-container">
                        <div class="input-field flexed-input">
                            <div>
                                <label for="name">الاسم: </label>
                                <?php echo $name_error; ?>
                            </div>
                            <input class="input-text shadow" type="text" name="name" id="name" value="<?php echo $row['name']; ?>">
                        </div>

                        <div class="input-field flexed-input">
                            <div>
                                <label for="details">التفاصيل: </label> <?php echo $details_error; ?>
                            </div>
                            <textarea class="input-text shadow" name="details" id="details" cols="30" rows="10"><?php echo $row['details']; ?></textarea>
                        </div>
                    </div>
                    <div class="inputs-container">
                        <div class="input-field flexed-input">
                            <div>
                                <label for="code">الرمز: </label>
                                <?php echo $code_error; ?>
                            </div>
                            <input class="input-text shadow" type="text" name="code" id="code" value="<?php echo $row['code']; ?>">
                        </div>

                        <div class="input-field flexed-input">
                            <div>
                                <label for="scent">وصف الرائحة: </label> <?php echo $scent_error; ?>
                            </div>
                            <textarea class="input-text shadow" name="scent" id="scent" cols="30" rows="10"><?php echo $row['scent']; ?></textarea>

                        </div>
                    </div>
                    <div class="inputs-container">
                        <div class="input-field flexed-input">
                            <div>
                                <label for="size">الحجم: </label>
                                <?php echo $size_error; ?>
                            </div>
                            <input class="input-text shadow" type="text" name="size" id="size" value="<?php echo $row['size']; ?>">
                        </div>

                        <div class="input-field flexed-input">
                            <div>
                                <label for="expiration_date">صلاحية الانتهاء: </label> <?php echo $expiration_date_error; ?>
                            </div>
                            <textarea class="input-text shadow" name="expiration_date" id="expiration_date" cols="30" rows="10"><?php echo $row['expiration_date']; ?></textarea>
                        </div>
                    </div>

                    <div class="inputs-container">
                        <div class="input-field flexed-input">
                            <div>
                                <label for="milliliter">السعر في الميلي ليتر: </label>
                                <?php echo $error; ?>
                            </div>
                            <input class="input-text shadow" type="number" name="milliliter" id="milliliter" value="<?php echo $row['milliliter']; ?>">
                        </div>

                        <div class="input-field flexed-input">
                            <div>
                                <label for="price">السعر: </label>
                                <?php echo $error ?>
                            </div>
                            <input class="input-text shadow" type="number" name="price" id="price" value="<?php echo $row['price']; ?>">
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

        } elseif ($box == "product_delete") {

            $id = intval($_GET['id']);

            require '../connect.php';

            $sql = "SELECT * FROM products WHERE id = '$id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            $sql = "DELETE FROM products WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
            ?>
                <p class="msg success-msg">
                    تم الحذف بنجاح
                </p>
            <?php

                if (file_exists('uploads/' . $row['image_url'])) {
                    unlink('uploads/' . $row['image_url']);
                }

                header("refresh:2; url=main_categories.php");
            } else {
                echo "Error deleting record: " . $conn->error;
            }

            $conn->close();
            ?>
            <?php
        } elseif ($box == "image_add") {
            $id = intval($_GET['id']);
            $product_id = $id;

            $image_url = "";
            $image_url_error = "";

            if (isset($_POST['btn_send'])) {
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

                if (!empty($image_url) && !empty($product_id)) {
                    require '../connect.php';

                    $sql = "INSERT INTO images (image_url, product_id) VALUES ('$image_url', '$product_id')";

                    if ($conn->query($sql) === TRUE) {
            ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
            <?php

                        header("refresh:2; url=manage_product_details.php?id=" . $id);
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                    $conn->close();
                }
            }

            ?>

            <div>
                <h1>Add Image</h1>

                <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=image_add&&id=' . $id ?>" method="post" enctype="multipart/form-data">
                    <div class="input-field">
                        <div>
                            <label for="image_url">ارفاق الصورة: </label>
                            <?php echo $image_url_error; ?>
                        </div>
                        <input class="input-text shadow" type="file" name="image_url" id="image_url"> <br>
                    </div>
                    <input class="success-btn shadow" type="submit" value="اضافة" name="btn_send">
                </form>
            </div>

            <?php

        } elseif ($box == "image_delete") {
            $id = intval($_GET['id']);
            $id2 = intval($_GET['id2']);

            require '../connect.php';

            $sql = "SELECT * FROM images WHERE id = '$id2'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            $sql = "DELETE FROM images WHERE id = '$id2'";

            if ($conn->query($sql) === TRUE) {
            ?>
                <p class="msg success-msg">
                    تم الحذف بنجاح
                </p>
                <?php

                if (file_exists('uploads/' . $row['image_url'])) {
                    unlink('uploads/' . $row['image_url']);
                }

                header("refresh:2; url=manage_product_details.php?id=" . $id);
            } else {
                echo "Error deleting record: " . $conn->error;
            }

            $conn->close();
        } elseif ($box == "formulation_add") {

            $id = intval($_GET['id']);
            $product_id = $id;

            function filter($variable)
            {
                $variable = trim($variable);
                $variable = htmlspecialchars($variable);
                return $variable;
            }

            $formulation = $image_url = "";
            $formulation_error = $image_url_error = "";

            if (isset($_POST['btn_send'])) {
                if (empty($_POST['formulation'])) {
                    $formulation_error = "* ";
                } else {
                    $formulation = filter($_POST['formulation']);
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

                if (!empty($image_url) && !empty($formulation) && !empty($product_id)) {
                    require '../connect.php';

                    $sql = "INSERT INTO formulations (image_url, formulation, product_id) VALUES ('$image_url', '$formulation', '$product_id')";

                    if ($conn->query($sql) === TRUE) {
                ?>
                        <p class="msg success-msg">
                            تم الاضافة بنجاح
                        </p>
            <?php

                        header("refresh:2; url=manage_product_details.php?id=" . $id);
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                    $conn->close();
                }
            }


            ?>

            <div>
                <div class="content-header">
                    <h3>اضافة رائحة جديدة</h3>
                </div>

                <form class="form-container" action="<?php echo $_SERVER['PHP_SELF'] . '?box=formulation_add&&id=' . $id ?>" method="post" enctype="multipart/form-data">
                    <div class="input-field">
                        <div>
                            <label for="formulation">الرائحة : </label>
                            <?php echo $formulation_error; ?>
                        </div>
                        <input class="input-text shadow" type="text" name="formulation" id="formulation">
                    </div>

                    <div class="input-field">
                        <div>
                            <label for="image_url">ارفاق الصورة: </label>
                            <?php echo $image_url_error; ?>
                        </div>
                        <input class="input-text shadow" type="file" name="image_url" id="image_url"> <br>
                    </div>


                    <input class="success-btn" type="submit" value="اضافة" name="btn_send">
                </form>
            </div>

            <?php
        } elseif ($box == "formulation_delete") {

            $id = intval($_GET['id']);
            $id2 = intval($_GET['id2']);

            require '../connect.php';

            $sql = "SELECT * FROM formulations WHERE id = '$id2'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            $sql = "DELETE FROM formulations WHERE id = '$id2'";

            if ($conn->query($sql) === TRUE) {
            ?>
                <p class="msg success-msg">
                    تم الحذف بنجاح
                </p>
                <?php

                if (file_exists('uploads/' . $row['image_url'])) {
                    unlink('uploads/' . $row['image_url']);
                }

                header("refresh:2; url=manage_product_details.php?id=" . $id);
            } else {
                echo "Error deleting record: " . $conn->error;
            }

            $conn->close();
        } elseif ($box == "show") {
            $id = intval($_GET['id']);
            $id2 = intval($_GET['id2']);

            require '../connect.php';

            $sql = "UPDATE reviews SET status = 1 WHERE id = '$id2'";

            if ($conn->query($sql) === TRUE) {
                ?>
                <p class="msg success-msg">
                    تم تعديل الحالة بنجاح
                </p>
            <?php

                header("refresh:2; url=manage_product_details.php?id=" . $id);
            } else {
                echo "Error updating record: " . $conn->error;
            }

            $conn->close();
        } elseif ($box == "block") {
            $id = intval($_GET['id']);
            $id2 = intval($_GET['id2']);

            require '../connect.php';

            $sql = "UPDATE reviews SET status = 0 WHERE id = '$id2'";

            if ($conn->query($sql) === TRUE) {
            ?>
                <p class="msg success-msg">
                    تم تعديل الحالة بنجاح
                </p>
            <?php

                header("refresh:2; url=manage_product_details.php?id=" . $id);
            } else {
                echo "Error updating record: " . $conn->error;
            }

            $conn->close();
        } elseif ($box == "delete") {
            $id = intval($_GET['id']);
            $id2 = intval($_GET['id2']);

            require '../connect.php';

            $sql = "DELETE FROM reviews WHERE id = '$id2'";

            if ($conn->query($sql) === TRUE) {
            ?>
                <p class="msg success-msg">
                    تم الحذف بنجاح
                </p>
<?php

                header("refresh:2; url=manage_product_details.php?id=" . $id);
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