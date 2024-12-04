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

// Check if bookid is provided
if (isset($_GET['bookid']) && !empty($_GET['bookid'])) {
    $bookid = $_GET['bookid'];

    // Delete the book record
    $sql = "DELETE FROM book WHERE bookid = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $bookid);
        if ($stmt->execute()) {
            header("location: book.php"); // Redirect to book list after deletion
            exit();
        } else {
            echo "Error: Could not delete the book. Please try again later.";
        }
    }
} else {
    echo "Invalid request. Book ID not provided.";
}

$conn->close();
?>
