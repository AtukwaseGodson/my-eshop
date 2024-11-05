<?php
include 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
    header("Location: login.php");
}
include 'header.php';
?>
<div class="container">
    <h2>Sign Up</h2>
    <form action="register.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Sign Up</button>
    </form>
</div>
<?php include 'footer.php'; ?>
