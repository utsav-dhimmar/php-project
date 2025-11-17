<?php
require "../config/db.php";
require "../includes/functions.php";
include "../includes/header.php";

$name = $email = $phone_no = $gender = "";
$name_err = $email_err = $phone_err = $pass_err = $gender_err = $form_message =
	"";

if (isset($_POST["register"])) {
	if (
		empty(trim($_POST["name"])) ||
		!preg_match("/^[a-zA-Z\s]*$/", trim($_POST["name"]))
	) {
		$name_err = "Please enter a valid name (letters and spaces only).";
	} else {
		$name = trim($_POST["name"]);
	}

	$post_email = trim($_POST["email"]);
	if (empty($post_email)) {
		$email_err = "Please enter your email.";
	} elseif (!filter_var($post_email, FILTER_VALIDATE_EMAIL)) {
		$email_err = "Please enter a valid email format.";
	} else {
		$email = $post_email;
		$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result && $result->num_rows > 0) {
			$email_err = "An account with this email already exists.";
		}
	}

	if (empty(trim($_POST["phone_no"]))) {
		$phone_err = "Please enter your phone number.";
	} elseif (!preg_match('/^[0-9]{10}$/', trim($_POST["phone_no"]))) {
		$phone_err = "Phone number must be exactly 10 digits.";
	} else {
		$phone_no = trim($_POST["phone_no"]);
	}

	if (empty($_POST["password"])) {
		$pass_err = "Please enter a password.";
	} elseif (strlen($_POST["password"]) < 4) {
		$pass_err = "Password must have at least 4 characters.";
	} elseif (empty($_POST["confirm-password"])) {
		$pass_err = "Please confirm your password.";
	} elseif ($_POST["password"] !== $_POST["confirm-password"]) {
		$pass_err = "Passwords do not match.";
	} else {
		$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
	}

	if (empty($_POST["gender"])) {
		$gender_err = "Please select your gender.";
	} else {
		$gender = $_POST["gender"];
	}

	if (
		empty($name_err) &&
		empty($email_err) &&
		empty($phone_err) &&
		empty($pass_err) &&
		empty($gender_err)
	) {
		$stmt = $conn->prepare(
			"INSERT INTO users (name, email, phone_no, password, gender) VALUES (?, ?, ?, ?, ?)",
		);
		$stmt->bind_param(
			"sssss",
			$name,
			$email,
			$phone_no,
			$password,
			$gender,
		);

		if ($stmt->execute()) {
			echo "Registration successful! You can now log in.";
			redirect("../auth/login.php", 3);
			exit();
		} else {
			$form_message = "Something went wrong. Please try again later.";
		}
	}
}
?>
<div class="container">
  <h1 class="text-center">Registration form</h1>

  <?php if (!empty($form_message)): ?>
    <div class="alert alert-danger"><?php echo $form_message; ?></div>
  <?php endif; ?>

  <form id="form" action="" method="post" novalidate>

    <!-- name -->
    <div class="form-group">
      <label for="name">Name:</label>
      <input type="text" class="form-control <?php echo !empty($name_err)
      	? "is-invalid"
      	: ""; ?>" id="name" name="name" placeholder="Enter Your Name" value="<?php echo htmlspecialchars(
	$name,
); ?>" required />

      <div id="name-feedback" class="invalid-feedback"><?php echo htmlspecialchars(
      	$name_err,
      ); ?></div>
    </div>

    <!-- email -->
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" class="form-control <?php echo !empty($email_err)
      	? "is-invalid"
      	: ""; ?>" id="email" name="email" placeholder="Enter Your Email" value="<?php echo htmlspecialchars(
	$email,
); ?>" required />

      <div id="email-feedback" class="invalid-feedback"><?php echo htmlspecialchars(
      	$email_err,
      ); ?></div>
    </div>

    <!-- phone no -->
    <div class="form-group">
      <label for="phoneNumber">Phone Number:</label>
      <input type="text" class="form-control <?php echo !empty($phone_err)
      	? "is-invalid"
      	: ""; ?>" id="phoneNumber" name="phone_no" value="<?php echo htmlspecialchars(
	$phone_no,
); ?>" required placeholder="Enter Your Phone Number" />

      <div id="phone-feedback" class="invalid-feedback"><?php echo htmlspecialchars(
      	$phone_err,
      ); ?></div>
    </div>

    <!-- password -->
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" class="form-control <?php echo !empty($pass_err)
      	? "is-invalid"
      	: ""; ?>" id="password" name="password" placeholder="Enter Password" required />
    </div>

    <!-- confirm password -->
    <div class="form-group">
      <label for="confirm-password">Confirm password:</label>
      <input type="password" class="form-control <?php echo !empty($pass_err)
      	? "is-invalid"
      	: ""; ?>" id="confirm-password" name="confirm-password" placeholder="Enter confirm password" required />

      <div id="pass-feedback" class="invalid-feedback"><?php echo htmlspecialchars(
      	$pass_err,
      ); ?></div>
    </div>

    <!-- gender -->
    <div class="form-group">
      <label>Gender:</label>

        <div class="form-check form-check-inline">
          <input class="form-check-input <?php echo !empty($gender_err)
          	? "is-invalid"
          	: ""; ?>" type="radio" name="gender" id="male" value="male" <?php if (
	$gender === "male"
) {
	echo "checked";
} ?>>
          <label class="form-check-label" for="male">Male</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input <?php echo !empty($gender_err)
          	? "is-invalid"
          	: ""; ?>" type="radio" name="gender" id="female" value="female" <?php if (
	$gender === "female"
) {
	echo "checked";
} ?>>
          <label class="form-check-label" for="female">Female</label>
        </div>

        <div id="gender-feedback" class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$gender_err,
        ); ?></div>
      </div>



    <div class="form-group">
      <button name="register" type="submit" class="btn btn-primary">Register</button>
      <button type="reset" value="Reset" class="btn btn-secondary">Reset</button>
    </div>
  </form>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form");

    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const phoneInput = document.getElementById("phoneNumber");
    const passwordInput = document.getElementById("password");
    const confirmInput = document.getElementById("confirm-password");
    const genderInputs = document.querySelectorAll('input[name="gender"]');

    const nameFeedback = document.getElementById("name-feedback");
    const emailFeedback = document.getElementById("email-feedback");
    const phoneFeedback = document.getElementById("phone-feedback");
    const passFeedback = document.getElementById("pass-feedback");
    const genderFeedback = document.getElementById("gender-feedback");

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;


    function validateName() {
      const name = nameInput.value.trim();
      if (!name || !/^[a-zA-Z\s]*$/.test(name)) {
        nameFeedback.textContent = "Please enter a valid name.";
        nameInput.classList.add("is-invalid");
        return false;
      } else {
        nameInput.classList.remove("is-invalid");
        return true;
      }
    }

    function validateEmail() {
      const email = emailInput.value.trim();
      if (!emailRegex.test(email)) {
        emailFeedback.textContent = "Enter a valid email.";
        emailInput.classList.add("is-invalid");
        return false;
      } else {
        emailInput.classList.remove("is-invalid");
        return true;
      }
    }

    function validatePhone() {
      const phone = phoneInput.value.trim();
      if (!/^\d{10}$/.test(phone)) {
        phoneFeedback.textContent = "Phone number must be 10 digits.";
        phoneInput.classList.add("is-invalid");
        return false;
      } else {
        phoneInput.classList.remove("is-invalid");
        return true;
      }
    }

    function validatePassword() {
      const password = passwordInput.value;
      const confirm = confirmInput.value;
      let valid = true;

      if (password.length < 4) {
        passFeedback.textContent = "Password must be at least 4 characters long.";
        passwordInput.classList.add("is-invalid");
        confirmInput.classList.add("is-invalid");
        valid = false;
      } else if (password !== confirm && confirm.length > 0) {
        passFeedback.textContent = "Passwords do not match.";
        passwordInput.classList.add("is-invalid");
        confirmInput.classList.add("is-invalid");
        valid = false;
      }

      if (valid) {
        passwordInput.classList.remove("is-invalid");
        confirmInput.classList.remove("is-invalid");
      }
      return valid;
    }

    function validateGender() {
      let oneChecked = Array.from(genderInputs).some(input => input.checked);
      if (!oneChecked) {
        genderFeedback.textContent = "Please select a gender.";

        return false;
      } else {
        genderFeedback.textContent = "";
        return true;
      }
    }


    nameInput.addEventListener("input", validateName);
    emailInput.addEventListener("input", validateEmail);
    phoneInput.addEventListener("input", validatePhone);
    passwordInput.addEventListener("input", validatePassword);
    confirmInput.addEventListener("input", validatePassword);
    genderInputs.forEach(input => input.addEventListener("change", validateGender));


    form.addEventListener("submit", function(e) {

      const isNameValid = validateName();
      const isEmailValid = validateEmail();
      const isPhoneValid = validatePhone();
      const isPasswordValid = validatePassword();
      const isGenderValid = validateGender();


      if (!isNameValid || !isEmailValid || !isPhoneValid || !isPasswordValid || !isGenderValid) {
        e.preventDefault();
      }
    });
  });
</script>

<?php include "../includes/footer.php";
?>
