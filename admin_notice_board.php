<?php
session_start();
include 'DB_connection.php'; // Include your database connection file

// Fetch latest notice
$sql_latest_notice = "SELECT * FROM notices ORDER BY notice_id DESC";
$result_latest_notice = $con->query($sql_latest_notice);
$latest_notice = $result_latest_notice->fetch_assoc();

// Fetch all notices
$sql_all_notices = "SELECT * FROM notices ORDER BY notice_date DESC";
$result_all_notices = $con->query($sql_all_notices);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Notice Board</title>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
}

.notice-box {
    margin-bottom: 20px;
}

.notice {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

.notice p.date {
    font-size: 14px;
    color: #777;
}

.admin-options {
    margin-bottom: 20px;
}

.admin-options ul {
    list-style-type: none;
    padding: 0;
}

.admin-options li {
    margin-bottom: 10px;
}

.all-notices {
    border-top: 1px solid #ccc;
    padding-top: 20px;
}

.all-notices ul {
    list-style-type: none;
    padding: 0;
}

.all-notices li {
    margin-bottom: 10px;
}

	</style> 
</head>
<body>
    <div class="container">
        <h1>Admin Notice Board</h1>
        
        <!-- Display latest notice -->
        <div class="notice-box">
            <h2>Latest Notice</h2>
            <?php if ($latest_notice) : ?>
                <div class="notice">
                    <p><?php echo $latest_notice['notice_details']; ?></p>
                    <p class="date"><?php echo $latest_notice['notice_date']; ?></p>
                </div>
            <?php else : ?>
                <p>No notices available.</p>
            <?php endif; ?>
        </div>

        <!-- Admin options -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
            <div class="admin-options">
                <h2>Admin Options</h2>
                <ul>
                    <li><a href="add_notice.php">Add Notice</a></li>
                    <li><a href="delete_notice.php">Delete Notice</a></li>
                    <li><a href="view_all_notices.php">View All Notices</a></li>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Display all notices -->
        <div class="all-notices">
            <h2>All Notices</h2>
            <?php if ($result_all_notices->num_rows > 0) : ?>
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
    </div>
	<div class="go-back">
        <a href="admin_dashboard.php">Go back to Admin Dashboard</a>
    </div>
</div>
</body>
</html>
