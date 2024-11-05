<?php
include 'header.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    mail("support@example.com", "Contact Form Submission", $message, "From: $email");
    echo "<p>Thank you for reaching out, $name! We'll get back to you soon.</p>";
}
?>
<div class="container">
    <h2>Contact Us</h2>
    <form action="contact.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <label for="message">Message:</label>
        <textarea name="message" rows="5" required></textarea>
        <button type="submit">Send</button>
    </form>
</div>
<?php include 'footer.php'; ?>
