<?php
require "config.php";
$error = "";
$v = ["name" => "", "roll_number" => "", "email" => "", "course" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($v as $k => $_) { $v[$k] = trim($_POST[$k] ?? ""); }

    if (in_array("", $v, true) || !filter_var($v["email"], FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter all details with a valid email.";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, roll_number, email, course)
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $v["name"], $v["roll_number"], $v["email"], $v["course"]);
        try {
            $stmt->execute();
            $id = $conn->insert_id;
            $errs = array_filter([
                saveFile($conn, $id, "profile_image", "Profile Image"),
                saveFile($conn, $id, "id_proof", "ID Proof"),
                saveFile($conn, $id, "certificate", "Certificate")
            ]);
            if ($errs) {
                $error = "Student saved, but: " . implode(" ", $errs);
            } else {
                header("Location: index.php?msg=" . urlencode("Student added successfully."));
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Roll number already exists.";
        }
    }
}
header_html("Add Student");
?>
<div class="box">
  <h2>Add Student</h2>
  <?php if ($error): ?><div class="msg err"><?= h($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Name</label>
    <input name="name" value="<?= h($v["name"]) ?>" required>
    <label>Roll Number</label>
    <input name="roll_number" value="<?= h($v["roll_number"]) ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= h($v["email"]) ?>" required>
    <label>Course</label>
    <input name="course" value="<?= h($v["course"]) ?>" required>
    <label>Profile Image (JPG / PNG)</label>
    <input type="file" name="profile_image" accept=".jpg,.jpeg,.png">
    <label>ID Proof (JPG / PNG / PDF)</label>
    <input type="file" name="id_proof" accept=".jpg,.jpeg,.png,.pdf">
    <label>Certificate (JPG / PNG / PDF)</label>
    <input type="file" name="certificate" accept=".jpg,.jpeg,.png,.pdf">
    <p><small>Maximum 2 MB per file.</small></p>
    <button type="submit">Save Student</button>
    <a class="btn grey" href="index.php">Cancel</a>
  </form>
</div>
<?php footer_html(); ?>
