<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database credentials
    $host = 'localhost';
    $db = 'client';
    $user = 'root';
    $pass = '';

    // Connect to database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Database connection failed: ' . $e->getMessage();
        exit;
    }

    // Prepare data
    $fullName = $_POST['full-name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phoneNumber = $_POST['phone-number'] ?? null;
    $subject = $_POST['subject'] ?? '';
    $budget = $_POST['budget'] ?? null;
    $message = $_POST['message'] ?? '';
    $filePath = null;

    // Validate server-side
    if (empty($fullName) || empty($email) || empty($subject)) {
        echo 'All required fields must be filled.';
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format.';
        exit;
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filePath = $uploadDir . basename($_FILES['file']['name']);
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            echo 'Failed to upload the file.';
            exit;
        }
    }

    // Insert into database
    $sql = "INSERT INTO contact_messages (full_name, email, phone_number, subject, budget, message, attachment_path)
            VALUES (:full_name, :email, :phone_number, :subject, :budget, :message, :attachment_path)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([
        ':full_name' => $fullName,
        ':email' => $email,
        ':phone_number' => $phoneNumber,
        ':subject' => $subject,
        ':budget' => $budget,
        ':message' => $message,
        ':attachment_path' => $filePath
    ])) {
        echo 'success';
    } else {
        echo 'Failed to save your message. Please try again.';
    }
}
?>
