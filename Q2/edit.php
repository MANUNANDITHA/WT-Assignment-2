<?php
require "config.php";
$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) { die("Student not found."); }
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_number"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    if ($name == "" || $roll == "" || $course == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter all details with a valid email.";
    } else {
        $stmt = $conn->prepare("UPDATE students SET name=?, roll_number=?, email=?, course=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $roll, $email, $course, $id);
        try {
            $stmt->execute();
            $errs = array_filter([
                saveFile($conn, $id, "profile_image", "Profile Image"),
                saveFile($conn, $id, "id_proof", "ID Proof"),
                saveFile($conn, $id, "certificate", "Certificate")
            ]);
            if (!$errs) {
                header("Location: view.php?id=$id");
                exit;
            }
            $error = implode(" ", $errs);
        } catch (mysqli_sql_exception $e) {
            $error = "Roll number already exists.";
        }
    }
    $student = ["id" => $id, "name" => $name, "roll_number" => $roll,
                "email" => $email, "course" => $course];
}
header_html("Edit Student");
?>
<div class="box">
  <h2>Edit Student</h2>
  <?php if ($error): ?><div class="msg err"><?= h($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $id ?>">
    <label>Name</label>
    <input name="name" value="<?= h($student["name"]) ?>" required>
    <label>Roll Number</label>
    <input name="roll_number" value="<?= h($student["roll_number"]) ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= h($student["email"]) ?>" required>
    <label>Course</label>
    <input name="course" value="<?= h($student["course"]) ?>" required>
    <label>Add Profile Image</label>
    <input type="file" name="profile_image" accept=".jpg,.jpeg,.png">
    <label>Add ID Proof</label>
    <input type="file" name="id_proof" accept=".jpg,.jpeg,.png,.pdf">
    <label>Add Certificate</label>
    <input type="file" name="certificate" accept=".jpg,.jpeg,.png,.pdf">
    <br><br>
    <button type="submit">Update Student</button>
    <a class="btn grey" href="index.php">Cancel</a>
  </form>
</div>
<?php footer_html(); ?>
