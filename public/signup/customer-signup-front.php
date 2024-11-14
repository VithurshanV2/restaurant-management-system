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
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

h2 {
    margin-bottom: 20px;
    text-align: center;
    color: #333;
}

input[type="text"], input[type="email"], input[type="password"], input[type="tel"], select {
    width: calc(100% - 24px);
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 10px;
    background: #4CAF50;
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 16px;
}

button:hover {
    background: #45a049;
}

.error-message {
    color: red;
    font-size: 12px;
    margin-top: -10px;
    margin-bottom: 10px;
}

a {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #4CAF50;
}

a:hover {
    text-decoration: none;
    color: #45a049;
} 
    </style>
   
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
    </form>

    <p>
        <a href="../login/login-front.php">already have an account Sign In</a>
    </p>

    <p>
        <a href="employee-signup-front.php">Are you looking to sign up as an employee</a>
    </p>
</body>

</html>
