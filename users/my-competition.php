<?php
require("../config/db.php");
include("../includes/header.php");
include("../includes/functions.php");
requireLogin();

?>

<div class="container mt-5">
  <h2 class="mb-4">Joined Competitions</h2>

  <?php
  if (isset($_GET['withdrawal'])) {
    $withdrawal_status = $_GET['withdrawal'];
    if ($withdrawal_status == 'success') {
      echo "<div class='alert alert-success'>You have successfully withdrawn from the competition.</div>";
    } else if ($withdrawal_status == 'error') {
      echo "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
    } else if ($withdrawal_status == 'not_allowed') {
      echo "<div class='alert alert-warning'>Withdrawal is not allowed at this time. Please contact the admin.</div>";
    } else if ($withdrawal_status == 'invalid_competition') {
      echo "<div class='alert alert-danger'>Invalid competition.</div>";
    }
  }
  ?>

  <?php

  $user_id = $_SESSION['user_id'];

    $q = "SELECT
      competitions.id as competition_id,
      competitions.title as title,
      competitions.date as date,
      competitions.time as time,
      registrations.created_at as join_date
      from registrations join competitions on registrations.competition_id = competitions.id  where registrations.user_id  = $user_id";
    $result =  $conn->query($q);


    if ($result) {
      if ($result->num_rows > 0) {
        echo ' <table class="table  table-hover">
          <thead>
            <tr>
              <th scope="col">title</th>
              <th scope="col">date</th>
              <th scope="col">time</th>
              <th scope="col">join date</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>';
        while ($row = $result->fetch_assoc()) {
          $competition_date = strtotime($row['date']);
          $today = strtotime(date("Y-m-d"));
          $diff = $competition_date - $today;
          $days_left = floor($diff / (60 * 60 * 24));

          echo "<tr>
                      <td>" . htmlspecialchars($row['title']) . "</td>
                      <td>" . htmlspecialchars($row['date']) . "</td>
                      <td>" . htmlspecialchars($row['time']) . "</td>
                      <td>" . htmlspecialchars($row['join_date']) . "</td>";

          if ($days_left >= 2) {
            echo "<td><a href='withdraw.php?competition_id=" . $row['competition_id'] . "' class='btn btn-danger'>Withdraw</a></td>";
          } else {
            echo "<td><button class='btn btn-danger withdraw-btn' disabled data-toggle='tooltip' data-placement='top' title='Withdrawal not allowed. Contact admin.'>Withdraw</button></td>";
          }

          echo "</tr>";
        }
        echo ' </tbody>
    </table>';    } else {
      echo "<div class='alert alert-info'>You are nor take part in any competition</div>";
    }
  } else {
    echo "<div class='alert alert-danger'>Failed to fetch records: " . $conn->error . "</div>";
  }
  ?>

</div>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

if (window.innerWidth <= 768) {
    var withdrawButtons = document.querySelectorAll(".withdraw-btn");
    for (var i = 0; i < withdrawButtons.length; i++) {
        withdrawButtons[i].addEventListener("click", function() {
            alert("Withdrawal not allowed. Contact admin.");
        });
    }
}
</script>
<?php
include("../includes/footer.php");
?>
