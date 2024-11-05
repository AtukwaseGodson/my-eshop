<?php include 'header.php'; include 'includes/db.php'; ?>
<div class="container">
    <h2>My Account</h2>
    <div class="account-details">
        <p>Name: <?php echo $_SESSION['user_name']; ?></p>
        <p>Email: <?php echo $_SESSION['user_email']; ?></p>
        <h3>Order History</h3>
        <table>
            <tr><th>Date</th><th>Product</th><th>Quantity</th><th>Total</th></tr>
            <?php
            $userId = $_SESSION['user_id'];
            $orders = $conn->query("SELECT * FROM orders WHERE user_id = $userId");
            while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['date']; ?></td>
                    <td><?php echo $order['product_name']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>$<?php echo $order['total']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php include 'footer.php'; ?>
