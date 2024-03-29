<?php
session_start(); // Start session at the beginning

if(isset($_POST['login_but'])) {
    require '../../helpers/init_conn_db.php';

    // Prepare SQL statement
    $sql = 'SELECT * FROM `admin` WHERE admin_email = ? OR admin_uname = ?';
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header('Location: ../../admin/login.php?error=sqlerror');
        exit();
    } else {
        $email_id = $_POST['user_id'];
        $password = $_POST['user_pass'];

        // Bind parameters and execute statement
        mysqli_stmt_bind_param($stmt, 'ss', $email_id, $email_id);
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);

        // Debug: Print out the number of rows fetched
        $num_rows = mysqli_num_rows($result);
        echo "Number of rows fetched: $num_rows<br>";

        // Check if there is a matching row
        while($row = mysqli_fetch_assoc($result)) {
            // Debug: Print out the fetched row
            print_r($row);

            // Verify password (using MD5 hashing)
            if(md5($password) === $row['admin_pwd']) {
                // Password correct, set session variables
                $_SESSION['adminId'] = $row['admin_id'];
                $_SESSION['adminUname'] = $row['admin_uname'];
                $_SESSION['adminEmail'] = $row['admin_email'];
                header('Location: ../../admin/index.php?login=success');
                exit();
            }
        }

        // If the loop finishes without successful login, it means invalid credentials
        header('Location: ../../admin/login.php?error=invalidcred');
        exit();
    }

    // Close statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    // Redirect to index.php if login button not clicked
    header('Location: ../../index.php');
    exit();
}
?>
