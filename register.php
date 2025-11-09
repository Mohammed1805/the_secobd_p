<?php
// Include the database connection and the function
require_once 'functions.php';

 $metric_id = '';
 $error = '';
 $success = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. Get and Validate Inputs ---
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $section_code = trim($_POST['section_code']);
    $session_code = trim($_POST['session_code']);
    $study_level_code = trim($_POST['study_level_code']);
    
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // --- 2. Generate the Metric ID ---
        $metric_id = generateMetricId($conn, $section_code, $session_code, $study_level_code);
        
        // --- 3. Hash the Password for Security ---
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // --- 4. Insert into Database using Prepared Statements (to prevent SQL injection) ---
        $sql = "INSERT INTO students (metric_id, full_name, email, password_hash, section_code, session_code, study_level_code, registration_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $current_year = date("Y");
        $stmt->bind_param("ssssssii", $metric_id, $full_name, $email, $password_hash, $section_code, $session_code, $study_level_code, $current_year);
        
        if ($stmt->execute()) {
            $success = "Registration successful! Your new Student ID is: <strong>" . htmlspecialchars($metric_id) . "</strong>";
            // Clear form fields after successful submission
            $_POST = [];
        } else {
            // Check if it's a duplicate email error
            if ($conn->errno == 1062) {
                $error = "This email address is already registered.";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 40px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box; /* Important */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover { background-color: #0056b3; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Registration Portal</h2>

    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="register.php" method="post">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="section_code">College Section</label>
            <select id="section_code" name="section_code" required>
                <option value="">--Select Section--</option>
                <option value="CS" <?php echo (isset($_POST['section_code']) && $_POST['section_code']=='CS') ? 'selected' : ''; ?>>Computer Science</option>
                <option value="IT" <?php echo (isset($_POST['section_code']) && $_POST['section_code']=='IT') ? 'selected' : ''; ?>>Information Technology</option>
                <!-- Add more sections here if needed -->
            </select>
        </div>

        <div class="form-group">
            <label for="session_code">Session</label>
            <select id="session_code" name="session_code" required>
                <option value="">--Select Session--</option>
                <option value="1" <?php echo (isset($_POST['session_code']) && $_POST['session_code']=='1') ? 'selected' : ''; ?>>Morning</option>
                <option value="2" <?php echo (isset($_POST['session_code']) && $_POST['session_code']=='2') ? 'selected' : ''; ?>>Evening</option>
                <option value="3" <?php echo (isset($_POST['session_code']) && $_POST['session_code']=='3') ? 'selected' : ''; ?>>Parallel</option>
            </select>
        </div>

        <div class="form-group">
            <label for="study_level_code">Study Level</label>
            <select id="study_level_code" name="study_level_code" required>
                <option value="">--Select Level--</option>
                <option value="1" <?php echo (isset($_POST['study_level_code']) && $_POST['study_level_code']=='1') ? 'selected' : ''; ?>>Undergraduate</option>
                <option value="2" <?php echo (isset($_POST['study_level_code']) && $_POST['study_level_code']=='2') ? 'selected' : ''; ?>>Postgraduate</option>
            </select>
        </div>

        <button type="submit" class="btn">Register</button>

        <!-- this is the header to login  -->
           <p style="text-align: center; margin-top: 20px;">Already have an account? <a href="index.php">Login here</a>.</p>


    </form>
</div>

</body>
</html>