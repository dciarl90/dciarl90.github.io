<?php
// submit.php - Process form submissions
header('Content-Type: text/plain');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    // Sanitize input
    $feedback = trim($_POST['feedback']);
    
    if (empty($feedback)) {
        echo "error:empty";
        exit;
    }
    
    // Sanitize for security
    $feedback = htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8');
    
    // Get additional info
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Prepare data array
    $submission = [
        'timestamp' => $timestamp,
        'feedback' => $feedback,
        'ip' => $ip,
        'user_agent' => $userAgent
    ];
    
    // Convert to JSON
    $jsonData = json_encode($submission, JSON_PRETTY_PRINT);
    
    // Define file paths
    $jsonFile = 'submissions.json';
    $csvFile = 'submissions.csv';
    
    // Save to JSON file (append)
    if (file_put_contents($jsonFile, $jsonData . PHP_EOL, FILE_APPEND) !== false) {
        // Also save to CSV for easy spreadsheet viewing
        $csvLine = [
            $timestamp,
            $feedback,
            $ip,
            str_replace(["\r", "\n", "\t"], ' ', $userAgent) // Remove line breaks for CSV
        ];
        
        // Create CSV file with headers if it doesn't exist
        if (!file_exists($csvFile)) {
            $csvHeader = ['Timestamp', 'Feedback', 'IP Address', 'User Agent'];
            file_put_contents($csvFile, implode(',', $csvHeader) . PHP_EOL);
        }
        
        // Escape CSV values
        $escapedLine = array_map(function($value) {
            // Escape quotes and wrap in quotes if contains comma or quote
            $value = str_replace('"', '""', $value);
            if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                $value = '"' . $value . '"';
            }
            return $value;
        }, $csvLine);
        
        file_put_contents($csvFile, implode(',', $escapedLine) . PHP_EOL, FILE_APPEND);
        
        echo "success";
    } else {
        echo "error:file_write";
    }
} else {
    echo "error:invalid_request";
}
?>