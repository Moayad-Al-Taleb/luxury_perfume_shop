<?php
session_start();

function filter($variable)
{
    $variable = trim($variable);
    $variable = htmlspecialchars($variable);
    return $variable;
}
$error_msg = "";
$user_name = $pass = "";
$user_name_error = $pass_error = "";

if (isset($_POST['btn_send'])) {
    if (empty($_POST['user_name'])) {
        $user_name_error = "* ";
    } else {
        $user_name = filter($_POST['user_name']);
    }

    if (empty($_POST['pass'])) {
        $pass_error = "* ";
    } else {
        $pass = sha1(filter($_POST['pass']));
    }

    if (!empty($user_name) && !empty($pass)) {
        require 'connect.php';

        $sql = "SELECT * FROM customers WHERE user_name = '$user_name' AND pass = '$pass'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $_SESSION['id'] = $row['id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['status'] = $row['status'];
            $_SESSION['type'] = $row['type'];

            header("refresh:0; url=check.php");
        } else {
            $error_msg = "خطأ في ادخال البيانات";
        }

        $conn->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin/style/main.css">
    <link rel="stylesheet" href="login.css">
</head>

<body dir="rtl" class="login-page">
    <div class="over-flow-screen"></div>

    <div class="login-form">

        <h2 class="text-center title-text">مرحبا مجددا 🙋👋</h2>
        <h3>تسجيل الدخول </h3>
        <p class="err_msg"><?php echo $error_msg ?></p>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="input-field">
                <label class="label" for="username">اسم المستخدم</label>
                <input dir="ltr" name="user_name" class="input-text" type="text" id="username" placeholder="ex: ex@gmail.com">
                <?php echo $user_name_error; ?>
            </div>
            <div id="text" class="input-field">
                <label class="label" for="password">كلمة المرور</label>
                <div class="pass-con">
                    <input dir="ltr" name="pass" class="input-pass" type="password" id="password">
                    <i onclick="showPass()" id="eye-show" class="fa-solid fa-eye eye-icon "></i>
                    <i onclick="hidePass()" style="display: none;" id="eye-hide" class="fa-solid fa-eye-slash eye-icon "></i>
                </div>
                <?php echo $pass_error; ?>
            </div>
            <button name="btn_send">تسجيل الدخول</button>
        </form>
        <a href="register.php">ليس لديك حساب! انشاء جديد</a>

    </div>

    <script>
        // document.getElementById 
        // document.querySelector
        // document.querySelectorAll

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
</body>

</html>