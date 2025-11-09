<?php
// Include the database connection file
require_once "functions.php";

 $metric_id = "";
 $email = "";
 $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate credentials
    if (empty($email_err)) {
        // Prepare a select statement
        $sql = "SELECT metric_id FROM students WHERE email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);
            $param_email = $email;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($metric_id);
                    if ($stmt->fetch()) {
                        // Email found, we have the ID. The ID is now in the $metric_id variable.
                    }
                } else {
                    // Email doesn't exist
                    $email_err = "No account found with that email address.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Metric ID</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .wrapper { width: 360px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #218838; }
        .error { color: red; font-size: 0.9em; margin-bottom: 15px; text-align: center; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #007BFF; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Recover Metric ID</h2>
        <p>Please enter your registered email address.</p>

        <?php
        if (!empty($email_err)) {
            echo '<div class="error">' . $email_err . '</div>';
        }
        
        // If the form was submitted and we have a metric_id, show it.
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($metric_id)) {
            echo '<div class="success">Your Metric ID is: <strong>' . htmlspecialchars($metric_id) . '</strong></div>';
        }
        ?>

        <!-- Only show the form if we haven't successfully retrieved the ID yet -->
        <?php if (empty($metric_id) || $_SERVER["REQUEST_METHOD"] != "POST"): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Recover My ID">
            </div>
        </form>
        <?php endif; ?>

        <p class="back-link"><a href="index.php">Back to Login</a></p>
    </div>
</body>
</html>