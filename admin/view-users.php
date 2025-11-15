<?php
require "../config/db.php";
include "../includes/header.php";
include "../includes/functions.php";

requireAdminLogin();
?>
<h2>Users</h2>

<?php
$q =
	"SELECT id, name, email, phone_no, gender, created_at FROM users ORDER BY id DESC";
$stmt = $conn->prepare($q);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
	echo '  <table class="table  table-hover">
    <thead>
        <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Phone No.</th>
            <th scope="col">Gender</th>
            <th scope="col">Created at</th>
        </tr>
    </thead>
    <tbody>';
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_array()) {
			echo "<tr>
                            <td>" .
				htmlspecialchars($row["id"]) .
				"</td>
                            <td>" .
				htmlspecialchars($row["name"]) .
				"</td>
                            <td>" .
				htmlspecialchars($row["email"]) .
				"</td>
                            <td>" .
				htmlspecialchars($row["phone_no"]) .
				"</td>
                            <td>" .
				htmlspecialchars($row["gender"]) .
				"</td>
                            <td>" .
				htmlspecialchars($row["created_at"]) .
				"</td>
                        </tr>";
		}
		echo "</tbody></table>";
	} else {
		echo '<tr><td colspan="6" class="text-center">No users have registered yet.</td></tr>';
	}
} else {
	echo '<tr><td colspan="6" class="text-center text-danger">Failed to fetch records: ' .
		htmlspecialchars($conn->error) .
		"</td></tr>";
}
?>



<?php include "../includes/footer.php";
?>
