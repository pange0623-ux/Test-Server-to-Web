<?php
// Define constants for security
define('MAX_LOG_SIZE', 5000); // Max log size in characters
define('LOG_DIR', 'mobslogs');   // Directory to store logs
define('ALLOWED_EXTENSIONS', ['txt']); // Allowed file extensions

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errorLog = $_POST['error_log'] ?? '';
    $mainTitle = $_POST['error_title'] ?? 'General_Error'; // Expecting "error_title" from the Java app

    // Step 1: Check if the log size is within acceptable limits
    if (strlen($errorLog) > MAX_LOG_SIZE) {
        http_response_code(413); // Payload Too Large
        echo "Error log is too large.";
        logInvalidAttempt("Payload too large", $errorLog);
        exit;
    }

    // Step 2: Validate the log content
    if (!isValidErrorLog($errorLog)) {
        http_response_code(400); // Bad Request
        echo "Invalid error log format.";
        logInvalidAttempt("Invalid format", $errorLog);
        exit;
    }

    // Step 3: Sanitize the main title to prevent malicious filenames
    $safeTitle = sanitizeTitle($mainTitle);

    // Enforce .txt file extension to avoid creating executable files
    $filename = LOG_DIR . "/{$safeTitle}_error_log.txt";

    // Step 4: Ensure the log directory exists
    if (!file_exists(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true); // Use 0755 permissions for better security
    }

    // Step 5: Append the log to the file
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp]\n$errorLog\n---------------------------------------\n";

    file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX); // Use LOCK_EX to prevent concurrent writes

    echo "Error log saved successfully.";
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}

// Function to validate the error log format
function isValidErrorLog($log) {
    // Check for required fields
    $requiredFields = [
        'Timestamp:',
        'Phone Model:',
        'Android Version:',
        'Thread:',
        'Stack Trace:'
    ];

    foreach ($requiredFields as $field) {
        if (strpos($log, $field) === false) {
            return false;
        }
    }

    return true;
}

// Function to sanitize the main title
function sanitizeTitle($title) {
    // Remove dangerous characters and replace spaces with underscores
    $title = preg_replace('/[^a-zA-Z0-9_\-]/', '', $title); // Allow only alphanumeric, underscores, and hyphens
    $title = str_replace(' ', '_', $title); // Replace spaces with underscores
    return $title ?: 'General_Error'; // Default to 'General_Error' if the title is empty
}

// Function to log invalid attempts
function logInvalidAttempt($reason, $data) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] Reason: $reason\n";
    $logEntry .= "Data: " . substr($data, 0, 500) . "\n"; // Truncate the data to avoid excessive logging
    $logEntry .= "---------------------------------------\n";

    // Ensure the invalid attempts log file is secure
    $invalidLogFile = LOG_DIR . '/invalid_attempts.log';
    file_put_contents($invalidLogFile, $logEntry, FILE_APPEND | LOCK_EX); // Use LOCK_EX to prevent concurrent writes
}
?>
