<?php
// clear_submissions.php - Clear all submissions (use with caution!)
session_start();

// Simple password check
$correctPassword = 'coffee123'; // Same as in view_submissions.php

if (!isset($_POST['confirm']) || $_POST['password'] !== $correctPassword) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clear Submissions - Confirmation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: rgb(5, 42, 41);
                color: rgb(177, 201, 195);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .confirmation-box {
                background-color: rgba(0, 0, 0, 0.3);
                padding: 30px;
                border-radius: 8px;
                text-align: center;
                width: 350px;
            }
            input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                background-color: rgb(10, 50, 48);
                border: 1px solid rgb(50, 80, 75);
                color: rgb(177, 201, 195);
                border-radius: 4px;
            }
            button {
                background-color: #8B0000;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                margin-top: 10px;
            }
            .warning {
                color: #ffcc00;
                margin-bottom: 15px;
                padding: 10px;
                background-color: rgba(255, 204, 0, 0.1);
                border-radius: 4px;
            }
            .btn-cancel {
                background-color: rgb(50, 80, 75);
                margin-top: 5px;
            }
        </style>
    </head>
    <body>
        <div class="confirmation-box">
            <h2>Clear All Submissions</h2>
            <div class="warning">
                <strong>WARNING:</strong> This will permanently delete ALL submissions!
            </div>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password to confirm" required>
                <input type="hidden" name="confirm" value="1">
                <button type="submit">PERMANENTLY DELETE ALL SUBMISSIONS</button>
                <a href="view_submissions.php"><button type="button" class="btn-cancel">Cancel</button></a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Clear the files
$jsonFile = 'submissions.json';
$csvFile = 'submissions.csv';

$success = true;
if (file_exists($jsonFile)) {
    if (!file_put_contents($jsonFile, '')) {
        $success = false;
    }
}

if (file_exists($csvFile)) {
    // Keep CSV header
    $csvHeader = "Timestamp,Feedback,IP Address,User Agent\n";
    if (!file_put_contents($csvFile, $csvHeader)) {
        $success = false;
    }
}

// Redirect back
header('Location: view_submissions.php?cleared=' . ($success ? '1' : '0'));
exit;
?>