<?php
session_start();

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

// Fetch student details if rocketid is set
if (isset($_GET['rocketid']) && !empty($_GET['rocketid'])) {
    $rocketid = $_GET['rocketid'];
    $sql = "SELECT * FROM student WHERE rocketid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rocketid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        die("Error: Student with RocketID $rocketid not found.");
    }
} else {
    header("Location: student.php");
    exit();
}

// Update student details on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Validate input (could be extended further)
    if (empty($name) || empty($phone) || empty($address)) {
        $error = "All fields are required.";
    } else {
        $update_sql = "UPDATE student SET name = ?, phone = ?, address = ?, last_updated = NOW() WHERE rocketid = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssss", $name, $phone, $address, $rocketid);

        if ($stmt->execute()) {
            header("Location: student.php");
            exit();
        } else {
            $error = "Error updating student: " . $stmt->error;
        }
    }
}

$conn->close();
?>

<!-- HTML Form for Editing the Student -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student: <?php echo htmlspecialchars($student['rocketid']); ?></h1>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form method="POST" action="student_edit.php?rocketid=<?php echo urlencode($student['rocketid']); ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($student['address']); ?>" required><br>

        <input type="submit" value="Save Changes">
    </form>
    <br>
    <a href="student.php">Back to Student List</a>
</body>
</html>
