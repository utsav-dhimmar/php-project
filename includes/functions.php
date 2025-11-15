<!-- contains command function which may be used multiple time  -->

<?php
function redirect($url, $time = 2)
{
    header("refresh:$time;url=$url");
    return;
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /college-competition-portal/auth/login.php");
        exit();
    }
}

function requireAdminLogin()
{
    if (!isset($_SESSION['admin_email'])) {
        header("Location: /college-competition-portal/admin/login.php");
        exit();
    }
}
?>