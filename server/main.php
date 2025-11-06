<?php

require("../includes/functions.php");
session_start();


function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidPhone($phone)
{
    return preg_match('/^[0-9]{10}$/', $phone);
}

function isStrongPassword($password)
{
    return strlen($password) >= 6;
}

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone_no']);
    $password = $_POST['password'];
    $confirmPass = $_POST['confirm-password'];
    $gender = trim($_POST['gender']);

    $errors = [];

    if (empty($name) || empty($email) || empty($phone_no) || empty($password) || empty($confirmPass) || empty($gender)) {
        $errors[] = "All fields are required.";
    }

    if (!isValidEmail($email)) {
        $errors[] = "Invalid email format.";
    }

    if (!isValidPhone($phone_no)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (!isStrongPassword($password)) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirmPass) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {

        $check_user_query = "SELECT * FROM users WHERE email = '$email'";
        // OOP way
        $check_user_result = $conn->query($check_user_query);

        if ($check_user_result->num_rows > 0) {
            echo "<p style='color:red;'>User with this email already exists.</p>";
        } else {

                        $q = "INSERT INTO users (name,email,phone_no,password,gender)
                              VALUES ('$name','$email','$phone_no','$password','$gender')";
                        // OOP way
                        $result = $conn->query($q);

                        if ($result) {
                            echo "Register successfully. Redirecting to login page...";
                            redirect("../auth/login.php");
                        } else {
                            echo "<p style='color:red;'>Failed to insert records: " . $conn->error . "</p>";
                            redirect("../auth/register.php");
                        }        }
    }
} else if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $q = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    // OOP way
    $result = $conn->query($q);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_array();
        $_SESSION['user_id'] = $row['id'];
        echo "Login successfully. Redirecting to competition page...";
        redirect("../users/competition.php");
    } else {
        echo "<p style='color:red;'>Invalid email or password.</p>";
        redirect("../auth/login.php");
    }
} elseif (isset($_GET['logout'])) {
    unset($_SESSION['user_id']);
    redirect("/college-competition-portal/index.php", 0);
}
