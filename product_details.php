<?php 
include 'header.php'; 
include 'includes/db.php'; 

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();
?>
<div class="container">
    <h2><?php echo $product['name']; ?></h2>
    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    <p>Price: $<?php echo $product['price']; ?></p>
    <p><?php echo $product['description']; ?></p>
    <form class="add-to-cart-form" action="cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">Add to Cart</button>
    </form>
    <script>
function addToCart(productId) {
    const quantity = document.querySelector('input[name="quantity"]').value;
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
            // Optionally refresh the page or update cart count
        } else {
            alert(data.message || 'Error adding product to cart');
            if (data.message === 'Please login first') {
                window.location.href = 'login.php';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding product to cart');
    });
}
</script>
</div>
<?php include 'footer.php'; ?>
