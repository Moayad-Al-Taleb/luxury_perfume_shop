<?php
$page_title = "ادارة المستخدمين";
include "includes/header.php";

session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {

        $box = (isset($_GET['box'])) ? $_GET['box'] : "index";

        if ($box == "index") {
            require '../connect.php';

            $sql = "SELECT * FROM customers WHERE type = 1 ORDER BY status ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

?>
                <div>
                    <h3>عرض جميع المستخدمين</h3>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم الاول</th>
                                    <th>الاسم الاخير</th>
                                    <th>اسم المستخدم</th>
                                    <th>رقم الهاتف</th>
                                    <th>حالة الحساب</th>
                                    <th>الخيارات</th>
                                </tr>
                            </thead>

                            <?php
                            while ($row = $result->fetch_assoc()) {
                                if ($row['status'] == 0) {
                            ?>
                                    <tr>
                                        <td>#</td>
                                        <td><?php echo $row['first_name']; ?></td>
                                        <td><?php echo $row['last_name']; ?></td>
                                        <td><?php echo $row['user_name']; ?></td>
                                        <td><?php echo $row['phone']; ?></td>
                                        <td><span>فعال</span></td>
                                        <td>
                                            <div class="btns-controls-container">
                                                <a class="danger-btn" href="?box=deactivation&&id=<?php echo $row['id']; ?>">الغاء التفعيل</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php

                                } elseif ($row['status'] == 1) {

                                ?>
                                    <tr>
                                        <td>#</td>
                                        <td><?php echo $row['first_name']; ?></td>
                                        <td><?php echo $row['last_name']; ?></td>
                                        <td><?php echo $row['user_name']; ?></td>
                                        <td><?php echo $row['phone']; ?></td>

                                        <td><span>غير فعال</span></td>

                                        <td>
                                            <div class="btns-controls-container">
                                                <a class="success-btn" href="?box=activation&&id=<?php echo $row['id']; ?>">تفعيل</a>
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
        } elseif ($box == "deactivation") {
            $id = intval($_GET['id']);

            require '../connect.php';

            $sql = "UPDATE customers SET status = 1 WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
            ?>

                <p class="msg success-msg">
                    تم تعديل الحالة بنجاح
                </p>

            <?php


                header("refresh:2; url=users_management.php");
            } else {
                echo "Error updating record: " . $conn->error;
            }

            $conn->close();
        } elseif ($box == "activation") {
            $id = intval($_GET['id']);

            require '../connect.php';

            $sql = "UPDATE customers SET status = 0 WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
            ?>

                <p class="msg success-msg">
                    تم تعديل الحالة بنجاح
                </p>

<?php


                header("refresh:2; url=users_management.php");
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
?>
<?php include "includes/footer.php" ?>