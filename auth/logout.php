<?php
session_start();

if (isset($_GET["admin"])) {
	unset($_SESSION["admin_email"]);
	header("Location: /college-competition-portal/index.php");
	exit();
} else {
	unset($_SESSION["user_id"]);
	header("Location: /college-competition-portal/index.php");
	exit();
}
