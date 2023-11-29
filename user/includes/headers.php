<?php

session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/main.css">
    <link rel="stylesheet" href="./style/headers.css">
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />

    <!--=============== SWIPER CSS ===============-->
    <link rel="stylesheet" href="./style/swiper-bundle.min.css" />
    <link rel="stylesheet" href="./style/styles.css">
    <link rel="stylesheet" href="./style/cart.css">

    <style>
        .home-swiper {
            width: 100%;
            height: 500px;
        }
    </style>

</head>

<body dir="rtl">
    <div class="first-header">
        <div class="header-overflow"></div>
        <div class="nav-1-wrapper container">
            <span style="font-size: 18px; font-weight: 500;">مرحبا بكم جميعا 👋🙋</span>
            <div>
                <?php

                if (isset($_SESSION['id'])) {
                ?>
                    <a class="nav-1-link shadow" href="cart.php">ادارة السلة</a>
                    <a class="nav-1-link shadow" href="../logout.php">تسجيل الخروج</a>
                <?php
                } else {
                ?>
                    <a class="nav-1-link shadow" href="../login.php">تسجيل الدخول</a>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="nav-with-logo">
            <div class="nav-2-wrapper">
                <img src="../assets/logo.png" alt="" width="100">
                <h2 class="title">متجر العطور الفاخرة</h2>
            </div>

            <div class="nav-3-wrapper container">
                <a href="home-page.php" class="lg-text nav-links  navbar-link">الصفحة الرئيسية</a>
                <?php

                require '../connect.php';

                $sql = "SELECT main_categorys.* FROM main_categorys";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {

                    $id = $row['id'];
                ?>
                    <div class="sub_nav_container">
                        <a href="view_categories.php?box=main_categorie&&id=<?php echo $id; ?>" class="lg-text nav-links navbar-link"><?php echo $row['name']; ?></a>
                        <div class="sub_nav">
                            <?php

                            $sql2 = "SELECT sub_categorys.* FROM sub_categorys WHERE main_category_id = '$id'";
                            $result2 = $conn->query($sql2);

                            while ($row2 = $result2->fetch_assoc()) {

                                $id2 = $row2['id'];
                            ?>
                                <a href="view_categories.php?box=sub_categorie&&id=<?php echo $id2; ?>" class="sub_nav_link"><?php echo $row2['name']; ?></a>
                            <?php

                            }
                            ?>
                        </div>
                    </div>
                <?php

                }
                $conn->close();

                ?>
            </div>
        </div>
    </div>

    <script>
        // Get the current URL
        const currentUrl = window.location.href;
        console.log(currentUrl[1])

        // Get all the link elements
        const links = document.querySelectorAll(".navbar-link");

        // Check if the link's href matches the current URL
        links.forEach(function(link) {
            if (currentUrl == link.href) {
                link.classList.add("active");
            }
        });
    </script>