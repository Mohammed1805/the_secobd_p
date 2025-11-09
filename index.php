<?php
// Start the session to be able to use session variables
session_start();

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

// Include the database connection file
require_once "functions.php";

// Define variables and initialize with empty values
 $metric_id = $password = "";
 $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if metric_id is empty
    if (empty(trim($_POST["metric_id"]))) {
        $login_err = "Please enter your metric ID.";
    } else {
        $metric_id = trim($_POST["metric_id"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $login_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($login_err)) {
        // Prepare a select statement
        $sql = "SELECT id, metric_id, full_name, password_hash, role FROM students WHERE metric_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_metric_id);
            $param_metric_id = $metric_id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if metric_id exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $db_metric_id, $full_name, $hashed_password, $role);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_regenerate_id(true); // Prevents session fixation

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["metric_id"] = $db_metric_id;
                            $_SESSION["full_name"] = $full_name;
                            $_SESSION["role"] = $role;

                            // Redirect user to dashboard
                            header("location: dashboard.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid metric ID or password.";
                        }
                    }
                } else {
                    // Metric ID doesn't exist, display a generic error message
                    $login_err = "Invalid metric ID or password.";
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
    <title>Student Portal Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .wrapper { width: 360px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { width: 100%; padding: 10px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #0056b3; }
        .error { color: red; font-size: 0.9em; margin-bottom: 15px; text-align: center; }
        .register-link { text-align: center; margin-top: 20px; }
        .register-link a { color: #007BFF; text-decoration: none; }
        .forgot-link { text-align: center; margin-top: 10px; }
.forgot-link a { color: #6c757d; text-decoration: none; font-size: 0.9em; }
.forgot-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="error">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Metric ID</label>
                <input type="text" name="metric_id" value="<?php echo htmlspecialchars($metric_id); ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Login">
            </div>
            <!-- here the link to register -->
            <p class="register-link">Don't have an account? <a href="register.php">Sign up now</a>.</p>
<!-- here the link to recover your id -->
<p class="forgot-link"><a href="recover_id.php">Forgot your Metric ID?</a></p>


        </form>
    </div>
</body>
</html>