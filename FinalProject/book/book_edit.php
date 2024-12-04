<?php
// Include database connection using the details from myproperties.ini
$ini = parse_ini_file('../core/myproperties.ini', true); // Ensure the path is correct
$host = $ini['database']['host'];
$user = $ini['database']['user'];
$password = $ini['database']['password'];
$dbname = $ini['database']['dbname'];

try {
    // Create the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the book ID is passed in the URL
if (isset($_GET['bookid'])) {
    $bookid = $_GET['bookid'];

    // Fetch the book's current details from the database
    $sql = "SELECT * FROM book WHERE bookid = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$bookid]);
    $book = $stmt->fetch();

    if (!$book) {
        echo "Book not found.";
        exit;
    }

    // Handle form submission to update book details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $active = $_POST['active']; // Capture the active status

        // Update the book details
        $update_sql = "UPDATE book SET title = ?, author = ?, publisher = ?, active = ?, last_updated = NOW() WHERE bookid = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$title, $author, $publisher, $active, $bookid]);

        // Redirect back to the book list after successful update
        header("Location: book.php");
        exit;
    }
} else {
    echo "No book ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
</head>
<body>
    <h1>Edit Book</h1>

    <form method="POST" action="book_edit.php?bookid=<?php echo $bookid; ?>">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required><br><br>

        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required><br><br>

        <label for="publisher">Publisher:</label><br>
        <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required><br><br>

        <label for="active">Status:</label><br>
        <select name="active" id="active">
            <option value="1" <?php if ($book['active'] == 1) echo "selected"; ?>>Active</option>
            <option value="0" <?php if ($book['active'] == 0) echo "selected"; ?>>Inactive</option>
        </select><br><br>

        <input type="submit" value="Update Book">
    </form>

    <br>
    <a href="book.php">Back to Book List</a>
</body>
</html>
