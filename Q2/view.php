<?php
require "config.php";
$id = (int)($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) { die("Student not found."); }

$stmt = $conn->prepare("SELECT * FROM student_files WHERE student_id=? ORDER BY file_id");
$stmt->bind_param("i", $id);
$stmt->execute();
$files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
header_html("Student Profile");
?>
<div class="box">
  <h2>Student Profile</h2>
  <p><b>Name:</b> <?= h($student["name"]) ?></p>
  <p><b>Roll Number:</b> <?= h($student["roll_number"]) ?></p>
  <p><b>Email:</b> <?= h($student["email"]) ?></p>
  <p><b>Course:</b> <?= h($student["course"]) ?></p>

  <h3>Uploaded Files</h3>
  <?php if (!$files): ?>
    <p>No files uploaded.</p>
  <?php else: ?>
  <table>
    <tr><th>Type</th><th>File</th><th>Format</th><th>Uploaded On</th><th>Preview</th></tr>
    <?php foreach ($files as $f): ?>
    <tr>
      <td><?= h($f["doc_type"]) ?></td>
      <td><?= h($f["original_name"]) ?></td>
      <td><?= h($f["file_type"]) ?></td>
      <td><?= h($f["upload_date"]) ?></td>
      <td>
        <?php if (strpos($f["file_type"], "image/") === 0): ?>
          <img class="profile" src="<?= h($f["file_path"]) ?>" alt="<?= h($f["doc_type"]) ?>">
        <?php else: ?>
          <a href="<?= h($f["file_path"]) ?>" target="_blank">Open PDF</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
  <br>
  <a class="btn" href="edit.php?id=<?= $student["id"] ?>">Edit</a>
  <a class="btn grey" href="index.php">Back</a>
</div>
<?php footer_html(); ?>
