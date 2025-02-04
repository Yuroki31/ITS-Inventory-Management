<?php
include 'db_connect.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from form
    $username = $_POST['username'];
    $password = md5($_POST['pword']); // Ensure this matches your database encryption method (MD5 in your case)

    if (isset($_POST['submit'])) {
        // SQL Query to check user
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $con->prepare($sql);
        
        // Bind parameters (email and password)
        $stmt->bind_param("ss", $username, $password);
        
        // Execute query
        $stmt->execute();
        
        // Get the result of the query
        $result = $stmt->get_result();
        
        // Check if any matching record was found
        if ($result && $result->num_rows > 0) {
            session_start(); // Start the session
            
            // Fetch user data from the result
            $row = $result->fetch_assoc();
            
            // Store user information in the session (user_id is a better option to store than password)
            $_SESSION['user_id'] = $row['user_id']; // Store user_id for future references
            $_SESSION['username'] = $row['username']; // You can also store email
            
            // Redirect to dashboard or home page
            header("Location: index.php");
            exit();
        } else {
            // If no user is found with those credentials, show an error message
            echo "<script>alert('Incorrect email or password');</script>";
        }
        // Close statement
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css"> <!-- Ensure correct path to your CSS file -->
</head>
<body>

    <div class="imgcontainer-left">
        <img src="perps-logo.gif" alt="Left Avatar" class="avatar-left">
    </div>

    <video autoplay muted loop id="backgroundVideo">
        <source src="uphsl vid.mp4" type="video/mp4">
    </video>

    <div class="heading-container">
    <h1>UPHSL -  ITS Inventory System</h1>
    <div class="imgcontainer">
        <img src="assets/uploads/its logo.png" alt="Avatar" class="avatar">
    </div>
    </div>


    <!-- Login Form -->
    <form action="" method="post" class="login-form">
            <div class="form-box">
                <h2 class="form-heading">Login to Your Account</h2>

                <label for="username" class="form-label"></label>
                <input type="text" id="username" placeholder="Username" name="username" class="form-control" required autocomplete="username">

                <label for="pword" class="form-label"></label>
                <input type="password" id="pword" placeholder="Password" name="pword" class="form-control" required autocomplete="current-password"> 
                <br>
                <button type="submit" name="submit" class="btn btn-primary w-100 mt-3">Login</button>
            
                <div class="forgot-password">
                <br>
                    <center><a href="forgot_password.php" style="font-size: 16px;">Forgot Password?</a></center>
                </div>
            </div>
        </form>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 UPHSL - ITS Inventory System. All Rights Reserved.</p>
        <a href="https://uphsl.edu.ph" target="_blank" class="footer-link">Visit Website</a>
        <a href="https://www.facebook.com/uphslits" target="_blank" class="facebook-link">
            <img src="fbicon.png" alt="Facebook" class="facebook-icon">
        </a>
    </footer>

</body>
</html>