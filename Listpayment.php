
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>
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
<div class="item-form">
    <h2>Payment</h2>
    <form action="" method="post">
        <select name="fullname" required>
            <option value="">Select Fullname</option>
            <?php
            // Database connection
            include 'dbcreds.php';

            $conn = new mysqli($servername, $username_db, $password_db, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT fullname FROM ytulist";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['fullname']) . "'>" . htmlspecialchars($row['fullname']) . "</option>";
                }
            } else {
                echo "<option value=''>No users found</option>";
            }

            $conn->close();
            ?>
        </select></br>
        <input type="number" name="Recievable" placeholder="Utang" required></br>
        <input type="number" name="Reciev" placeholder="Payment" required></br>
        <input type="submit" value="Pay" name="payer"></br>
    </form>
    <br>
</div>

</body>
</html>

<?php
if (isset($_POST['payer'])) {
include 'dbcreds.php';
    $payer = $_POST['fullname'];
    $amt = $_POST['Reciev'];
    $ut = $_POST['Recievable'];

    $bal = $amt - $ut;

    // Connect to the database
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete all records for the given fullname
    $sql = "DELETE FROM ytulistitems WHERE fullname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $payer);
    $stmt->execute();

    if ($bal == 0) {
        // If balance is zero, do nothing after deleting records
        echo "Congratulations! You have fully paid your debt.";
    } elseif ($bal < 0) {
        // If balance is negative, insert a new record with balance
        $balance = abs($bal);
        $sql = "INSERT INTO ytulistitems (fullname, ytitems, itemamount) VALUES (?, 'Balance', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $payer, $balance);
        $stmt->execute();
        echo "All records for $payer have been deleted and a new balance of $balance record has been inserted.";
    } else {
        // If balance is positive, just show the change amount
        echo "Your change is $bal.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
