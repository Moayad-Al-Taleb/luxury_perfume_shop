<?php include "./includes/headers.php"; ?>

<!-- رح يكون عندي واجهتين 
واجهة العطور العامة رح يطالعلي مجموعة منوعة من العطور 1
2 وقت اكبس ع فئة معينة من العطور رح ياخدني ع واجهة الخاصة ب عرض عطور لفئة معينة عبر Id -->
<?php

$box = (isset($_GET['box'])) ? $_GET['box'] : "main_categorie";

if ($box == "main_categorie") {
    $id = intval($_GET['id']);

    require '../connect.php';
    $sql = "SELECT * FROM main_categorys WHERE id= '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();

?>

    <div class="container">
        <div class="title-header">
            <h3><?php echo $row['name'] ?></h3>
        </div>
        <div class="eles-container">
            <!--  -->
            <?php

            require '../connect.php';

            $sql = "SELECT products.id, products.name, products.image_url, sub_categorys.name AS 'category' FROM products, sub_categorys WHERE products.status = '0' AND products.sub_category_id = sub_categorys.id AND products.sub_category_id IN ( SELECT sub_categorys.id FROM sub_categorys WHERE sub_categorys.main_category_id = '$id' )";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $image_url = "../admin/uploads/" . $row['image_url'];
            ?>
                    <div class="ele">
                        <div class="type">
                            <?php echo $row['category']; ?>
                        </div>
                        <img src="<?php echo $image_url; ?>" alt="">
                        <span><?php echo $row['name']; ?></span>
                        <div class="ele-btns">
                            <a href="cart.php?box=add_to_cart&&id=<?php echo $row['id']; ?>">اضافة الى السلة</a>
                            <a href="product_page.php?id=<?php echo $row['id']; ?>">عرض</a>
                        </div>
                    </div>

                <?php
                }
            } else {
                ?>
                <p class="msg">
                    نعتذر ولكن لايوجد منتجات بعد لعرضها!
                </p>
            <?php
            }
            $conn->close();
            ?>
        </div>
    </div>
<?php
} elseif ($box == "sub_categorie") {
    $id = intval($_GET['id']);

    require '../connect.php';
    $sql = "SELECT * FROM sub_categorys WHERE id= '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $conn->close();

?>
    <div class="container">
        <div class="title-header">
            <h3><?php echo $row['name'] ?></h3>
        </div>
        <div class="eles-container">
            <?php

            require '../connect.php';

            $sql = "SELECT products.id, products.name, products.image_url, sub_categorys.name AS 'category' FROM products, sub_categorys WHERE products.status = '0' AND products.sub_category_id = '$id' AND products.sub_category_id = sub_categorys.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $image_url = "../admin/uploads/" . $row['image_url'];
            ?>
                    <div class="ele ">
                        <img src="<?php echo $image_url; ?>" alt="">
                        <span><?php echo $row['name']; ?></span>
                        <div class="ele-btns">
                            <a href="cart.php?box=add_to_cart&&id=<?php echo $row['id']; ?>">اضافة الى السلة</a>
                            <a href="product_page.php?id=<?php echo $row['id']; ?>">عرض</a>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="container">
                    <p class="msg">
                        نعتذر ولكن لايوجد منتجات بعد لعرضها!
                    </p>
                </div>
            <?php
            }
            $conn->close();
            ?>
        </div>
    </div>
<?php
}

include "./includes/footer.php"; ?>