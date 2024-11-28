<?php
session_start();

$token = isset($_GET["token"]) ? $_GET["token"] : null;
$success_message = $_SESSION["success_message"] ?? null;
$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["success_message"], $_SESSION["errors"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body>
    <form action="reset-password-back.php" method="post">
        <h2>Reset Password</h2>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div>
            <input type="password" id="password" name="password" placeholder="New Password" required>
            <?php if (isset($errors["password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["password_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            <?php if (isset($errors["confirm_password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["confirm_password_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <?php if (isset($errors["token_error"])): ?>
            <span class="error-message">
                <?php echo $errors["token_error"]; ?>
            </span>
        <?php endif; ?>
        <button type="submit">Reset Password</button>
        <div>
            <a href="../login/login-front.php">Go back to login page</a>
        </div>
    </form>
    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>
</body>

</html>