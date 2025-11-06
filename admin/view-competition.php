<?php
require("../config/db.php");
include("../includes/header.php");
include("../includes/functions.php");

requireAdminLogin();
?>
<!-- page contins the list of competition -->
<div class="container mt-5">
  <h2 class="mb-4">Competitions</h2>
  <?php
  $q = "SELECT id, title, description,banner FROM competitions ORDER BY date DESC, time DESC";
  $result =  $conn->query($q);

  if ($result) {
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_array()) {
        echo "<div class='card mb-4'>
                    <div class='card-body'>
                        <h4 class='card-title'>" . htmlspecialchars($row['title']) . "</h4>
                        <p class='card-text'>" . htmlspecialchars($row['description']) . "</p>
                        <img src='/college-competition-portal/uploads/" . htmlspecialchars($row['banner']) . "' alt='Banner Missing' style='max-width: 300px; height: auto;'>
                    </div>
                    <div class='card-footer'>
                        <a href='/college-competition-portal/admin/competition-form.php/?competitionID=" . htmlspecialchars($row['id']) . "' class='btn btn-primary'>Update</a>
                        <a href='/college-competition-portal/admin/view-participants.php/?competitionID=" . htmlspecialchars($row['id']) . "' class='btn btn-primary'>View participants</a>
                    </div>
                </div>";
      }
    } else {
      echo "<div class='alert alert-info'>No competitions found , Click the Add New Competition button to add new competition.</div>";
    }
  } else {
    echo "<div class='alert alert-danger'>Failed to fetch records: " . $conn->error . "</div>";
  }
  ?>

  <?php
  include("../includes/footer.php");
  ?>
