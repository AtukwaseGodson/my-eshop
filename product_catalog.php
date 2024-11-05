<?php 
include 'header.php'; 
include 'includes/db.php'; 

// Fetch all products
$result = $conn->query("SELECT * FROM products");
?>
<div class="container">
    <h2>Product Catalog</h2>
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
