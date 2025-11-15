<?php
include "../includes/header.php";
include "../includes/functions.php";
$email = $_SESSION["admin_email"];

if (!$email) {
	redirect("/college-competition-portal/", 10);
	exit();
}
?>

<p>Hello <?php echo htmlspecialchars($_SESSION["admin_email"]); ?> </p>

<ul class="list-unstyled d-flex flex-wrap justify-content-center">
    <li class="m-2">
        <a href="/college-competition-portal" class="btn btn-outline-primary  py-2">Home</a>
    </li>
    <li class="m-2">
        <a href="/college-competition-portal/admin/competition-form.php" class="btn btn-outline-primary py-2">Add Competitions</a>
    </li>
    <li class="m-2">
        <a href="/college-competition-portal/admin/view-competition.php" class="btn btn-outline-primary py-2">See Competitions</a>
    </li>
    <li class="m-2">
        <a href="/college-competition-portal/admin/view-users.php" class="btn btn-outline-primary py-2">View Users</a>
    </li>

    <?php if (isset($_SESSION["admin_email"])): ?>
        <li class="m-2">
            <button name="logout" class="btn btn-outline-primary py-2">
                Logout
            </button>
        </li>

    <?php endif; ?>
</ul>
