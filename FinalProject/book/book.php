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

// Default sorting parameters
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'title';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

// Switch sorting order if same column is clicked again
if ($sort_order == 'ASC') {
    $next_sort_order = 'DESC';
} else {
    $next_sort_order = 'ASC';
}

// Fetch books from the database with dynamic sorting
$sql = "SELECT * FROM book ORDER BY $sort_column $sort_order"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <a href="../core/dashboard.php">
        <button type="button">Back to Dashboard</button>
    </a>

    <h1>Manage Books</h1>

    <!-- Add New Book Button -->
    <a href="book_add.php">
        <button type="button">Add New Book</button>
    </a>

    <table border="1">
        <thead>
            <tr>
                <th><a href="book.php?sort_column=bookid&sort_order=<?php echo $next_sort_order; ?>">Book ID</a></th>
                <th><a href="book.php?sort_column=title&sort_order=<?php echo $next_sort_order; ?>">Title</a></th>
                <th><a href="book.php?sort_column=author&sort_order=<?php echo $next_sort_order; ?>">Author</a></th>
                <th><a href="book.php?sort_column=publisher&sort_order=<?php echo $next_sort_order; ?>">Publisher</a></th>
                <th><a href="book.php?sort_column=active&sort_order=<?php echo $next_sort_order; ?>">Active</a></th>
                <th><a href="book.php?sort_column=create_dt&sort_order=<?php echo $next_sort_order; ?>">Created Date</a></th>
                <th><a href="book.php?sort_column=last_updated&sort_order=<?php echo $next_sort_order; ?>">Last Updated</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bookid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['publisher']) . "</td>";
                    echo "<td>" . ($row['active'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . htmlspecialchars($row['create_dt']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_updated']) . "</td>";
                    echo "<td>
                            <a href='book_edit.php?bookid=" . urlencode($row['bookid']) . "'>Edit</a> |
                            <a href='book_delete.php?bookid=" . urlencode($row['bookid']) . "' onclick='return confirm(\"Are you sure you want to delete this book?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No books found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
