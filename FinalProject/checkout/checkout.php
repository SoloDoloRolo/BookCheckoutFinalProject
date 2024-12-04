<?php
// Database connection
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

// Sorting functionality
$sort_column = $_GET['sort'] ?? 'title';
$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$order_text = $order === 'asc' ? 'desc' : 'asc';

// Fetch checkout data
$query = "
    SELECT c.checkoutid, c.bookid, c.rocketid, c.promise_date, c.return_date, b.title, s.name 
    FROM checkout c 
    JOIN book b ON c.bookid = b.bookid
    JOIN student s ON c.rocketid = s.rocketid
    ORDER BY $sort_column $order
";
$stmt = $pdo->query($query);
$checkouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Records</title>
</head>
<body>

<!-- Back to Dashboard Button -->
<a href="../core/dashboard.php"><button type="button">Back to Dashboard</button></a>

<h1>Manage Checkout Records</h1>
<a href="checkout_add.php"><button type="button">Add Checkout</button></a>

<table border="1">
    <thead>
        <tr>
            <th><a href="checkout.php?sort=title&order=<?= $order_text ?>">Book Title</a></th>
            <th><a href="checkout.php?sort=name&order=<?= $order_text ?>">Student Name</a></th>
            <th><a href="checkout.php?sort=promise_date&order=<?= $order_text ?>">Promise Date</a></th>
            <th><a href="checkout.php?sort=return_date&order=<?= $order_text ?>">Return Date</a></th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($checkouts as $checkout): ?>
            <tr>
                <td><?= htmlspecialchars($checkout['title']) ?></td>
                <td><?= htmlspecialchars($checkout['name']) ?></td>
                <td><?= htmlspecialchars($checkout['promise_date']) ?></td>
                <td><?= $checkout['return_date'] ? htmlspecialchars($checkout['return_date']) : "Not Returned" ?></td>
                <td>
                    <?php if (!$checkout['return_date']): ?>
                        <a href="checkout_return.php?checkoutid=<?= $checkout['checkoutid'] ?>">Mark as Returned</a> |
                    <?php else: ?>
                        <a href="checkout_unreturn.php?checkoutid=<?= $checkout['checkoutid'] ?>">Not Returned?</a> |
                    <?php endif; ?>
                    <a href="checkout_edit.php?checkoutid=<?= $checkout['checkoutid'] ?>">Edit</a> |
                    <a href="checkout_delete.php?checkoutid=<?= $checkout['checkoutid'] ?>" onclick="return confirm('Are you sure?');">Delete</a> |
                    <a href="checkout_history.php?bookid=<?= $checkout['bookid'] ?>">View History</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
