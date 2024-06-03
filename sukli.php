<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindahan Sukli</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
    <h1>Sukli</h1>
    <?php
    $host = ''; // Change this to your host if MySQL is not on localhost
    $dbname = ''; // Change this to your actual database name
    $username = ''; // Change this to your actual username
    $password = ''; // Change this to your actual password

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch full names from ytulistitems table
    $result = $conn->query("SELECT fullname FROM ytulist");
    $fullnames = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fullnames[] = $row['fullname'];
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fullname']) && isset($_POST['sukli'])) {
        $fullname = $_POST['fullname'];
        $sukli = $_POST['sukli'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO sukli (fullname, sukli) VALUES (?, ?)");
        $stmt->bind_param("si", $fullname, $sukli);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        // Prepare and bind
        $stmt = $conn->prepare("DELETE FROM sukli WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Fetch all data from sukli table
    $result = $conn->query("SELECT * FROM sukli");
    $sukliData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sukliData[] = $row;
        }
    }

    $conn->close();
    ?>

    <form action="" method="post">
        <label for="fullname">Full Name:</label><br>
        <select id="fullname" name="fullname" required>
            <?php foreach ($fullnames as $name): ?>
                <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
        </select><br><br>
        
        <label for="sukli">Sukli:</label><br>
        <input type="number" id="sukli" name="sukli" required><br><br>
        
        <input type="submit" value="Submit">
    </form>

    <h2>List of Sukli Entries</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Sukli</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php foreach ($sukliData as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['sukli']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form action="" method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="submit" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
