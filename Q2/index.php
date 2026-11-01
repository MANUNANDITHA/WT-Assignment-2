<?php
require "config.php";
$msg = $_GET["msg"] ?? "";
$students = $conn->query("SELECT s.*, COUNT(f.file_id) AS files
    FROM students s LEFT JOIN student_files f ON f.student_id = s.id
    GROUP BY s.id ORDER BY s.id DESC");
header_html("Student Admin Panel");
?>
<?php if ($msg): ?><div class="msg ok"><?= h($msg) ?></div><?php endif; ?>
<div class="box">
  <a class="btn" href="add.php">+ Add Student</a>
  <h2>All Student Records</h2>
  <table>
    <tr><th>Name</th><th>Roll Number</th><th>Course</th><th>Files</th><th>Actions</th></tr>
    <?php while ($s = $students->fetch_assoc()): ?>
    <tr>
      <td><?= h($s["name"]) ?></td>
      <td><?= h($s["roll_number"]) ?></td>
      <td><?= h($s["course"]) ?></td>
      <td><?= (int)$s["files"] ?></td>
      <td>
        <a class="btn" href="view.php?id=<?= $s["id"] ?>">View</a>
        <a class="btn grey" href="edit.php?id=<?= $s["id"] ?>">Edit</a>
        <form class="inline" method="post" action="delete.php"
              onsubmit="return confirm('Delete this student?');">
          <input type="hidden" name="id" value="<?= $s["id"] ?>">
          <button class="red" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php footer_html(); ?>
