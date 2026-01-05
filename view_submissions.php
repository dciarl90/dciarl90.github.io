<?php
// view_submissions.php - View all submissions
$pageTitle = "Coffee Tour Submissions";
$jsonFile = 'submissions.json';
$csvFile = 'submissions.csv';

// Password protection (optional - remove or change password)
$protected = false; // Set to true to enable password protection
$correctPassword = 'coffee123'; // Change this!

if ($protected && !isset($_SESSION['authenticated'])) {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $correctPassword) {
            $_SESSION['authenticated'] = true;
        } else {
            $error = "Incorrect password!";
        }
    }
    
    if (!isset($_SESSION['authenticated'])) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login - <?php echo $pageTitle; ?></title>
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
                .login-box {
                    background-color: rgba(0, 0, 0, 0.3);
                    padding: 30px;
                    border-radius: 8px;
                    text-align: center;
                    width: 300px;
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
                    background-color: rgb(50, 80, 75);
                    color: rgb(177, 201, 195);
                    border: none;
                    padding: 10px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    width: 100%;
                }
                .error {
                    color: #ff6b6b;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>Admin Login</h2>
                <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Read submissions from JSON file
$submissions = [];
if (file_exists($jsonFile)) {
    $lines = file($jsonFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $submissions[] = $data;
        }
    }
}

// Sort by timestamp (newest first)
usort($submissions, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

$totalSubmissions = count($submissions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgb(5, 42, 41);
            color: rgb(177, 201, 195);
            line-height: 1.6;
        }
        
        h1 {
            color: rgb(177, 201, 195);
            text-align: center;
            margin-bottom: 10px;
        }
        
        .header-info {
            text-align: center;
            margin-bottom: 30px;
            color: rgb(150, 180, 175);
        }
        
        .controls {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .btn {
            background-color: rgb(50, 80, 75);
            color: rgb(177, 201, 195);
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: rgb(70, 100, 95);
        }
        
        .btn-danger {
            background-color: #8B0000;
        }
        
        .btn-danger:hover {
            background-color: #A00000;
        }
        
        .submission {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid rgb(50, 80, 75);
        }
        
        .submission-header {
            color: rgb(120, 150, 145);
            font-size: 0.9em;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .submission-feedback {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 15px;
            border-radius: 4px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .no-submissions {
            text-align: center;
            padding: 40px;
            color: rgb(120, 150, 145);
            font-style: italic;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .stat-box {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 8px;
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: rgb(177, 201, 195);
        }
        
        .stat-label {
            font-size: 0.9em;
            color: rgb(150, 180, 175);
        }
        
        @media (max-width: 600px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <h1>Sunday Morning Coffee Tour - Submissions</h1>
    
    <div class="header-info">
        <p>Total submissions: <?php echo $totalSubmissions; ?></p>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <div class="stat-number"><?php echo $totalSubmissions; ?></div>
            <div class="stat-label">Total Submissions</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">
                <?php 
                if ($totalSubmissions > 0) {
                    echo date('M j, Y', strtotime($submissions[0]['timestamp']));
                } else {
                    echo "N/A";
                }
                ?>
            </div>
            <div class="stat-label">Latest Submission</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">
                <?php 
                if ($totalSubmissions > 0) {
                    echo date('M j, Y', strtotime(end($submissions)['timestamp']));
                } else {
                    echo "N/A";
                }
                ?>
            </div>
            <div class="stat-label">First Submission</div>
        </div>
    </div>
    
    <div class="controls">
        <div>
            <a href="index.html" class="btn">Back to Feedback Form</a>
            <?php if (file_exists($csvFile)): ?>
                <a href="<?php echo $csvFile; ?>" class="btn" download>Download CSV</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($totalSubmissions > 0): ?>
                <a href="clear_submissions.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear ALL submissions? This cannot be undone!');">Clear All Submissions</a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($totalSubmissions === 0): ?>
        <div class="no-submissions">
            <h2>No submissions yet</h2>
            <p>No feedback has been submitted yet. Check back later!</p>
        </div>
    <?php else: ?>
        <?php foreach ($submissions as $index => $submission): ?>
            <div class="submission">
                <div class="submission-header">
                    <div>
                        <strong>#<?php echo $totalSubmissions - $index; ?></strong> | 
                        <?php echo date('F j, Y g:i A', strtotime($submission['timestamp'])); ?>
                    </div>
                    <div>
                        IP: <?php echo htmlspecialchars($submission['ip']); ?>
                    </div>
                </div>
                <div class="submission-feedback">
                    <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="controls" style="margin-top: 30px;">
        <a href="index.html" class="btn">Back to Feedback Form</a>
    </div>
</body>
</html>