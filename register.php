<?php
function filter($variable)
{
    $variable = trim($variable);
    $variable = htmlspecialchars($variable);
    return $variable;
}
$err_msg = "";
$first_name = $last_name = $user_name  = $phone = $pass = "";
$first_name_error = $last_name_error = $user_name_error  = $phone_error = $pass_error = "";

if (isset($_POST['btn_send'])) {
    if (empty($_POST['first_name'])) {
        $first_name_error = "* ";
    } else {
        $first_name = filter($_POST['first_name']);
    }

    if (empty($_POST['last_name'])) {
        $last_name_error = "* ";
    } else {
        $last_name = filter($_POST['last_name']);
    }

    if (empty($_POST['user_name'])) {
        $user_name_error = "* ";
    } else {
        $user_name = filter($_POST['user_name']);
    }

    if (empty($_POST['phone'])) {
        $phone_error = "* ";
    } else {
        $phone = filter($_POST['phone']);
    }

    if (empty($_POST['pass'])) {
        $pass_error = "* ";
    } else {
        $pass = sha1(filter($_POST['pass']));
    }

    if (!empty($first_name) && !empty($last_name) && !empty($user_name) && !empty($phone) && !empty($pass)) {
        require 'connect.php';

        $sql = "SELECT * FROM customers WHERE user_name = '$user_name'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $err_msg = "هذا الاسم مأخوذ مسبقا";
        } else {
            $sql = "INSERT INTO customers (first_name, last_name, user_name, phone, pass) VALUES ('$first_name', '$last_name', '$user_name', '$phone', '$pass')";

            if ($conn->query($sql) === TRUE) {
                $err_msg = "تم انشاء الحساب يمكنك الان تسجيل الدخول";

                header("refresh:2; url=login.php");
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        $conn->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user/style/main.css">
    <link rel="stylesheet" href="register.css">
</head>

<body dir="rtl" class="login-page">
    <div class="over-flow-screen"></div>
    <div class="register-form">
        <h2 class="text-center title-text">مرحبا بك في عالم العطور الرائع 🙋👋</h2>
        <p style="font-size: 16px; font-weight: 400;">امتع حواسك وانطلق في رحلة عطرية ساحرة. من خلال إنشاء حساب، ستحصل على وصول إلى عالم من الروائح الفاخرة والتجارب المخصصة.</p>
        <p><?php echo $err_msg; ?></p>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="inputs-con">
                <div class="input-field">
                    <label class="label" for="first_name">الاسم</label>
                    <input dir="ltr" class="input-text" type="text" id="first_name" name="first_name" placeholder="ex: john">
                    <?php echo $first_name_error; ?>
                </div>
                <div class="input-field">
                    <label class="label" for="last_name">اسم العائلة</label>
                    <input dir="ltr" class="input-text type=" text" id="last_name" name="last_name" placeholder="ex: doe">
                    <?php echo $last_name_error; ?>
                </div>
            </div>
            <div class="inputs-con">
                <div class="input-field">
                    <label class="label" for="nick_name">اسم المستخدم</label>
                    <input dir="ltr" class="input-text" type="text" id="nick_name" name="user_name" placeholder="ex: john doe">
                    <?php echo $user_name_error; ?>
                </div>
            </div>
            <div class="inputs-con">
                <div class="input-field">
                    <label class="label" for="phone_number">رقم الهاتف</label>
                    <input dir="ltr" class="input-text" type="number" id="phone_number" name="phone" placeholder="ex: 0978811111">
                    <?php echo $phone_error; ?>
                </div>
                <div class="input-field">
                    <label class="label" for="password">كلمة المرور</label>
                    <div class="pass-con">
                        <input dir="ltr" class="input-pass" name="pass" type="password" id="password">
                        <i onclick="showPass()" id="eye-show" class="fa-solid fa-eye eye-icon "></i>
                        <i onclick="hidePass()" style="display: none;" id="eye-hide" class="fa-solid fa-eye-slash eye-icon "></i>
                    </div>
                    <?php echo $pass_error; ?>
                </div>
            </div>
            <input class="btn" type="submit" value="انشاء" name="btn_send">
        </form>
        <a style="color: black;" href="login.php">تملك حساب؟ تسجيل الدخول الآن</a>
    </div>

    <script>
        const passInput = document.getElementById("password");
        const showPass = () => {
            passInput.setAttribute("type", "text");
            document.getElementById("eye-show").style.display = "none";
            document.getElementById("eye-hide").style.display = "block";
        }
        const hidePass = () => {
            passInput.setAttribute("type", "password");
            document.getElementById("eye-hide").style.display = "none";
            document.getElementById("eye-show").style.display = "block";
        }
    </script>

    <script src="https://kit.fontawesome.com/d8ee9aaa2f.js" crossorigin="anonymous"></script>

    <script src="https://kit.fontawesome.com/d8ee9aaa2f.js" crossorigin="anonymous"></script>
</body>

</html>