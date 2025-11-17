<?php
require "../config/db.php";
include "../includes/header.php";
include "../includes/functions.php";

requireAdminLogin();

function isTimeSlotAvailable($conn, $date, $time, $excludeCompetitionID = null)
{
	$sql = "SELECT id FROM competitions WHERE date = ? AND time = ?";
	$params = ["ss", $date, $time];

	if ($excludeCompetitionID !== null) {
		$sql .= " AND id != ?";
		$params[0] .= "i";
		$params[] = $excludeCompetitionID;
	}

	$stmt = $conn->prepare($sql);
	$stmt->bind_param(...$params);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result) {
		return $result->num_rows == 0;
	}

	return false;
}

$competitionID = $_GET["competitionID"] ?? ($_POST["id"] ?? null);
$isUpdate = !empty($competitionID) && is_numeric($competitionID);

$title = $description = $date = $time = "";
$title_err = $description_err = $date_err = $time_err = $banner_err = "";
$form_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$competitionID = $_POST["id"] ?? null;
	// for update id
	//for new it will be null
	$title = trim($_POST["title"]);
	$description = trim($_POST["description"]);
	$date = $_POST["date"];
	$time = $_POST["time"];
	$current_banner = $_POST["current_banner"] ?? "";
	$banner_name_to_save = $current_banner;

	if (isset($_FILES["banner"]) && $_FILES["banner"]["error"] == 0) {
		$upload_dir = "../uploads/";
		$allowed_extensions = ["jpg", "jpeg", "png", "gif"];
		$allowed_mime_types = ["image/jpeg", "image/png", "image/gif"];
		$max_size = 10 * 1024 * 1024;

		$file_size = $_FILES["banner"]["size"];
		$temp_file = $_FILES["banner"]["tmp_name"];
		$original_filename = $_FILES["banner"]["name"];
		$file_extension = strtolower(
			pathinfo($original_filename, PATHINFO_EXTENSION),
		);

		// First check: File extension
		if (!in_array($file_extension, $allowed_extensions)) {
			$banner_err = "Invalid file type. Only image files (JPG, JPEG, PNG, GIF) are allowed. You uploaded a .$file_extension file.";
		} else {
			// Second check: MIME type validation
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mime_type = $finfo->file($temp_file);

			if (!in_array($mime_type, $allowed_mime_types)) {
				$banner_err =
					"Invalid file type detected. Only image files are allowed. The file you uploaded appears to be a " .
					$mime_type .
					" file.";
			} else {
				// Third check: Verify it's actually an image
				$image_info = getimagesize($temp_file);

				if ($image_info === false) {
					$banner_err =
						"The uploaded file is not a valid image. Please upload only JPG, JPEG, PNG, or GIF images.";
				} elseif ($file_size > $max_size) {
					$banner_err = "File is too large. Maximum size is 10 MB.";
				} else {
					// All validations passed, proceed with upload
					$unique_filename =
						time() . "_" . uniqid() . "." . $file_extension;
					$target_file = $upload_dir . $unique_filename;

					if (move_uploaded_file($temp_file, $target_file)) {
						$banner_name_to_save = $unique_filename;

						// Delete old banner if updating
						if (
							!empty($current_banner) &&
							file_exists($upload_dir . $current_banner)
						) {
							unlink($upload_dir . $current_banner);
						}
					} else {
						$banner_err =
							"Sorry, there was an error uploading your file.";
					}
				}
			}
		}
	} elseif (isset($_FILES["banner"]) && $_FILES["banner"]["error"] != 4) {
		// Handle other upload errors (error code 4 means no file was uploaded)
		$error_messages = [
			UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the server's maximum file size.",
			UPLOAD_ERR_FORM_SIZE => "The uploaded file is too large.",
			UPLOAD_ERR_PARTIAL => "The file was only partially uploaded. Please try again.",
			UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder. Please contact the administrator.",
			UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk. Please contact the administrator.",
			UPLOAD_ERR_EXTENSION => "File upload stopped by extension. Please contact the administrator.",
		];

		$error_code = $_FILES["banner"]["error"];
		$banner_err =
			$error_messages[$error_code] ?? "Unknown upload error occurred.";
	}

	if (strlen($title) < 3) {
		$title_err = "Title must be at least 3 characters.";
	}

	if (strlen($description) < 5) {
		$description_err = "Description must be at least 5 characters.";
	}

	// not in past
	if (
		empty($date) ||
		(strtotime($date) < strtotime(date("Y-m-d")) && !$isUpdate)
	) {
		$date_err = "Date cannot be in the past.";
	}
	if (empty($time)) {
		$time_err = "Please select a time.";
	}

	if (empty($date_err) && empty($time_err)) {
		if (!isTimeSlotAvailable($conn, $date, $time, $competitionID)) {
			$time_err =
				"This time slot is already booked for another competition on this date.";
			$date_err =
				"This time slot is already booked for another competition on this date.";
		}
	}

	if (
		empty($title_err) &&
		empty($description_err) &&
		empty($date_err) &&
		empty($time_err) &&
		empty($banner_err)
	) {
		$sql = "";

		if ($competitionID) {
			$sql =
				"UPDATE competitions SET title = ?, description = ?, date = ?, time = ?, banner = ? WHERE id = ?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param(
				"sssssi",
				$title,
				$description,
				$date,
				$time,
				$banner_name_to_save,
				$competitionID,
			);
			$success_message = "Competition updated successfully!";
		} else {
			$sql =
				"INSERT INTO competitions (title, description, date, time, banner) VALUES (?, ?, ?, ?, ?)";

			$stmt = $conn->prepare($sql);

			$stmt->bind_param(
				"sssss",
				$title,
				$description,
				$date,
				$time,
				$banner_name_to_save,
			);

			$success_message = "Competition added successfully!";
		}

		if ($stmt->execute()) {
			redirect(
				"/college-competition-portal/admin/view-competition.php",
				0,
			);
			exit();
		} else {
			$form_message = "Database error: " . $conn->error;
		}
	}
} elseif ($isUpdate) {
	$stmt = $conn->prepare(
		"SELECT title, description, date, time, banner FROM competitions WHERE id = ?",
	);
	$stmt->bind_param("i", $competitionID);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($row = $result->fetch_assoc()) {
		$title = $row["title"];
		$description = $row["description"];
		$date = $row["date"];
		$time = $row["time"];
		$current_banner = $row["banner"] ?? "";
	} else {
		echo "Error: Competition with this ID was not found.";
		redirect("/college-competition-portal/admin/view-competition.php", 1);
	}
}
?>
<h2>
    <?php if ($isUpdate) {
    	echo "Update";
    } else {
    	echo "Add";
    } ?>

    Competition</h2>
<?php if (!empty($form_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($form_message); ?>
    </div>
<?php endif; ?>
<!-- <form action="../../server/admin.php" method="post"> -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" class="form-control <?php echo !empty($title_err)
        	? "is-invalid"
        	: ""; ?>" id="title" name="title" required value="<?php echo htmlspecialchars(
	$title,
); ?>">
        <p id="titlewarn" class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$title_err,
        ); ?></p>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control <?php echo !empty($description_err)
        	? "is-invalid"
        	: ""; ?>" id="description" name="description" rows="3"><?php echo trim(
	htmlspecialchars($description),
); ?></textarea>
        <p id="descwarn" class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$description_err,
        ); ?></p>
    </div>
    <div class="form-group">
        <label for="date">Date:</label>
        <input type="date" class="form-control <?php echo !empty($date_err)
        	? "is-invalid"
        	: ""; ?>" id="date" name="date" value="<?php echo htmlspecialchars(
	$date,
); ?>">
        <p id="datewarn" class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$date_err,
        ); ?></p>
    </div>
    <div class="form-group">
        <label for="time">Time:</label>
        <input type="time" class="form-control <?php echo !empty($time_err)
        	? "is-invalid"
        	: ""; ?>

        " id="time" name="time" value="<?php echo htmlspecialchars(
        	$time,
        ); ?>" min="09:00" max="18:00">
        <p id="timewarn" class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$time_err,
        ); ?></p>
    </div>

    <!-- banner -->
    <div class="form-group">
        <label for="banner">Competition Banner</label>
        <?php if ($isUpdate && !empty($current_banner)): ?>
            <div class="mb-2">
                <p>Current Banner:</p>
                <img src="/college-competition-portal/uploads/<?php echo htmlspecialchars(
                	$current_banner,
                ); ?>" alt="Current Banner" style="max-width: 300px; height: auto;">
            </div>
            <p><small>Upload a new file below to replace the current banner.</small></p>
        <?php endif; ?>
        <input type="file" class="form-control-file <?php echo !empty(
        	$banner_err
        )
        	? "is-invalid"
        	: ""; ?>" id="banner" name="banner" accept="image/png, image/gif, image/jpeg">
        <div class="invalid-feedback d-block"><?php echo htmlspecialchars(
        	$banner_err,
        ); ?></div>
    </div>
    <input type="hidden" value="true" <?php if ($isUpdate) {
    	echo 'name="admin-update-competition"';
    } else {
    	echo 'name="admin-add-competition"';
    } ?> />
    <?php if ($isUpdate) {
    	echo '<input type="hidden" value="' .
    		htmlspecialchars($competitionID) .
    		'" name="id"/>';
    	echo '<input type="hidden" value="' .
    		htmlspecialchars($current_banner) .
    		'" name="current_banner"/>';
    } ?>
    <button type="submit" class="btn btn-primary">
        <?php if ($isUpdate) {
        	echo "Update";
        } else {
        	echo "Add";
        } ?>
        competition
    </button>
</form>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        const titleInput = document.getElementById("title");
        const descInput = document.getElementById("description");
        const dateInput = document.getElementById("date");
        const timeInput = document.getElementById("time");

        const titleWarn = document.getElementById("titlewarn");
        const descWarn = document.getElementById("descwarn");
        const dateWarn = document.getElementById("datewarn");
        const timeWarn = document.getElementById("timewarn");

        const minDate = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', minDate);

        function validateTitle() {
            const title = titleInput.value.trim();
            if (title.length < 3) {
                titleWarn.textContent = "Title must be at least 3 characters.";
                titleWarn.style.display = "block";
                titleInput.classList.add("is-invalid");
                return false;
            } else {
                titleWarn.style.display = "none";
                titleInput.classList.remove("is-invalid");
                return true;
            }
        }

        function validateDescription() {
            const desc = descInput.value.trim();
            if (desc.length < 5) {
                descWarn.textContent = "Description must be at least 5 characters.";
                descWarn.style.display = "block";
                descInput.classList.add("is-invalid");
                return false;
            } else {
                descWarn.style.display = "none";
                descInput.classList.remove("is-invalid");
                return true;
            }
        }

        function validateDate() {
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dateInput.value === "" || selectedDate < today) {
                dateWarn.textContent = "Date cannot be in the past.";
                dateWarn.style.display = "block";
                dateInput.classList.add("is-invalid");
                return false;
            } else {
                dateWarn.style.display = "none";
                dateInput.classList.remove("is-invalid");
                return true;
            }
        }

        function validateTime() {
            const selectedTime = timeInput.value;
            const minTime = "09:00";
            const maxTime = "18:00";
            let isValid = true;
            let errorMessage = "";

            if (!selectedTime) {
                errorMessage = "Please select a time.";
                isValid = false;
            } else if (selectedTime < minTime || selectedTime > maxTime) {
                errorMessage = "Time must be between 9:00 AM and 6:00 PM.";
                isValid = false;
            }

            if (!isValid) {
                timeWarn.textContent = errorMessage;
                timeWarn.style.display = "block";
                timeInput.classList.add("is-invalid");
                return false;
            } else {
                timeWarn.textContent = "";
                timeWarn.style.display = "none";
                timeInput.classList.remove("is-invalid");
                return true;
            }
        }

        function validateForm() {
            const isTitleValid = validateTitle();
            const isDescriptionValid = validateDescription();
            const isDateValid = validateDate();
            const isTimeValid = validateTime();
            return isTitleValid && isDescriptionValid && isDateValid && isTimeValid;
        }

        titleInput.addEventListener("change", validateTitle);
        descInput.addEventListener("change", validateDescription);
        dateInput.addEventListener("blur", validateDate);
        timeInput.addEventListener("change", validateTime);

        form.addEventListener("submit", function(event) {
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    });
</script>


<?php include "../includes/footer.php";
?>
