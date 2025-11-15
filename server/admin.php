<?php
require("../config/db.php");
require("../includes/functions.php");
session_start();

const ADMIN_EMAIL = "admin@ccp.com";
const ADMIN_PASSWORD = "abcd@admin";


function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidDate($date)
{
    return strtotime($date) >= strtotime(date("Y-m-d"));
}

function isValidTime($time)
{
    return preg_match("/^\d{2}:\d{2}$/", $time);
}

function isValidText($text, $minLength = 3)
{
    return strlen(trim($text)) >= $minLength;
}


if (isset($_POST['admin-login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($email) || !isValidEmail($email)) {
        $errors[] = "Invalid or empty email.";
    }

    if (empty($password) || strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } elseif ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_email'] = ADMIN_EMAIL;
        echo "Login successfully";
        redirect("../admin/links.php");
        exit();
    } else {
        echo "<p style='color:red;'>Incorrect admin credentials.</p>";
    }
} elseif (isset($_POST['admin-add-competition'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $errors = [];

    if (!isValidText($title)) {
        $errors[] = "Title must be at least 3 characters.";
    }

    if (!isValidText($description, 5)) {
        $errors[] = "Description must be at least 5 characters.";
    }

    if (empty($date) || !isValidDate($date)) {
        $errors[] = "Date must be today or later.";
    }

    if (empty($time) || !isValidTime($time)) {
        $errors[] = "Invalid time format.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {
        $q = "INSERT INTO competitions (title,description,date,time)
              VALUES ('$title','$description','$date','$time')";
        // OOP way
        $result = $conn->query($q);

        if ($result) {
            echo "Competition created successfully";
            redirect("../admin/view-competition.php");
            exit();
        } else {
            echo "Failed to insert records: " . $conn->error;
        }
    }
} elseif (isset($_POST['admin-update-competition'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $errors = [];

    if (!is_numeric($id)) {
        $errors[] = "Invalid competition ID.";
    }

    if (!isValidText($title)) {
        $errors[] = "Title must be at least 3 characters.";
    }

    if (!isValidText($description, 5)) {
        $errors[] = "Description must be at least 5 characters.";
    }

    if (empty($date) || !isValidDate($date)) {
        $errors[] = "Date must be today or later.";
    }

    if (empty($time) || !isValidTime($time)) {
        $errors[] = "Invalid time format.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {
                $q = "UPDATE competitions
                      SET title = '$title', description = '$description', date = '$date', time = '$time'
                      WHERE id = $id";
                // OOP way
                $result = $conn->query($q);

                if ($result) {
                    echo "Competition updated successfully";
                    redirect("../admin/view-competition.php");
                    exit();
                } else {
                    echo "Failed to update records: " . $conn->error;
                }    }
} elseif (isset($_GET['logout'])) {
    unset($_SESSION['admin_email']);
    redirect("/college-competition-portal/index.php", 0);
    exit();
} else {
    echo "<p style='color:red;'>Something went wrong.</p>";
}
