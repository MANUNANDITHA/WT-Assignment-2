<?php
// Database connection and shared helper functions
$conn = new mysqli("localhost", "root", "", "student_admin");
if ($conn->connect_error) {
    die("Database connection failed.");
}
$conn->set_charset("utf8mb4");

define("UPLOAD_DIR", __DIR__ . "/uploads/");
define("MAX_SIZE", 2 * 1024 * 1024);          // 2 MB
const ALLOWED = [
    "image/jpeg"      => "jpg",
    "image/png"       => "png",
    "application/pdf" => "pdf"
];

function h($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, "UTF-8");
}

// Validates one uploaded file, stores it under a unique name and
// saves its metadata. Returns an error message or "" on success.
function saveFile($conn, $studentId, $field, $docType) {
    if (!isset($_FILES[$field]) || $_FILES[$field]["error"] == UPLOAD_ERR_NO_FILE) {
        return "";
    }
    $file = $_FILES[$field];
    if ($file["error"] != UPLOAD_ERR_OK) {
        return "$docType: upload failed.";
    }
    if ($file["size"] > MAX_SIZE) {
        return "$docType: file must be 2 MB or smaller.";
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file["tmp_name"]);
    if (!isset(ALLOWED[$mime])) {
        return "$docType: only JPG, PNG and PDF files are allowed.";
    }
    // unique name -> an existing file can never be overwritten
    $stored = uniqid("f_", true) . "." . ALLOWED[$mime];
    if (!move_uploaded_file($file["tmp_name"], UPLOAD_DIR . $stored)) {
        return "$docType: could not store the file.";
    }
    $stmt = $conn->prepare("INSERT INTO student_files
        (student_id, doc_type, original_name, file_path, file_type)
        VALUES (?, ?, ?, ?, ?)");
    $path = "uploads/" . $stored;
    $orig = basename($file["name"]);
    $stmt->bind_param("issss", $studentId, $docType, $orig, $path, $mime);
    $stmt->execute();
    return "";
}

function header_html($title) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<h1>Student Admin Panel</h1>
<?php }
function footer_html() { echo "</div></body></html>"; }
