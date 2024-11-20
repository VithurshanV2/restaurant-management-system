<?php
session_start();

$errors = isset($_SESSION["errors"]) ? $_SESSION["errors"] : [];
$form_data = isset($_SESSION["form_data"]) ? $_SESSION["form_data"] : [];
unset($_SESSION["errors"], $_SESSION["form_data"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="/assets/css/customer signup.css">

</head>

<body>
    <h2>Sign Up</h2>
    <form action="customer-signup-back.php" method="post">
        <div>
            <input type="text" id="username" name="username" placeholder="username"
                value="<?php echo isset($form_data['username']) ? htmlspecialchars($form_data['username']) : ''; ?>"
                required>
            <?php if (isset($errors["username_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["username_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <input type="email" id="email" name="email" placeholder="email"
                value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>"
                required>
            <?php if (isset($errors["email_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["email_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="password"
                value="<?php echo isset($form_data['password']) ? htmlspecialchars($form_data['password']) : ''; ?>" required autocomplete="off">
            <?php if (isset($errors["password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["password_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="confirm password" required autocomplete="off" onpaste="return false;" oncopy="return false;" oncut="return false;">
            <?php if (isset($errors["confirm_password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["confirm_password_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <button type="submit">Sign Up</button>
        <p>
            <a href="../login/login-front.php">already have an account Sign In</a>
        </p>

        <p>
            <a href="employee-signup-front.php">Are you looking to sign up as an employee</a>
        </p>
    </form>
</body>

</html>