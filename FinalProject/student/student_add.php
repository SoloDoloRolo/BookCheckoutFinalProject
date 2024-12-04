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

$rocketid = $name = $phone = $address = "";
$rocketid_err = $name_err = $phone_err = $address_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty(trim($_POST["rocketid"]))) {
        $rocketid_err = "Please enter a RocketID.";
    } else {
        $rocketid = trim($_POST["rocketid"]);
    }

    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter an address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Insert into database if no errors
    if (empty($rocketid_err) && empty($name_err) && empty($phone_err) && empty($address_err)) {
        $insert_sql = "INSERT INTO student (rocketid, name, phone, address, active, create_dt, last_updated) 
                       VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssss", $rocketid, $name, $phone, $address);

        if ($stmt->execute()) {
            header("Location: student.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
</head>
<body>
    <!-- Back to Student List Button -->
    <a href="student.php">
        <button type="button">Back to Student List</button>
    </a>

    <h1>Add New Student</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="rocketid">RocketID</label>
            <input type="text" name="rocketid" id="rocketid" value="<?php echo $rocketid; ?>">
            <span><?php echo $rocketid_err; ?></span>
        </div>
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $name; ?>">
            <span><?php echo $name_err; ?></span>
        </div>
        <div>
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>">
            <span><?php echo $phone_err; ?></span>
        </div>
        <div>
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?php echo $address; ?>">
            <span><?php echo $address_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Add Student">
        </div>
    </form>
</body>
</html>

<?php
$conn->close();
?>
