<?php
// Include database connection settings
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

if (isset($_GET['checkoutid'])) {
    $checkoutid = $_GET['checkoutid'];

    // Retrieve current checkout information
    $stmt = $pdo->prepare("
        SELECT c.checkoutid, b.title, c.return_date
        FROM checkout c
        JOIN book b ON c.bookid = b.bookid
        WHERE c.checkoutid = :checkoutid
    ");
    $stmt->execute([':checkoutid' => $checkoutid]);
    $checkout = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$checkout) {
        echo "Checkout record not found.";
        exit;
    }

    $book_title = $checkout['title'];
} else {
    echo "Invalid request.";
    exit;
}

// Process form submission to update return_date
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_date'])) {
    $return_date = $_POST['return_date'];

    // Update the return_date for this checkout
    $update_stmt = $pdo->prepare("
        UPDATE checkout 
        SET return_date = :return_date
        WHERE checkoutid = :checkoutid
    ");
    $update_stmt->execute([
        ':return_date' => $return_date,
        ':checkoutid' => $checkoutid
    ]);

    // Redirect to checkout page
    header("Location: checkout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark as Returned - <?= htmlspecialchars($book_title) ?></title>
</head>
<body>

<h1>Mark as Returned</h1>

<p>Book Title: <?= htmlspecialchars($book_title) ?></p>

<form method="POST" action="checkout_return.php?checkoutid=<?= $checkoutid ?>">
    <label for="return_date">Return Date:</label>
    <input type="date" id="return_date" name="return_date" required>
    <button type="submit">Mark as Returned</button>
</form>

<!-- Back to Checkouts Button -->
<a href="checkout.php">
    <button type="button">Back to Checkouts</button>
</a>

</body>
</html>
