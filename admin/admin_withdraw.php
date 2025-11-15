<?php
require "../config/db.php";
include "../includes/header.php";
include "../includes/functions.php";
requireAdminLogin();

if (!isset($_GET["registration_id"]) || !is_numeric($_GET["registration_id"])) {
	redirect("view-competition.php");
	exit();
}

$registration_id = (int) $_GET["registration_id"];

$stmt = $conn->prepare(
	"SELECT c.date, r.competition_id FROM registrations r JOIN competitions c ON r.competition_id = c.id WHERE r.id = ?",
);
$stmt->bind_param("i", $registration_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$competition_date = strtotime($row["date"]);
	$today = strtotime(date("Y-m-d"));
	$diff = $competition_date - $today;
	$days_left = floor($diff / (60 * 60 * 24));
	$competition_id = $row["competition_id"];

	if ($days_left >= 2) {
		$delete_stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
		$delete_stmt->bind_param("i", $registration_id);
		if ($delete_stmt->execute()) {
			redirect(
				"view-participants.php?competitionID=" .
					$competition_id .
					"&withdrawal=success",
			);
		} else {
			redirect(
				"view-participants.php?competitionID=" .
					$competition_id .
					"&withdrawal=error",
			);
		}
	} else {
		redirect(
			"view-participants.php?competitionID=" .
				$competition_id .
				"&withdrawal=not_allowed",
		);
	}
} else {
	redirect("view-competition.php?withdrawal=invalid_registration");
}

?>
