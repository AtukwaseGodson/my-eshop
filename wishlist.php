<?php
include 'includes/db.php';
session_start();
include 'header.php';

$userId = $_SESSION['user_id'];
$wishlistItems = $conn->query("SELECT p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = $userId");
?>
<div class="container">
    <h2>My Wishlist</h2>
    <div class="products">
        <?php while ($product = $wishlistItems->fetch_assoc()): ?>
            <div class="product">
                <a href="product_details.php?id=<?php echo $product['id']; ?>">
                    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p>Price: $<?php echo $product['price']; ?></p>
                </a>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
