<?php

ob_start();

include "./includes/headers.php";

$box = (isset($_GET['box'])) ? $_GET['box'] : "index";

if ($box == "index") {

    $id = (isset($_GET['id'])) ? intval($_GET['id']) : null;
    require '../connect.php';

    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $conn->query($sql);
?>

    <div class="title-header">
        <h3>بيانات المنتج</h3>
    </div>
    <?php

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_url = "../admin/uploads/" . $row['image_url'];
    ?>
        <div class="product_info-container container">
            <div class="gallery">
                <!-- =============== SWIPER GALLERY CARDS =============== -->
                <div class="swiper gallery-cards">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <article class="gallery__card">
                                <img src="<?php echo $image_url ?>" alt="image gallery" class="gallery__img" />
                            </article>
                        </div>
                        <?php
                        require '../connect.php';

                        $sql2 = "SELECT * FROM images WHERE product_id = '$id'";
                        $result2 = $conn->query($sql2);

                        if ($result2->num_rows > 0) {
                            while ($row2 = $result2->fetch_assoc()) {
                                $image_url2 = "../admin/uploads/" . $row2['image_url'];
                        ?>
                                <div class="swiper-slide">
                                    <article class="gallery__card">
                                        <img src="<?php echo $image_url2 ?>" alt="image gallery" class="gallery__img" />
                                    </article>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <!-- =============== SWIPER GALLERY THUMBNAIL =============== -->
                <div class="gallery__overflow">
                    <div class="swiper gallery-thumbs">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="gallery__thumbnail">
                                    <img src="<?php echo $image_url ?>" alt="image thumbnail" class="gallery__thumbnail-img" />
                                </div>
                            </div>
                            <?php
                            require '../connect.php';

                            $sql3 = "SELECT * FROM images WHERE product_id = '$id'";
                            $result3 = $conn->query($sql3);
                            if ($result3->num_rows > 0) {
                                while ($row3 = $result3->fetch_assoc()) {
                                    $image_url3 = "../admin/uploads/" . $row3['image_url'];
                            ?>
                                    <div class="swiper-slide">
                                        <div class="gallery__thumbnail">
                                            <img src="<?php echo $image_url3 ?>" alt="image thumbnail" class="gallery__thumbnail-img" />
                                        </div>
                                    </div>
                            <?php

                                }
                            }
                            ?>
                        </div>
                        <!-- Swiper pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
            <div class="product-content" style="margin: 32px 16px;">
                <ul class="product-content-list">
                    <li><span>الاسم:</span> <?php echo $row['name']; ?></li>
                    <li><span>التفاصيل:</span> <?php echo $row['details']; ?></li>
                    <li><span>الرمز:</span> <?php echo $row['code']; ?></li>
                    <li><span>وصف الرائحة:</span> <?php echo $row['scent']; ?></li>
                    <li><span>الحجم:</span> <?php echo $row['size']; ?></li>
                    <li><span>الصلاحية:</span> <?php echo $row['expiration_date']; ?></li>

                    <?php
                    echo (!empty($row['milliliter'])) ? "<li><span>السعر في الميلي ليتر: </span>" . $row['milliliter'] . "</li>" : "<li><span>السعر: </span> " . $row['price'] . "</li>";
                    ?>

                </ul>
            </div>
        </div>
        <div class="title-header">
            <h3>الروائح</h3>
        </div>
        <div class="product-imgs-gallery">
            <?php
            require '../connect.php';

            $sql = "SELECT * FROM formulations WHERE product_id = '$id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $image_url = "../admin/uploads/" . $row['image_url'];

            ?>

                    <div class="product-img-gallery">
                        <img src="<?php echo $image_url; ?>" alt="" style="height: 60%; width: 60%;" />
                        <span><?php echo $row['formulation'] ?></span>
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

        <div class="title-header">
            <h3>اراء المستخدمين</h3>
        </div>

        <div class="container">
            <?php
            $id = intval($_GET['id']);
            $product_id = $id;

            $customer_id = (isset($_SESSION['id'])) ? $_SESSION['id'] : null;


            function filter($variable)
            {
                $variable = trim($variable);
                $variable = htmlspecialchars($variable);
                return $variable;
            }

            $review = "";
            $review_error = "";

            if (isset($_POST['btn_send'])) {
                if (empty($_POST['review'])) {
                    $review_error = "* ";
                } else {
                    $review = filter($_POST['review']);
                }

                if (!empty($review) && !empty($product_id) && !empty($customer_id)) {
                    require '../connect.php';

                    $sql = "INSERT INTO reviews (review, product_id, customer_id) VALUES ('$review', '$product_id', '$customer_id')";

                    if ($conn->query($sql) === TRUE) {
            ?>

                        <p class="msg">
                            تم اضافة الرأي بنجاح
                        </p>

            <?php

                        header("refresh:2; url=product_page.php?id=" . $id);
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                    $conn->close();
                }
            }
            ?>
            <?php
            if ($customer_id != null) {
            ?>
                <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id ?>" method="post" class="feedback-form">
                    <div class="feedback-input-field">
                        <label for="">اضافة رأي</label>
                        <textarea name="review" type="text" class="text-input"></textarea>
                    </div>
                    <input name="btn_send" type="submit" value="اضافة" class="main-btn">
                </form>
            <?php
            }
            ?>
        </div>

        <div class="testmonials-container container">
            <?php
            require '../connect.php';

            $sql = "SELECT * FROM reviews WHERE product_id = '$id' ORDER BY status ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    if ($row['status'] == 1) {
            ?>
                        <div class="testmonial-content">
                            <p><?php echo $row['review']; ?></p>
                            <?php
                            if (isset($_SESSION['id']) && $row['customer_id'] == $_SESSION['id']) {
                            ?>
                                <a href="product_page.php?box=delete&&id=<?php echo $id; ?>&&id2=<?php echo $row['id']; ?>">حذف الرأي</a>
                            <?php
                            }
                            ?>
                        </div>
                    <?php
                    } elseif ($row['status'] == 1) {
                    ?>
                        <div class="testmonial-content">
                            <p><?php echo $row['review']; ?></p>
                        </div>
            <?php

                    }
                }
            } else {
            }
            $conn->close();
            ?>
        </div>

    <?php
    }
} elseif ($box == "delete") {
    $id = intval($_GET['id']);
    $id2 = intval($_GET['id2']);

    require '../connect.php';

    $sql = "DELETE FROM reviews WHERE id = '$id2'";

    if ($conn->query($sql) === TRUE) {
    ?>
        <div class="container" style="height: 200px;">
            <p class="msg">
                تم الحذف بنجاح
            </p>
        </div>
<?php
        header("refresh:2; url=product_page.php?id=" . $id);
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}
?>
<?php include "./includes/footer.php";
ob_end_flush();
?>