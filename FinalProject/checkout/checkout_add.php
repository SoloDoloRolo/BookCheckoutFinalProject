<?php
// Database connection
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

// Fetch books and students for dropdowns
$books = $pdo->query("SELECT bookid, title FROM book WHERE active = 1")->fetchAll(PDO::FETCH_ASSOC);
$students = $pdo->query("SELECT rocketid, name FROM student WHERE active = 1")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookid = $_POST['bookid'];
    $rocketid = $_POST['rocketid'];
    $promise_date = $_POST['promise_date'];

    $stmt = $pdo->prepare("INSERT INTO checkout (bookid, rocketid, promise_date) VALUES (:bookid, :rocketid, :promise_date)");
    $stmt->execute([
        ':bookid' => $bookid,
        ':rocketid' => $rocketid,
        ':promise_date' => $promise_date
    ]);

    header("Location: checkout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Checkout</title>
</head>
<body>

<!-- Back to Checkouts Button -->
<a href="checkout.php"><button type="button">Back to Checkouts</button></a>
<h1>Add New Checkout</h1>

<form action="checkout_add.php" method="post">
    <label for="bookid">Select Book:</label>
    <select name="bookid" id="bookid" required>
        <?php foreach ($books as $book): ?>
            <option value="<?= $book['bookid'] ?>"><?= htmlspecialchars($book['title']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="rocketid">Select Student:</label>
    <select name="rocketid" id="rocketid" required>
        <?php foreach ($students as $student): ?>
            <option value="<?= $student['rocketid'] ?>"><?= htmlspecialchars($student['name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="promise_date">Promise Date:</label>
    <input type="date" name="promise_date" id="promise_date" required><br><br>

    <button type="submit">Add Checkout</button>
</form>

</body>
</html>
