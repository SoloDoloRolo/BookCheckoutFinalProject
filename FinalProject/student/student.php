<?php
session_start();

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

// Sorting configuration
$sort_column = $_GET['sort'] ?? 'rocketid'; // Default sort column is 'rocketid'
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default sort order is 'ASC'
$next_order = $sort_order === 'ASC' ? 'DESC' : 'ASC'; // Toggle the order between 'ASC' and 'DESC'

$allowed_columns = ['rocketid', 'name', 'phone', 'address', 'active', 'create_dt', 'last_updated'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'rocketid'; // If the column is not allowed, default to 'rocketid'
}

$sql = "SELECT * FROM student ORDER BY $sort_column $sort_order";
$result = $conn->query($sql);

// Toggle active status
if (isset($_GET['toggle_active']) && isset($_GET['rocketid'])) {
    $rocketid = $_GET['rocketid'];
    $toggle_active = $_GET['toggle_active'] == '1' ? 1 : 0;

    $toggle_sql = "UPDATE student SET active = ? WHERE rocketid = ?";
    $stmt = $conn->prepare($toggle_sql);
    $stmt->bind_param("is", $toggle_active, $rocketid);
    $stmt->execute();
    header("Location: student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <a href="http://localhost/FinalProject/core/dashboard.php">
        <button type="button">Back to Dashboard</button>
    </a>

    <h1>Manage Students</h1>

    <!-- Add New Student Button -->
    <a href="student_add.php">
        <button type="button">Add New Student</button>
    </a>

    <table border="1">
        <thead>
            <tr>
                <th><a href="?sort=rocketid&order=<?php echo $next_order; ?>">RocketID</a></th>
                <th><a href="?sort=name&order=<?php echo $next_order; ?>">Name</a></th>
                <th><a href="?sort=phone&order=<?php echo $next_order; ?>">Phone</a></th>
                <th><a href="?sort=address&order=<?php echo $next_order; ?>">Address</a></th>
                <th><a href="?sort=active&order=<?php echo $next_order; ?>">Active</a></th>
                <th><a href="?sort=create_dt&order=<?php echo $next_order; ?>">Created Date</a></th>
                <th><a href="?sort=last_updated&order=<?php echo $next_order; ?>">Last Updated</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['rocketid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                    echo "<td>" . ($row['active'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . htmlspecialchars($row['create_dt']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_updated']) . "</td>";
                    echo "<td>
                            <a href='student_edit.php?rocketid=" . urlencode($row['rocketid']) . "'>Edit</a> |
                            <a href='student.php?toggle_active=" . ($row['active'] ? 0 : 1) . "&rocketid=" . urlencode($row['rocketid']) . "'>" 
                            . ($row['active'] ? 'Deactivate' : 'Reactivate') . "</a> |
                            <a href='student_delete.php?rocketid=" . urlencode($row['rocketid']) . "' onclick='return confirm(\"Are you sure you want to delete this student?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
