<?php 
include 'header.php'; 
include 'includes/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Handle AJAX add to cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    try {
        // Check if product already exists in cart
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing cart item
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        } else {
            // Insert new cart item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            throw new Exception("Error adding to cart");
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

$userId = $_SESSION['user_id'];
$totalAmount = 0;

// Use JOIN to get cart items with product details
try {
    $query = "SELECT c.*, p.name, p.price, p.image 
              FROM cart c 
              JOIN products p ON c.product_id = p.id 
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $cartItems = $stmt->get_result();

    if ($cartItems === false) {
        throw new Exception("Error executing cart query: " . $conn->error);
    }
?>

<div class="container">
    <h2>Your Cart</h2>
    <?php if ($cartItems->num_rows === 0): ?>
        <p>Your cart is empty. <a href="product_catalog.php">Continue shopping</a></p>
    <?php else: ?>
        <table class="cart-table">
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
            <?php while ($item = $cartItems->fetch_assoc()): 
                $totalPrice = $item['price'] * $item['quantity'];
                $totalAmount += $totalPrice;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>
                        <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                    </td>
                    <td>
                        <form class="update-quantity-form" data-product-id="<?php echo $item['product_id']; ?>">
                            <input type="number" value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                   min="1" class="quantity-input">
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                    <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                    <td>$<?php echo htmlspecialchars($totalPrice); ?></td>
                    <td>
                        <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)" 
                                class="remove-btn">Remove</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            <tr class="total-row">
                <td colspan="4">Total Amount</td>
                <td colspan="2">$<?php echo htmlspecialchars($totalAmount); ?></td>
            </tr>
        </table>
        <div class="cart-actions">
            <a href="product_catalog.php" class="continue-shopping">Continue Shopping</a>
            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php 
} catch (Exception $e) {
    echo "<div class='container'><p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p></div>";
}
?>

<style>
.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.cart-table th, .cart-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.cart-table th {
    background-color: #f5f5f5;
}

.quantity-input {
    width: 60px;
    padding: 5px;
    margin-right: 5px;
}

.update-btn, .remove-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.update-btn {
    background-color: #4CAF50;
    color: white;
}

.remove-btn {
    background-color: #f44336;
    color: white;
}

.total-row {
    font-weight: bold;
    background-color: #f9f9f9;
}

.cart-actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.continue-shopping, .checkout-btn {
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 4px;
}

.continue-shopping {
    background-color: #6c757d;
    color: white;
}

.checkout-btn {
    background-color: #28a745;
    color: white;
}

.error {
    color: #dc3545;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #dc3545;
    border-radius: 4px;
    background-color: #f8d7da;
}
</style>

<script>
function addToCart(productId, quantity = 1) {
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_cart&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
            location.reload();
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

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove_from_cart&product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error removing item from cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item from cart');
        });
    }
}

// Add event listeners when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity update forms
    document.querySelectorAll('.update-quantity-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = this.querySelector('.quantity-input').value;
            updateCartQuantity(productId, quantity);
        });
    });
});
</script>

<?php include 'footer.php'; ?>
