<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="forgot_password.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form action="reset_request.php" method="POST">
            <div class="input-container">
                <!-- Added placeholder inside the text input -->
                <input type="text" id="username" name="username" placeholder="Enter Admin Username" required>
            </div>
            <button type="submit">Request Reset</button>
        </form>
    </div>
</body>
</html>