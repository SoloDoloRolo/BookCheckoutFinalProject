<?php
// Include database connection settings
$settings = parse_ini_file('../core/myproperties.ini', true)['database'];
$pdo = new PDO("mysql:host={$settings['host']};dbname={$settings['dbname']}", $settings['user'], $settings['password']);

if (isset($_GET['checkoutid'])) {
    $checkoutid = $_GET['checkoutid'];

    // Update query to set return_date to NULL, marking as "Not Returned"
    $stmt = $pdo->prepare("
        UPDATE checkout 
        SET return_date = NULL 
        WHERE checkoutid = :checkoutid
    ");
    $stmt->execute([':checkoutid' => $checkoutid]);

    // Redirect back to the checkout page
    header("Location: checkout.php");
    exit;
} else {
    echo "Invalid request.";
    exit;
}
?>