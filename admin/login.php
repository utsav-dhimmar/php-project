<?php
require "../includes/functions.php";
include "../includes/header.php";
const ADMIN_EMAIL = "admin@ccp.com";
const ADMIN_PASSWORD = "abcd@admin";

$error_message = "";

// normal user no
if (isset($_SESSION["user_id"])) {
	redirect("/college-competition-portal/users/dashboard.php", 0);
	exit();
}

// Ialready login bye bye
if (isset($_SESSION["admin_email"])) {
	redirect("/college-competition-portal/admin/links.php", 0);
	exit();
}

if (isset($_POST["admin-login"])) {
	$email = trim($_POST["email"]);
	$password = $_POST["password"];

	if (empty($email) || empty($password)) {
		$error_message = "Email and password fields are required.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = "Invalid email.";
	} elseif ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
		echo "login successful";
		$_SESSION["admin_email"] = ADMIN_EMAIL;
		// var_dump($_SESSION);
		redirect("../admin/links.php", 1);
		exit();
	} else {
		$error_message = "Invalid admin credentials. Please try again.";
	}
}
?>

<div class="container">
  <h1 class="text-center">Login as <b>admin</b></h1>

  <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo htmlspecialchars($error_message); ?>
    </div>
  <?php endif; ?>

  <form id="form" action="" method="POST">
    <!-- email -->
    <div class="form-group">
      <label for="email">Email:</label>
      <input
        type="email"
        class="form-control"
        id="email"
        name="email"
        placeholder="Enter Your Email"
        required />
      <div id="email-feedback" class="invalid-feedback"></div>
    </div>

    <!-- password -->
    <div class="form-group">
      <label for="password">Password:</label>
      <input
        type="password"
        class="form-control"
        id="password"
        name="password"
        placeholder="Enter Password"
        required />
      <div id="pass-feedback" class="invalid-feedback"></div>
    </div>

    <input type="hidden" name="admin-login" value="true" />
    <!-- button -->
    <div class="form-group">
      <button name="login" type="submit" class="btn btn-primary">Login</button>
      <button type="reset" value="Reset" class="btn btn-secondary">Reset</button>
    </div>
  </form>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");

    const emailFeedback = document.getElementById("email-feedback");
    const passFeedback = document.getElementById("pass-feedback");

    function validateEmail() {
      const email = emailInput.value.trim();
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!emailRegex.test(email)) {
        emailFeedback.textContent = "Please enter a valid email address.";
        emailInput.classList.add("is-invalid");
        return false;
      } else {
        emailInput.classList.remove("is-invalid");
        return true;
      }
    }

    function validatePassword() {
      const password = passwordInput.value;
      if (password.length < 4) {
        passFeedback.textContent = "Password must be at least 4 characters long.";
        passwordInput.classList.add("is-invalid");
        return false;
      } else {
        passwordInput.classList.remove("is-invalid");
        return true;
      }
    }


    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);


    form.addEventListener("submit", function(e) {
      if (!validateEmail() || !validatePassword()) {
        e.preventDefault();
      }
    });
  });
</script>

<?php include "../includes/footer.php";
?>
