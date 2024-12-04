<?php
session_start();

// Load configuration settings
$config = parse_ini_file(dirname(__FILE__) . "/myproperties.ini");

if (!$config) {
    die("Error: Unable to load the configuration file.");
}

// Establish a database connection
$conn = new mysqli($config['host'], $config['user'], $config['password'], $config['dbname']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT * FROM user_authentication WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verify the password with the hash stored in the database
            if (password_verify($password, $row['passwordhash'])) {
                $_SESSION['username'] = $username;
                header("Location: /FinalProject/core/dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password. Please try again.";
            }
        } else {
            $error = "No such user found. Please try again.";
        }
    } else {
        $error = "Error preparing the statement.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</body>
</html>
