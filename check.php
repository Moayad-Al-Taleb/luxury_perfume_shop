<?php
session_start();

if (isset($_SESSION['id'])) {
    if ($_SESSION['type'] == 0) {
        header("refresh:0; url=admin/users_management.php");
    } elseif ($_SESSION['type'] == 1) {
        if ($_SESSION['status'] == 0) {
            header("refresh:0; url=user/home-page.php");
        } else {
            echo "The account is inactive";
        }
    }
}
