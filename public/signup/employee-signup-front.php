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

</head>

<body>
    <h2>Sign Up</h2>
    <form action="employee-signup-back.php" method="post">
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
            <input type="text" name="first_name" placeholder="first name"
                value="<?php echo isset($form_data['first_name']) ? htmlspecialchars($form_data['first_name']) : ''; ?>"
                required>
            <?php if (isset($errors["first_name_error"])): ?>
                <span class="error-message"><?php echo $errors["first_name_error"]; ?></span>
            <?php endif; ?>
        </div>
        <div>
            <input type="text" name="last_name" placeholder="last name"
                value="<?php echo isset($form_data['last_name']) ? htmlspecialchars($form_data['last_name']) : ''; ?>"
                required>
            <?php if (isset($errors["last_name_error"])): ?>
                <span class="error-message"><?php echo $errors["last_name_error"]; ?></span>
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
            <input type="password" id="confirm_password" name="confirm_password" placeholder="confirm password"
                value="<?php echo isset($form_data['confirm_password']) ? htmlspecialchars($form_data['confirm_password']) : ''; ?>" required autocomplete="off" onpaste="return false;" oncopy="return false;" oncut="return false;">
            <?php if (isset($errors["confirm_password_error"])): ?>
                <span class="error-message">
                    <?php echo $errors["confirm_password_error"]; ?>
                </span>
            <?php endif; ?>
        </div>
        <div>
            <label for="job_role">Job role</label>
            <select name="job_role" id="job_role" required>
                <option value="staff" <?php echo (isset($form_data['job_role']) && $form_data['job_role'] === 'staff') ? 'selected' : ''; ?>>Staff</option>
                <option value="chef" <?php echo (isset($form_data['job_role']) && $form_data['job_role'] === 'chef') ? 'selected' : ''; ?>>Chef</option>
                <option value="manager" <?php echo (isset($form_data['job_role']) && $form_data['job_role'] === 'manager') ? 'selected' : ''; ?>>Manager</option>
            </select>
        </div>
        <div>
            <input type="text" name="employee_id" placeholder="employee ID"
                value="<?php echo isset($form_data['employee_id']) ? htmlspecialchars($form_data['employee_id']) : ''; ?>" required>
            <?php if (isset($errors["employee_id_error"])): ?>
                <span class="error-message"><?php echo $errors["employee_id_error"]; ?></span>
            <?php endif; ?>
        </div>
        <div>
            <input type="tel" name="contact_number" placeholder="contact number"
                value="<?php echo isset($form_data['contact_number']) ? htmlspecialchars($form_data['contact_number']) : ''; ?>" required
                pattern="\d{10}" title="Please enter a valid contact number">
            <?php if (isset($errors["contact_number_error"])): ?>
                <span class="error-message"><?php echo $errors["contact_number_error"]; ?></span>
            <?php endif; ?>
        </div>
        <button type="submit">Sign Up</button>
    </form>

    <p>
        <a href="../login/login-front.php">already have an account Sign In</a>
    </p>

    <p>
        <a href="customer-signup-front.php">Sign up as a customer</a>
    </p>
</body>

</html>
