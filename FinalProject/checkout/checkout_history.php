<?php
// Include database connection settings
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

$bookid = $_GET['bookid'];

// Fetch the book title
$book_stmt = $pdo->prepare("SELECT title FROM book WHERE bookid = :bookid");
$book_stmt->execute([':bookid' => $bookid]);
$book = $book_stmt->fetch(PDO::FETCH_ASSOC);
$book_title = $book ? $book['title'] : 'Unknown Book';

// Fetch checkout history for the selected book
$stmt = $pdo->prepare("
    SELECT c.rocketid, c.promise_date, c.return_date, s.name 
    FROM checkout c 
    JOIN student s ON c.rocketid = s.rocketid
    WHERE c.bookid = :bookid
    ORDER BY c.promise_date DESC
");
$stmt->execute([':bookid' => $bookid]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout History for <?= htmlspecialchars($book_title) ?></title>
</head>
<body>

<!-- Back to Checkouts Button -->
<a href="checkout.php">
    <button type="button">Back to Checkouts</button>
</a>

<h1>Checkout History for <?= htmlspecialchars($book_title) ?></h1>

<table border="1">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Promise Date</th>
            <th>Return Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($history as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['name']) ?></td>
                <td><?= htmlspecialchars($record['promise_date']) ?></td>
                <td><?= $record['return_date'] ? htmlspecialchars($record['return_date']) : 'Not Returned' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
