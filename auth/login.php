<?php
include "../includes/header.php";
require "../config/db.php";

require "../includes/functions.php";
if (isset($_SESSION["admin_email"])) {
	redirect("/college-competition-portal/admin/view-competition.php", 0);
	exit();
}
if (isLoggedIn()) {
	redirect("/college-competition-portal", 0);
	exit();
}

$error_message = "";

if (isset($_POST["login"])) {
	$email = trim($_POST["email"]);
	$password = $_POST["password"];

	if (empty($email) || empty($password)) {
		$error_message = "Email and password are required.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = "Invalid email.";
	} else {
		$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if (password_verify($password, $row["password"])) {
				$_SESSION["user_id"] = $row["id"];
				echo "Login successfully. Redirecting to competition page...";
				redirect("../users/competition.php");
				exit();
			} else {
				$error_message = "Invalid email or password.";
			}
		} else {
			$error_message = "Invalid email or password.";
		}
	}
}
?>

<div class="container">
  <h1 class="text-center">Login Page</h1>
  <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo htmlspecialchars($error_message); ?>
    </div>
  <?php endif; ?>

  <!-- <form id="form" action="../server/main.php" method="POST"> -->
  <form id="form" action="" method="POST"> <!--  IP phase-->
    <!-- email -->
    <div class="form-group">
      <label for="email">Email:</label>
      <input
        type="email"
        class="form-control"
        id="email"
        name="email"
        placeholder="Enter Your Email" />
      <p id="emailwarn" class="invalid-feedback" style="display: none"></p>
    </div>

    <!-- password -->
    <div class="form-group">
      <label for="password">Password:</label>
      <input
        type="password"
        class="form-control"
        id="password"
        name="password"
        maxlength="10"
        placeholder="Enter Password" />
      <p id="passwarn" class="invalid-feedback" style="display: none"></p>
    </div>
    <!-- <input type="hidden" name="login" value="true" /> -->
    <!-- button -->
    <div class="form-group">
      <button name="login" type="submit" class="btn btn-primary">Login</button>
      <button type="reset" value="Reset" class="btn btn-secondary">Reset</button>
    </div>
  </form>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const emailWarn = document.getElementById("emailwarn");
    const passWarn = document.getElementById("passwarn");


    emailInput.addEventListener("change", function() {
      const email = emailInput.value.trim();
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!emailRegex.test(email)) {
        emailWarn.textContent = "Enter a valid email.";
        emailWarn.style.display = "block";
        emailInput.classList.add("is-invalid");
      } else {
        emailWarn.style.display = "none";
        emailInput.classList.remove("is-invalid");
      }
    });


    passwordInput.addEventListener("change", function() {
      const password = passwordInput.value;

      if (password.length < 4) {
        passWarn.textContent = "Password must be at least 4 characters long.";
        passWarn.style.display = "block";
        passwordInput.classList.add("is-invalid");
      } else {
        passWarn.style.display = "none";
        passwordInput.classList.remove("is-invalid");
      }
    });


    document.getElementById("form").addEventListener("submit", function(e) {
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      let valid = true;

      if (email.indexOf("@") < 1 || email.lastIndexOf(".") - email.indexOf("@") < 2) {
        emailWarn.textContent = "Enter a valid email.";
        emailWarn.style.display = "block";
        emailInput.classList.add("is-invalid");
        valid = false;
      }

      if (password.length < 4) {
        passWarn.textContent = "Password must be at least 4 characters long.";
        passWarn.style.display = "block";
        passwordInput.classList.add("is-invalid");
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  });
</script>
