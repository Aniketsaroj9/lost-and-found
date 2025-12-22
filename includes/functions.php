<?php
// Common functions for the Lost & Found system

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function is_admin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

// Function to redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Function to display success/error messages
function display_message($type, $message) {
    $_SESSION['message'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'text' => $message
    ];
}

// Function to check if an item exists
function item_exists($item_id, $type = null) {
    global $pdo;
    
    $sql = "SELECT * FROM items WHERE item_id = :item_id";
    if ($type) {
        $sql .= " AND type = :type";
    }
    
    $stmt = $pdo->prepare($sql);
    $params = ['item_id' => $item_id];
    if ($type) {
        $params['type'] = $type;
    }
    
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to upload file
function upload_file($file, $target_dir = "../assets/uploads/") {
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if file already exists
    if (file_exists($target_file)) {
        $target_file = $target_dir . uniqid() . "." . $imageFileType;
    }
    
    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "Sorry, your file is too large."];
    }
    
    // Allow certain file formats
    $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];
    if (!in_array($imageFileType, $allowed_types)) {
        return ["success" => false, "message" => "Sorry, only JPG, JPEG, PNG, GIF & PDF files are allowed."];
    }
    
    // Try to upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            "success" => true, 
            "file_path" => $target_file,
            "file_name" => basename($target_file)
        ];
    } else {
        return ["success" => false, "message" => "Sorry, there was an error uploading your file."];
    }
}

// Function to log actions
function log_action($user_id, $action, $item_id = null) {
    global $pdo;
    
    $sql = "INSERT INTO activity_logs (user_id, action, item_id) VALUES (:user_id, :action, :item_id)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'user_id' => $user_id,
        'action' => $action,
        'item_id' => $item_id
    ]);
}
?>
