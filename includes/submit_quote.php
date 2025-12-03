<?php
require_once '../config/database.php';
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    // Required fields (volume removed, service added)
    $required = ['company-name', 'industry', 'contact-name', 'email', 'phone', 'delivery-location', 'service'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Sanitize input
    $company_name = sanitize($_POST['company-name']);
    $industry = sanitize($_POST['industry']);
    $contact_person = sanitize($_POST['contact-name']);
    $position = sanitize($_POST['position'] ?? '');
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = sanitize($_POST['phone']);
    $service = sanitize($_POST['service']);                    // NEW: Required
    $delivery_location = sanitize($_POST['delivery-location']);
    $message = sanitize($_POST['message'] ?? '');
    
    if (!$email) {
        throw new Exception("Invalid email address");
    }
    
    // Handle file upload
    $attachment_path = null;
    $attachment_original_name = null;
    
    if (!empty($_FILES['attachment']['name'])) {
        $file = $_FILES['attachment'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $file['error']);
        }
        
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception("File size exceeds 10MB limit");
        }
        
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, ALLOWED_FILE_TYPES)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', ALLOWED_FILE_TYPES));
        }
        
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        $filename = uniqid('quote_') . '_' . time() . '.' . $file_ext;
        $upload_path = UPLOAD_DIR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $attachment_path = 'uploads/' . $filename;
            $attachment_original_name = $file['name'];
        } else {
            throw new Exception("Failed to save uploaded file");
        }
    }
    
    // INSERT query (volume removed, service added)
    $sql = "INSERT INTO quote_requests 
            (company_name, industry, contact_person, position, email, phone, 
             service, delivery_location, message, attachment_path, attachment_original_name,
             ip_address, user_agent) 
            VALUES 
            (:company_name, :industry, :contact_person, :position, :email, :phone,
             :service, :delivery_location, :message, :attachment_path, :attachment_original_name,
             :ip_address, :user_agent)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':company_name' => $company_name,
        ':industry' => $industry,
        ':contact_person' => $contact_person,
        ':position' => $position,
        ':email' => $email,
        ':phone' => $phone,
        ':service' => $service,
        ':delivery_location' => $delivery_location,
        ':message' => $message,
        ':attachment_path' => $attachment_path,
        ':attachment_original_name' => $attachment_original_name,
        ':ip_address' => getClientIP(),
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    $quote_id = $db->lastInsertId();
    
    // Log activity
    $log_sql = "INSERT INTO activity_logs (quote_id, action, description) 
                VALUES (:quote_id, 'created', 'New quote request submitted')";
    $log_stmt = $db->prepare($log_sql);
    $log_stmt->execute([':quote_id' => $quote_id]);
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Quote request submitted successfully!',
        'quote_id' => $quote_id
    ]);
    
} catch (Exception $e) {
    error_log("Quote Submission Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>