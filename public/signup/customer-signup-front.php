<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
</head>

<body>
    <h2>Sign Up</h2>
    <form action="customer-signup-back.php" method="post">
        <div>
            <input type="text" name="username" placeholder="username"
                value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>"
                required>
            <?php if (isset($_SESSION["errors"]["username_error"])): ?>
                <span class="error-message">
                    <?php echo $_SESSION["errors"]["username_error"]; ?>
                </span>
                <?php unset($_SESSION["errors"]["username_error"]); ?>
            <?php endif; ?>
        </div>
        <div>
            <input type="email" name="email" placeholder="email"
                value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>"
                required>
            <?php if (isset($_SESSION["errors"]["email_error"])): ?>
                <span class="error-message">
                    <?php echo $_SESSION["errors"]["email_error"]; ?>
                </span>
                <?php unset($_SESSION["errors"]["email_error"]); ?>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" name="password" placeholder="password"
                value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>" required>
            <?php if (isset($_SESSION["errors"]["password_error"])): ?>
                <span class="error-message">
                    <?php echo $_SESSION["errors"]["password_error"]; ?>
                </span>
                <?php unset($_SESSION["errors"]["password_error"]); ?>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" name="confirm_password" placeholder="confirm password" required>
            <?php if (isset($_SESSION["errors"]["confirm_password_error"])): ?>
                <span class="error-message">
                    <?php echo $_SESSION["errors"]["confirm_password_error"]; ?>
                </span>
                <?php unset($_SESSION["errors"]["confirm_password_error"]); ?>
            <?php endif; ?>
        </div>
        <button type="submit">Sign Up</button>
    </form>

    <p>
        <a href="employee-signup.html">Are you looking to sign up as an employee</a>
    </p>

    <?php
    if (empty($_SESSION["errors"])) {
        unset($_SESSION["form_data"]);
    }
    ?>
</body>

</html>