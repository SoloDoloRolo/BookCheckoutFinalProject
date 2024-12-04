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
$title = $author = $publisher = "";
$title_err = $author_err = $publisher_err = "";

// Processing the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate author
    if (empty(trim($_POST["author"]))) {
        $author_err = "Please enter an author.";
    } else {
        $author = trim($_POST["author"]);
    }

    // Validate publisher
    if (empty(trim($_POST["publisher"]))) {
        $publisher_err = "Please enter a publisher.";
    } else {
        $publisher = trim($_POST["publisher"]);
    }

    // Insert into database if no errors
    if (empty($title_err) && empty($author_err) && empty($publisher_err)) {
        $sql = "INSERT INTO book (title, author, publisher) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $title, $author, $publisher);
            if ($stmt->execute()) {
                // Redirect to book.php after successful insertion
                header("location: book.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
</head>
<body>
    <!-- Back to Manage Books Button -->
    <a href="book.php">
        <button type="button">Back to Manage Books</button>
    </a>

    <h1>Add New Book</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo $title; ?>">
            <span><?php echo $title_err; ?></span>
        </div>
        <div>
            <label for="author">Author</label>
            <input type="text" name="author" id="author" value="<?php echo $author; ?>">
            <span><?php echo $author_err; ?></span>
        </div>
        <div>
            <label for="publisher">Publisher</label>
            <input type="text" name="publisher" id="publisher" value="<?php echo $publisher; ?>">
            <span><?php echo $publisher_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Add Book">
        </div>
    </form>
</body>
</html>

<?php
$conn->close();
?>
