<?php
require "../config/db.php";
include "../includes/header.php";
include "../includes/functions.php";
requireLogin();
$competitionID = $_GET["competitionID"] ?? "";
if (!$competitionID) {
	redirect("/college-competition-portal/users/competition.php");
	exit();
}

$userID = (int) $_SESSION["user_id"];
$competitionID = (int) $_GET["competitionID"];
$check_stmt = $conn->prepare(
	"SELECT id FROM registrations WHERE user_id = ? AND competition_id = ?",
);
$check_stmt->bind_param("ii", $userID, $competitionID);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result && $check_result->num_rows > 0) {
	echo "You have already joined this competition.";

	redirect("/college-competition-portal/users/my-competition.php", 1);
	exit();
}

$insert_stmt = $conn->prepare(
	"INSERT INTO registrations (user_id, competition_id) VALUES (?, ?)",
);

$insert_stmt->bind_param("ii", $userID, $competitionID);

$result = $insert_stmt->execute();

if ($result) {
	echo "Register successfully redirect to my competition page";
	redirect("/college-competition-portal/users/my-competition.php");
	exit();
} else {
	echo "<div class='alert alert-danger'>Failed to fetch records: " .
		htmlspecialchars($conn->error) .
		"</div>";
	exit();
}
?>

<?php include "../includes/footer.php";
?>
