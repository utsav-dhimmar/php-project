<?php
require "../config/db.php";
include "../includes/header.php";
include "../includes/functions.php";
requireLogin();

if (!isset($_GET["competition_id"]) || !is_numeric($_GET["competition_id"])) {
	redirect("my-competition.php");
	exit();
}

$competition_id = (int) $_GET["competition_id"];
$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT date FROM competitions WHERE id = ?");
$stmt->bind_param("i", $competition_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$competition_date = strtotime($row["date"]);
	$today = strtotime(date("Y-m-d"));
	$diff = $competition_date - $today;
	$days_left = floor($diff / (60 * 60 * 24));

	if ($days_left >= 2) {
		$delete_stmt = $conn->prepare(
			"DELETE FROM registrations WHERE user_id = ? AND competition_id = ?",
		);
		$delete_stmt->bind_param("ii", $user_id, $competition_id);
		if ($delete_stmt->execute()) {
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
