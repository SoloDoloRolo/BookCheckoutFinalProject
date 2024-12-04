<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create the password hash
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Display the username and password hash
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password hash: " . htmlspecialchars($passwordHash) . "<br>";
}
?>

<!-- HTML form for entering the username and password -->
<form method="POST" action="generate_hash.php">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br>

    <input type="submit" value="Generate Hash">
</form>

