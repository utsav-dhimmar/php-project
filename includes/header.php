<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Competitions - My System</title>
  <link rel="stylesheet" href="/college-competition-portal/bootstrap/bootstrap.min.css" />
  <link rel="stylesheet" href="/college-competition-portal/assets/css/styles.css">
</head>

<body class="text-white">
  <header>
    <nav class="container mt-3">
      <div class="row">
        <div class="col-12">
          <ul class="list-unstyled d-flex flex-wrap justify-content-center">
            <li class="m-2">
              <a href="/college-competition-portal" class="btn btn-outline-primary  py-2">Home</a>
              <!-- <a href="/college-competition-portal" class="btn  py-2">Home</a> -->
            </li>

            <?php if (!isset($_SESSION['admin_email'])): ?>
              <li class="m-2">
                <a href="/college-competition-portal/users/competition.php" class="btn btn-outline-primary  py-2">Competitions</a>
              </li>
            <?php endif; ?>

            <li class="m-2">
              <a href="/college-competition-portal/about.php" class="btn btn-outline-primary  py-2">About</a>
            </li>

            <?php if (isset($_SESSION['admin_email'])): ?>
              <li class="m-2">
                <a href="/college-competition-portal/admin/competition-form.php" class="btn btn-outline-primary py-2">Add Competitions</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/admin/view-competition.php" class="btn btn-outline-primary py-2">See Competitions</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/admin/view-users.php" class="btn btn-outline-primary  py-2">View Users</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/server/admin.php?logout=true" class="btn btn-outline-warning py-2">Logout</a>
              </li>
            <?php elseif (isset($_SESSION['user_id'])): ?>
              <li class="m-2">
                <a href="/college-competition-portal/users/dashboard.php" class="btn btn-outline-primary py-2">dashboard</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/server/main.php?logout=true" class="btn btn-outline-warning py-2">Logout</a>
              </li>
            <?php else: ?>
              <li class="m-2">
                <a href="/college-competition-portal/auth/register.php" class="btn btn-outline-primary  py-2">Register</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/auth/login.php" class="btn btn-outline-primary  py-2">Login</a>
              </li>
              <li class="m-2">
                <a href="/college-competition-portal/admin/login.php" class="btn btn-outline-primary  py-2">Admin login</a>
              </li>
            <?php endif; ?>

          </ul>
        </div>
      </div>
    </nav>
  </header>


  <main class="container my-2">
