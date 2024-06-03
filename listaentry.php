<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
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

<div class="item-form">
    <h2>Add Item</h2>
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
        </select>
        <input type="text" name="ytitems" placeholder="Item" required>
        <input type="number" name="itemamount" placeholder="Amount" required min="0">
        <button type="submit">Add Item</button>
    </form>
</div>

</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars(strip_tags(trim($_POST['fullname'])));
    $ytitems = htmlspecialchars(strip_tags(trim($_POST['ytitems'])));
    $itemamount = intval($_POST['itemamount']);

    // Database connection
    include 'dbcreds.php';

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO ytulistitems (fullname, ytitems, itemamount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $ytitems, $itemamount);

    if ($stmt->execute()) {
        //echo "Item added successfully.";
        unset($fullname, $ytitems, $itemamount);
        header("refresh:2");

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
