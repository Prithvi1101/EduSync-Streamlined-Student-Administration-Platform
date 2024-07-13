<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Notices</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        /* Hover effect for table rows */
        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            padding: 8px 16px;
            border: none;
            background-color: #f44336;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #d32f2f;
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
        <h1>Delete Notices</h1>
        
        <?php
        session_start();
        include 'DB_connection.php'; // Include your database connection file

        // Fetch all notices
        $sql_all_notices = "SELECT * FROM notices ORDER BY notice_date DESC";
        $result_all_notices = $con->query($sql_all_notices);

        if ($result_all_notices->num_rows > 0) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Notice ID</th>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_all_notices->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['notice_id']; ?></td>
                            <td><?php echo $row['notice_date']; ?></td>
                            <td><?php echo $row['notice_details']; ?></td>
                            <td>
                                <form action="delete_notice_process.php" method="post">
                                    <input type="hidden" name="notice_id" value="<?php echo $row['notice_id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this notice?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No notices available.</p>
        <?php endif; ?>

        <a href="admin_notice_board.php">Go back</a>
    </div>
</body>
</html>
