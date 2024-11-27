<?php
session_start();

$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body>
    <form action="forgot-password-back.php" method="post">
        <h2>Forgot Password</h2>
        <div>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <?php if (isset($errors["email_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["email_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <button type="submit">Send Reset Link</button>
        <div>
            <a href="../login/login-front.php">Go back to login page</a>
        </div>
    </form>
    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>

</html>