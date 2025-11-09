<?php
// It's good practice to have one file for database connection
$servername = "localhost";
$username = "root"; // Changed from 'your_db_username'
$password = ""; // Changed from 'your_db_password' (it's empty for default XAMPP)
$dbname = "student_portal"; // Changed from 'your_database_name' (use your actual DB name)

// Create connection
 $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Generates a unique metric ID for a new student.
 *
 * @param object $conn The database connection object.
 * @param string $sectionCode The section code (e.g., 'CS', 'IT').
 * @param int $sessionCode The session code (1, 2, or 3).
 * @param int $studyLevelCode The study level code (1 or 2).
 * @return string The generated unique metric ID.
 */
function generateMetricId($conn, $sectionCode, $sessionCode, $studyLevelCode) {
    // 1. Get the last two digits of the current year
    $yearPart = date("y");
    $fullYear = date("Y");

    // 2. Find the next serial number for this specific combination
    $sql = "SELECT COUNT(*) as count FROM students WHERE section_code = ? AND session_code = ? AND study_level_code = ? AND registration_year = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $sectionCode, $sessionCode, $studyLevelCode, $fullYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // The next serial number is the current count + 1
    $nextSerialNumber = $row['count'] + 1;
    
    // 3. Format the serial number to be 4 digits with leading zeros (e.g., 0001, 0045)
    $serialPart = sprintf('%04d', $nextSerialNumber);
    
    // 4. Combine all parts to create the final ID
    $metricId = strtoupper($sectionCode) . $sessionCode . $studyLevelCode . $yearPart . $serialPart;
    
    return $metricId;
}

?>