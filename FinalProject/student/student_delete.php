<?php
session_start();

// Database connection setup
$config = parse_ini_file(dirname(__FILE__) . "/../core/myproperties.ini");
if (!$config) {
    die("Error: Unable to load the configuration file.");
}

$dbhost = $config['host'];
$dbuser = $config['user'];
$dbpass = $config['password'];
$dbname = $config['dbname'];

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$rocketid = $name = $phone = $address = "";
$rocketid_err = "";

// Check if the student ID is provided
if (isset($_GET["rocketid"])) {
    $rocketid = $_GET["rocketid"];

    // Prepare a delete statement
    $sql = "DELETE FROM student WHERE rocketid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $rocketid);

        if ($stmt->execute()) {
            // Redirect to the student management page after successful deletion
            header("location: student.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
} else {
    echo "No student ID provided.";
}

$conn->close();
?>
