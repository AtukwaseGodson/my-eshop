<?php 
include 'header.php'; 
include 'includes/db.php'; 

// Fetch featured products
$result = $conn->query("SELECT * FROM products WHERE featured = 1 LIMIT 4");
?>
<div class="container">
    <h2>Featured Products</h2>
    <div class="products">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product">
                <a href="product_details.php?id=<?php echo $product['id']; ?>">
                    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p>Price: $<?php echo $product['price']; ?></p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'footer.php'; ?>
