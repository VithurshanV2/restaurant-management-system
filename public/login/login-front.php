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
    <title>Login Page</title>
    <style>
     body { 
         font-family: Arial, sans-serif; 
         background-color: #f9f9f9; 
         display: flex; 
         justify-content: center; 
         align-items: center; 
         height: 100vh; 
         margin: 0; 
     } 
        h2 { 
            text-align: center; 
            color: #333; 
        }
        form { 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            width: 300px; 
        }
        div { 
            margin-bottom: 15px; 
        } 
        input[type="text"], 
        input[type="password"]  { 
            width: 100%; 
            padding: 10px; 
            box-sizing: border-box; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            margin-top: 5px;
        } 
        button { 
            width: 100%; 
            padding: 10px; 
            border: none; 
            background-color: #4CAF50; 
            color: white; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 16px; 
        } 
        button:hover { 
            background-color: #45a049; 
        }
        .error-message { 
            color: red; 
            font-size: 14px; 
        } 
        p { 
            text-align: center;
            margin-top: 20px; 
        } 
        a { 
            color: #007BFF; 
            text-decoration: none; 
        } 
        a:hover { 
        text-decoration: underline; 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }    
    </style>
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
        <button type="submit">Login</button>
    </form>

    <p>
        <a href="../signup/customer-signup-front.php">Don't have an account Sign Up</a>
    </p>

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
