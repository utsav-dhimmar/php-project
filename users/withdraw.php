<?php
require("../config/db.php");
include("../includes/header.php");
include("../includes/functions.php");
requireLogin();

if (!isset($_GET['competition_id']) || !is_numeric($_GET['competition_id'])) {
    redirect("my-competition.php");
}

$competition_id = (int)$_GET['competition_id'];
$user_id = (int)$_SESSION['user_id'];

$q = "SELECT date FROM competitions WHERE id = $competition_id";
$result = $conn->query($q);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $competition_date = strtotime($row['date']);
    $today = strtotime(date("Y-m-d"));
    $diff = $competition_date - $today;
    $days_left = floor($diff / (60 * 60 * 24));

    if ($days_left >= 2) {
        $delete_q = "DELETE FROM registrations WHERE user_id = $user_id AND competition_id = $competition_id";
        if ($conn->query($delete_q)) {
            redirect("my-competition.php?withdrawal=success");
        } else {
            redirect("my-competition.php?withdrawal=error");
        }
    } else {
        redirect("my-competition.php?withdrawal=not_allowed");
    }
} else {
    redirect("my-competition.php?withdrawal=invalid_competition");
}

?>
