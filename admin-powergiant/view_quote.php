<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../config/auth.php';
requireLogin();

$database = new Database();
$db = $database->connect();

$quote_id = intval($_GET['id'] ?? 0);

if (!$quote_id) {
    echo '<div class="p-6 text-center bg-red-50 rounded-lg">
            <p class="text-red-700 font-medium">Invalid quote ID.</p>
          </div>';
    exit;
}

// Status and priority maps
$status_map = [
    'new' => ['text' => 'text-indigo-800', 'bg' => 'bg-indigo-100', 'border' => 'border-indigo-400', 'name' => 'New'],
    'in_progress' => ['text' => 'text-yellow-800', 'bg' => 'bg-yellow-100', 'border' => 'border-yellow-400', 'name' => 'In Progress'],
    'quoted' => ['text' => 'text-blue-800', 'bg' => 'bg-blue-100', 'border' => 'border-blue-400', 'name' => 'Quoted'],
    'completed' => ['text' => 'text-green-800', 'bg' => 'bg-green-100', 'border' => 'border-green-400', 'name' => 'Completed'],
    'cancelled' => ['text' => 'text-red-800', 'bg' => 'bg-red-100', 'border' => 'border-red-400', 'name' => 'Cancelled']
];

$priority_map = [
    'low' => ['text' => 'text-green-800', 'bg' => 'bg-green-100', 'border' => 'border-green-400', 'name' => 'Low'],
    'medium' => ['text' => 'text-orange-800', 'bg' => 'bg-orange-100', 'border' => 'border-orange-400', 'name' => 'Medium'],
    'high' => ['text' => 'text-red-800', 'bg' => 'bg-red-100', 'border' => 'border-red-400', 'name' => 'High'],
    'urgent' => ['text' => 'text-pink-800', 'bg' => 'bg-pink-100', 'border' => 'border-pink-400', 'name' => 'Urgent']
];

// Fetch quote details
try {
    $sql = "SELECT * FROM quote_requests WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $quote_id]);
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quote) {
        echo '<div class="p-6 text-center bg-red-50 rounded-lg">
                <p class="text-red-700 font-medium">Quote not found.</p>
              </div>';
        exit;
    }
    
    $current_status_data = $status_map[$quote['status']] ?? ['text' => 'text-gray-800', 'bg' => 'bg-gray-100', 'border' => 'border-gray-400', 'name' => 'Unknown'];
    $current_priority_data = $priority_map[$quote['priority']] ?? ['text' => 'text-gray-800', 'bg' => 'bg-gray-100', 'border' => 'border-gray-400', 'name' => 'Unknown'];

} catch (Exception $e) {
    echo '<div class="p-6 text-center bg-red-50 rounded-lg">
            <p class="text-red-700 font-medium">Error fetching quote: ' . htmlspecialchars($e->getMessage()) . '</p>
          </div>';
    exit;
}

// Helper function to check if file is an image
function isImageFile($filepath) {
    if (!$filepath) return false;
    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
}

// Helper function to get file icon
function getFileIcon($filepath) {
    if (!$filepath) return 'document';
    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    $icons = [
        'pdf' => '📄',
        'doc' => '📝',
        'docx' => '📝',
        'xls' => '📊',
        'xlsx' => '📊',
        'zip' => '🗜️',
        'rar' => '🗜️',
        'txt' => '📋',
        'csv' => '📊'
    ];
    return $icons[$ext] ?? '📎';
}

// Helper function to fix file path for viewing
function getViewablePath($filepath) {
    if (!$filepath) return '';
    if (preg_match('/^https?:\/\//', $filepath)) {
        return $filepath;
    }
    $filepath = preg_replace('/^(\.\.\/)+/', '', $filepath);
    $filepath = preg_replace('/^\.\//', '', $filepath);
    if (substr($filepath, 0, 1) !== '/' && !preg_match('/^\.\.\//', $filepath)) {
        $filepath = '../' . $filepath;
    }
    return $filepath;
}
?>
<link rel="icon" type="" href="POWER-GIANT.png">
<!-- Modal Content: Professional Quote Management Interface -->
<div class="space-y-6">
    <!-- Header Section with Export Button -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-wrap justify-between items-start gap-3">
            <div>
                <h2 class="text-2xl font-bold">Quote Request #<?php echo $quote_id; ?></h2>
                <p class="text-blue-100 mt-1">Detailed information and management</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <!-- Export Button -->
                <a href="export_single_quote.php?id=<?php echo $quote_id; ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition shadow-md text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>
                <!-- Status Badge -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-20 border border-white border-opacity-30">
                    <?php echo $current_status_data['name']; ?>
                </span>
                <!-- Priority Badge -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-20 border border-white border-opacity-30">
                    <?php echo $current_priority_data['name']; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Quote Details -->
        <div class="space-y-6">
            <!-- Quote Details Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Quote Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Client Information -->
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-700 text-sm uppercase tracking-wider border-b pb-2">Client Information</h4>
                            <div class="space-y-3">
                                <div class="flex">
                                    <div class="w-1/3 text-sm font-medium text-gray-500">Company</div>
                                    <div class="w-2/3 text-gray-900 font-medium"><?php echo htmlspecialchars($quote['company_name']); ?></div>
                                </div>
                                <div class="flex">
                                    <div class="w-1/3 text-sm font-medium text-gray-500">Industry</div>
                                    <div class="w-2/3 text-gray-900"><?php echo htmlspecialchars($quote['industry']); ?></div>
                                </div>
                                <div class="flex">
                                    <div class="w-1/3 text-sm font-medium text-gray-500">Contact</div>
                                    <div class="w-2/3 text-gray-900"><?php echo htmlspecialchars($quote['contact_person']); ?></div>
                                </div>
                                <div class="flex">
                                    <div class="w-1/3 text-sm font-medium text-gray-500">Position</div>
                                    <div class="w-2/3 text-gray-900"><?php echo htmlspecialchars($quote['position'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Project Information -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-700 text-sm uppercase tracking-wider border-b pb-2">Project Information</h4>
                                <div class="space-y-3">
                                    <div class="flex">
                                        <div class="w-1/3 text-sm font-medium text-gray-500">Delivery</div>
                                        <div class="w-2/3 text-gray-900"><?php echo htmlspecialchars($quote['delivery_location']); ?></div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-1/3 text-sm font-medium text-gray-500">Service</div>
                                        <div class="w-2/3 text-gray-900 font-bold text-lg">
                                            <?php 
                                            $service = $quote['service'] ?? 'N/A';
                                            if ($service !== 'N/A') {
                                                echo '<span class="text-blue-700">' . htmlspecialchars($service) . '</span>';
                                            } else {
                                                echo htmlspecialchars($service);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-1/3 text-sm font-medium text-gray-500">Status</div>
                                        <div class="w-2/3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $current_status_data['bg'] . ' ' . $current_status_data['text']; ?>">
                                                <?php echo $current_status_data['name']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <div class="w-1/3 text-sm font-medium text-gray-500">Priority</div>
                                        <div class="w-2/3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $current_priority_data['bg'] . ' ' . $current_priority_data['text']; ?>">
                                                <?php echo $current_priority_data['name']; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                    
                    <!-- Contact Details -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700 text-sm uppercase tracking-wider mb-4">Contact Details</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:<?php echo htmlspecialchars($quote['email']); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    <?php echo htmlspecialchars($quote['email']); ?>
                                </a>
                            </div>
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($quote['phone']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-700 text-sm uppercase tracking-wider mb-3">Timeline</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">Created</div>
                                    <div class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($quote['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">Last Updated</div>
                                    <div class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($quote['updated_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachment Section -->
            <?php if ($quote['attachment_path']): ?>
            <?php 
                $viewable_path = getViewablePath($quote['attachment_path']); 
                $is_image = isImageFile($quote['attachment_path']);
            ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        Attachment
                    </h3>
                </div>
                <div class="p-6">
                    <?php if ($is_image): ?>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="mb-4">
                                <img src="<?php echo htmlspecialchars($viewable_path); ?>" 
                                     alt="Attachment Preview" 
                                     class="max-w-full h-auto rounded-lg shadow-md cursor-pointer hover:opacity-90 transition"
                                     style="max-height: 300px; object-fit: contain;"
                                     onclick="openImageModal('<?php echo htmlspecialchars($viewable_path); ?>')">
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($quote['attachment_original_name'] ?? 'Image File'); ?>
                                </span>
                                <a href="<?php echo htmlspecialchars($viewable_path); ?>" 
                                   download="<?php echo htmlspecialchars($quote['attachment_original_name'] ?? 'attachment'); ?>"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <span class="text-4xl"><?php echo getFileIcon($quote['attachment_path']); ?></span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($quote['attachment_original_name'] ?? 'Attached File'); ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php 
                                            $ext = strtoupper(pathinfo($quote['attachment_path'], PATHINFO_EXTENSION));
                                            echo $ext . ' File';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="<?php echo htmlspecialchars($viewable_path); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <a href="<?php echo htmlspecialchars($viewable_path); ?>" 
                                       download="<?php echo htmlspecialchars($quote['attachment_original_name'] ?? 'attachment'); ?>"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Client Message and Update Form -->
        <div class="space-y-6">
            <!-- Client Message Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Client Message
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-sm text-gray-700 whitespace-pre-wrap">
                        <?php echo htmlspecialchars($quote['message'] ?? 'No message provided.'); ?>
                    </div>
                </div>
            </div>

            <!-- Update Quote Form -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Update Quote
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="request_quote.php" class="space-y-6">
                        <input type="hidden" name="update_quote" value="1">
                        <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>">
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <?php foreach ($status_map as $key => $data): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $quote['status'] === $key ? 'selected' : ''; ?>><?php echo $data['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select id="priority" name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <?php foreach ($priority_map as $key => $data): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $quote['priority'] === $key ? 'selected' : ''; ?>><?php echo $data['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                            <textarea id="notes" name="notes" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="Add internal notes here..."><?php echo htmlspecialchars($quote['notes'] ?? ''); ?></textarea>
                            <p class="mt-2 text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                These notes are for internal use only and won't be shared with the client.
                            </p>
                        </div>
                        
                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeModal('quoteModal')" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-md flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="max-w-4xl max-h-full p-4">
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Image Preview</h3>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <img id="imageModalContent" src="" alt="Preview" class="max-w-full max-h-[70vh] mx-auto">
            </div>
        </div>
    </div>
</div>

<script>
    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('imageModalContent');
        if (modal && img) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            img.src = imageSrc;
        }
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    
    // Close modal when clicking outside the image
    document.getElementById('imageModal')?.addEventListener('click', function(e) {
        if (e.target.id === 'imageModal') {
            closeImageModal();
        }
    });
</script>