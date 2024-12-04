<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

echo "<h1>Welcome, {$_SESSION['username']}!</h1>";
?>
<p><a href="../student/student.php">Manage Students</a></p>
<p><a href="../book/book.php">Manage Books</a></p>
<p><a href="../checkout/checkout.php">Manage Checkouts</a></p>
<p><a href="logout.php">Logout</a></p>
