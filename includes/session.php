<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_type"])) {
    header("Location: /public/login/login-front.php");
    exit();
}

function check_access($allowed_roles)
{
    if (!in_array($_SESSION["user_type"], $allowed_roles)) {
        header("Location: ../../public/authorization/unauthorized.php");
        exit();
    }
}
