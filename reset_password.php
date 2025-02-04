<?php
require 'db_connect.php';

if (isset($_GET['key'])) {
    $resetKey = $_GET['key'];

    // Validate the reset key (in this example, it's static)
    if ($resetKey === "STATIC_RESET_KEY") {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Set New Password</title>
            <link rel="stylesheet" href="reset_password.css">
        </head>
        <body>
            <div class="container">
                <h2>Set New Password</h2>
                <form action="reset_password_action.php" method="POST">
                    <input type="hidden" name="key" value="<?php echo htmlspecialchars($resetKey); ?>">
                    <div class="input-container">
                        <label for="username"></label>
                        <input type="text" id="username" name="username" placeholder="Enter Admin Username" required>
                    </div>
                    <div class="input-container">
                        <label for="password"></label>
                        <input type="password" id="password" name="password" placeholder="Enter New Password" required>
                    </div>
                    <button type="submit">Reset Password</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid reset key.";
    }
} else {
    echo "No reset key provided.";
}
?>