<?php
require("../config/db.php");
include("../includes/header.php");
include("../includes/functions.php");
requireAdminLogin();
if (!isset($_GET['competitionID']) || !is_numeric($_GET['competitionID'])) {
    die("Error: Invalid or missing Competition ID.");
}
$competitionID = (int)$_GET['competitionID'];
$title_query = "SELECT title FROM competitions WHERE id = $competitionID";
$title_result = $conn->query($title_query);
$competition_title = "Participants";
if ($title_row = $title_result->fetch_assoc()) {
    $competition_title = "Participants for '" . htmlspecialchars($title_row['title']) . "'";
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?php echo $competition_title; ?></h2>
    <a href="/college-competition-portal/admin/view-competition.php" class="btn btn-secondary">Back to Competitions</a>
</div>


<?php

$competitionID = $_GET['competitionID'];
$q = "SELECT
            users.name as username,
            users.id as userId,
            competitions.title as competitionTitle,
            registrations.created_at as joinDate
         FROM registrations JOIN users ON registrations.user_id = users.id JOIN competitions ON registrations.competition_id = competitions.id WHERE competitions.id = $competitionID";
$result =  $conn->query($q);

if ($result) {

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
            <th scope="col">competition Title</th>
            <th scope="col">joinDate</th>
        </tr>
    </thead>
    <tbody>';
        while ($row = $result->fetch_array()) {
            echo "<tr>

                            <td>" . htmlspecialchars($row['userId']) . "</td>
                            <td>" . htmlspecialchars($row['username']) . "</td>
                            <td>" . htmlspecialchars($row['competitionTitle']) . "</td>
                            <td>" . htmlspecialchars($row['joinDate']) . "</td>

                        </tr>";
        }
        echo '</tbody></table>';
    } else {
        echo "<div class='alert alert-info'>No participants found</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Failed to fetch records: " . $conn->error . "</div>";
}
?>


<?php
include("../includes/footer.php");
?>
