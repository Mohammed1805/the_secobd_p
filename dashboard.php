<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
        p { font-size: 1.2em; }
        .user-info { background-color: #f9f9f9; padding: 20px; border-radius: 8px; display: inline-block; }
        a { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="user-info">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["full_name"]); ?></b>. Welcome.</h1>
        <p>Your Metric ID is: <b><?php echo htmlspecialchars($_SESSION["metric_id"]); ?></b></p>
        <p>Your role is: <b><?php echo htmlspecialchars(ucfirst($_SESSION["role"])); ?></b></p>
        
        <!-- We will add different content here based on the user's role later -->
        
        <p>
            <a href="logout.php">Sign Out of Your Account</a>
        </p>
    </div>
</body>
</html>