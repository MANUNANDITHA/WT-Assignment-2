<?php
require "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)($_POST["id"] ?? 0);

    // remove the stored files from disk first
    $stmt = $conn->prepare("SELECT file_path FROM student_files WHERE student_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $files = $stmt->get_result();
    while ($f = $files->fetch_assoc()) {
        $path = __DIR__ . "/" . $f["file_path"];
        if (is_file($path)) { unlink($path); }
    }
    // metadata rows are removed by ON DELETE CASCADE
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: index.php?msg=" . urlencode("Student deleted."));
exit;
