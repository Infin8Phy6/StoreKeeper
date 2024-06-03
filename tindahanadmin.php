<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan Admin</title>
    <style>
        /* Basic CSS for navigation bar */
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333;
        }
        li {
            float: left;
        }
        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }
        li a:hover {
            background-color: #111;
        }
    </style>
</head>
<body>
    <ul>
        <li><a href="tindahanadmin.php">Home</a></li>
        <li><a href="Listpayment.php">Payment</a></li>
        <li><a href="listaentry.php">Utang</a></li>
        <li><a href="sukli.php">Sukli</a></li>
        
    </ul>

    <div id="home">
        <h1>List Viewer</h1>

    <?php
    // Database credentials
    $host = ''; // Change this to your host if MySQL is not on localhost
    $dbname = ''; // Change this to your actual database name
    $username = ''; // Change this to your actual username
    $password = ''; // Change this to your actual password

    try {
        // Establish PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // Set PDO to throw exceptions on error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Set charset to UTF-8
        $pdo->exec("set names utf8");

        // Step 2: Retrieve Records Based on Selected Full Name
        if (isset($_POST['fullname'])) {
            $fullname = $_POST['fullname'];
            $sql = "SELECT * FROM ytulistitems WHERE fullname = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fullname]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Step 3: Calculate Total Item Amount
            $totalItemAmount = 0;
            foreach ($records as $record) {
                $totalItemAmount += $record['itemamount'];
            }
        
            // Display the records
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Full Name</th><th>YT Items</th><th>Item Amount</th><th>Created At</th></tr>";
            foreach ($records as $record) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($record['id']) . "</td>";
                echo "<td>" . htmlspecialchars($record['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($record['ytitems']) . "</td>";
                echo "<td>" . htmlspecialchars($record['itemamount']) . "</td>";
                echo "<td>" . htmlspecialchars($record['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        
            // Display Total Item Amount
            echo "<p>Total Item Amount: $totalItemAmount</p>";
        }

        // Step 6: Process Payment
        if(isset($_POST['payment'])) {
            $payment = $_POST['payment'];

            // Calculate remaining amount
            $remainingAmount = $totalItemAmount - $payment;

            // Step 7: Handle Remaining Amount
            if($remainingAmount == 0) {
                // Delete all records for that person
                $sql = "DELETE FROM ytulistitems WHERE fullname = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname]);
            } elseif($remainingAmount < 0) {
                // Delete previous records for that person
                $sql = "DELETE FROM ytulistitems WHERE fullname = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname]);

                // Insert new record with balance
                $balance = abs($remainingAmount);
                $sql = "INSERT INTO ytulistitems (fullname, ytitems, itemamount) VALUES (?, 'Balance', ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $balance]);
            }
        }
    } catch(PDOException $e) {
        // Print error message if connection fails
        die("Connection failed: " . $e->getMessage());
    }
    ?>

    <form method="post">
        <label for="fullname">Select Full Name:</label>
        <select name="fullname" id="fullname">
            <!-- Populate select options with full names from the database -->
            <?php
            // Assuming $pdo is your PDO object for database connection
            $sql = "SELECT DISTINCT fullname FROM ytulistitems";
            $stmt = $pdo->query($sql);
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['fullname']}'>{$row['fullname']}</option>";
            }
            ?>
        </select>
        <button type="submit">Fetch Records</button>
    </form>
        
    </div>

</body>
</html>


<?php
include 'dbcreds.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select all data from ytulistitems
$sql = "SELECT id, fullname, ytitems, itemamount, created_at FROM ytulistitems";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start the table and header row
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>YT Items</th>
                <th>Item Amount</th>
                <th>Created At</th>
            </tr>";

    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . $row["fullname"] . "</td>
                <td>" . $row["ytitems"] . "</td>
                <td>" . $row["itemamount"] . "</td>
                <td>" . $row["created_at"] . "</td>
              </tr>";

              
    }

    // End the table
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
<?php
// Database credentials
include 'dbcreds.php';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to calculate the sum of itemamount
$sql = "SELECT SUM(CAST(itemamount AS DECIMAL(10, 2))) AS total_itemamount FROM ytulistitems";

// Execute the query
$result = $conn->query($sql);

if ($result) {
    // Fetch the result
    $row = $result->fetch_assoc();
    $total_itemamount = $row['total_itemamount'];

    // Display the result
    echo "</br>";
    echo "The total item recievable amount is: " . htmlspecialchars($total_itemamount);
} else {
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>




