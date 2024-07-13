<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notices</title>
    <style>
        /* Basic page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .notice {
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 5px solid #4CAF50;
        }

        .date {
            font-size: 0.85em;
            color: #666;
            text-align: right;
            margin-top: 10px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }

        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Notices</h1>
        
        <?php
        session_start();
        include 'DB_connection.php'; // Include your database connection file

        // Fetch all notices
        $sql_all_notices = "SELECT * FROM notices ORDER BY notice_date DESC";
        $result_all_notices = $con->query($sql_all_notices);

        if ($result_all_notices->num_rows > 0) : ?>
            <ul>
                <?php while ($row = $result_all_notices->fetch_assoc()) : ?>
                    <li>
                        <div class="notice">
                            <p><?php echo $row['notice_details']; ?></p>
                            <p class="date"><?php echo $row['notice_date']; ?></p>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>No notices available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
