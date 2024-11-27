<?php
session_start();

$success_message = $_SESSION["success_message"] ?? null;
$errors = isset($_SESSION["errors"]) ? $_SESSION["errors"] : [];
$form_data = isset($_SESSION["form_data"]) ? $_SESSION["form_data"] : [];
unset($_SESSION["success_message"], $_SESSION["errors"], $_SESSION["form_data"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="/assets/css/login.css">

</head>

<body>
    <form action="login-back.php" method="post">
        <h2>Login</h2>
        <div>
            <input type="text" id="username_email" name="username_email" placeholder="username/email" value="<?php echo isset($form_data['username_email']) ? htmlspecialchars($form_data['username_email']) : ''; ?>" required>
            <?php if (isset($errors["username_email_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["username_email_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="password" value="<?php echo isset($form_data['password']) ? htmlspecialchars($form_data['password']) : ''; ?>" required>
            <?php if (isset($errors["password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["password_error"]; ?>
                </span>
            <?php endif; ?>
            <input type="checkbox" id="show_password" onclick="Toggle()">
            <strong>Show password</strong>
        </div>
        <div>
            <a href="../password/forgot-password-front.php">Forgot password?</a>
        </div>
        <button type="submit">Login</button>
        <p>
            <a href="../signup/customer-signup-front.php">Don't have an account Sign Up</a>
        </p>
    </form>

    <?php if ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($errors["email_error"])): ?>
        <span class="error-message">
            <?php echo $errors["email_error"]; ?>
        </span>
    <?php endif; ?>

    <script>
        function Toggle() {
            let temp = document.getElementById("password");

            if (temp.type === "password") {
                temp.type = "text";
            } else {
                temp.type = "password";
            }
        }
    </script>
</body>

</html>