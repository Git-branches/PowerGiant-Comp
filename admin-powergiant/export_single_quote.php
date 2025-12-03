<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';
requireLogin();

$database = new Database();
$db = $database->connect();

// Get quote ID
$quote_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$quote_id) {
    die("Invalid quote ID");
}

// Fetch quote details
$sql = "SELECT * FROM quote_requests WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $quote_id]);
$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    die("Quote not found");
}

// Generate filename with quote ID and timestamp
$filename = 'quote_' . $quote_id . '_' . date('Y-m-d_His') . '.csv';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for proper UTF-8 encoding in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write company header
fputcsv($output, ['QUOTE REQUEST DETAILS']);
fputcsv($output, ['Quote ID:', '#' . $quote['id']]);
fputcsv($output, ['Export Date:', date('F d, Y H:i:s')]);
fputcsv($output, []); // Empty line

// Company Information
fputcsv($output, ['COMPANY INFORMATION']);
fputcsv($output, ['Company Name:', $quote['company_name']]);
fputcsv($output, ['Industry:', $quote['industry']]);
fputcsv($output, []); // Empty line

// Contact Information
fputcsv($output, ['CONTACT INFORMATION']);
fputcsv($output, ['Contact Person:', $quote['contact_person']]);
fputcsv($output, ['Position:', $quote['position'] ?? 'N/A']);
fputcsv($output, ['Email:', $quote['email']]);
fputcsv($output, ['Phone:', $quote['phone']]);
fputcsv($output, []); // Empty line

// Order Details
fputcsv($output, ['ORDER DETAILS']);
fputcsv($output, ['Volume:', $quote['volume'] ?? 'N/A']);
fputcsv($output, ['Delivery Location:', $quote['delivery_location']]);
fputcsv($output, []); // Empty line

// Message
fputcsv($output, ['MESSAGE']);
fputcsv($output, [$quote['message'] ?? 'No message provided']);
fputcsv($output, []); // Empty line

// Status Information
fputcsv($output, ['STATUS INFORMATION']);
fputcsv($output, ['Status:', ucfirst(str_replace('_', ' ', $quote['status']))]);
fputcsv($output, ['Priority:', ucfirst($quote['priority'])]);
fputcsv($output, ['Admin Notes:', $quote['notes'] ?? 'No notes']);
fputcsv($output, []); // Empty line

// Attachment Information
if ($quote['attachment_original_name']) {
    fputcsv($output, ['ATTACHMENT']);
    fputcsv($output, ['File Name:', $quote['attachment_original_name']]);
    fputcsv($output, ['File Path:', $quote['attachment_path']]);
    fputcsv($output, []); // Empty line
}

// Technical Information
fputcsv($output, ['TECHNICAL INFORMATION']);
fputcsv($output, ['IP Address:', $quote['ip_address'] ?? 'N/A']);
fputcsv($output, ['User Agent:', $quote['user_agent'] ?? 'N/A']);
fputcsv($output, []); // Empty line

// Timestamps
fputcsv($output, ['TIMELINE']);
fputcsv($output, ['Created At:', date('M d, Y', strtotime($quote['created_at']))]);
fputcsv($output, ['Last Updated:', date('M d, Y', strtotime($quote['updated_at']))]);

// Close the output stream
fclose($output);
exit();
?>