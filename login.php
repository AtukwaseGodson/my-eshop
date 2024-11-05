<?php
include 'includes/db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: account.php");
    } else {
        $error = "Invalid email or password";
    }
}
include 'header.php';
?>
<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
<?php include 'footer.php'; ?>
