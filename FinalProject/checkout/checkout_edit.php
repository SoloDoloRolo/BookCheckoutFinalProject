<?php
// Include database connection settings
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update checkout information
    $checkoutid = $_POST['checkoutid'];
    $promise_date = $_POST['promise_date'];

    $stmt = $pdo->prepare("UPDATE checkout SET promise_date = :promise_date, last_updated = NOW() WHERE checkoutid = :checkoutid");
    $stmt->execute([':promise_date' => $promise_date, ':checkoutid' => $checkoutid]);

    header("Location: checkout.php"); // Redirect to checkout.php
    exit();
}

$checkoutid = $_GET['checkoutid'];
$stmt = $pdo->prepare("SELECT * FROM checkout WHERE checkoutid = :checkoutid");
$stmt->execute([':checkoutid' => $checkoutid]);
$checkout = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Checkout</title>
</head>
<body>
<a href="checkout.php"><button type="button">Back to Checkouts</button></a>
<h1>Edit Checkout</h1>

<form action="checkout_edit.php" method="post">
    <input type="hidden" name="checkoutid" value="<?= $checkout['checkoutid'] ?>">

    <label for="promise_date">Promise Date:</label>
    <input type="date" name="promise_date" id="promise_date" value="<?= $checkout['promise_date'] ?>" required><br><br>

    <button type="submit">Update Checkout</button>
</form>
</body>
</html>
