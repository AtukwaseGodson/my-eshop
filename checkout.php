<?php 
include 'header.php';
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$error = null;
$success = null;

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->begin_transaction();
        
        // Get cart items
        $cartItems = $conn->query("SELECT c.*, p.price, p.name 
                                 FROM cart c 
                                 JOIN products p ON c.product_id = p.id 
                                 WHERE c.user_id = $userId");
        
        if ($cartItems->num_rows > 0) {
            while ($item = $cartItems->fetch_assoc()) {
                $total = $item['price'] * $item['quantity'];
                
                // Insert into orders
                $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total, status) 
                                      VALUES (?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iiid", $userId, $item['product_id'], $item['quantity'], $total);
                $stmt->execute();
            }
            
            // Clear cart after successful order
            $conn->query("DELETE FROM cart WHERE user_id = $userId");
            
            $conn->commit();
            $success = "Order placed successfully!";
        } else {
            throw new Exception("Your cart is empty");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Get cart items for display
$cartItems = $conn->query("SELECT c.*, p.price, p.name 
                          FROM cart c 
                          JOIN products p ON c.product_id = p.id 
                          WHERE c.user_id = $userId");
$totalAmount = 0;
?>

<div class="container">
    <h2>Checkout</h2>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success); ?>
            <p><a href="account.php">View your orders</a></p>
        </div>
    <?php else: ?>
        <div class="checkout-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php while ($item = $cartItems->fetch_assoc()): 
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalAmount += $itemTotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td>$<?php echo htmlspecialchars($itemTotal); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="total-row">
                        <td colspan="3">Total Amount</td>
                        <td>$<?php echo htmlspecialchars($totalAmount); ?></td>
                    </tr>
                </table>
            </div>

            <form id="checkoutForm" method="POST" action="checkout.php">
                <h3>Delivery Information</h3>
                <div class="form-group">
                    <label for="delivery_date">Delivery Date:</label>
                    <input type="date" id="delivery_date" name="delivery_date" required 
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>

                <div class="form-group">
                    <label for="delivery_address">Delivery Address:</label>
                    <textarea id="delivery_address" name="delivery_address" required></textarea>
                </div>

                <div class="form-group">
                    <label for="delivery_method">Delivery Method:</label>
                    <select id="delivery_method" name="delivery_method" required>
                        <option value="">Select delivery method</option>
                        <option value="standard">Standard Delivery</option>
                        <option value="express">Express Delivery</option>
                    </select>
                </div>

                <button type="submit" id="placeOrderBtn">Place Order</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const confirmed = confirm('Are you sure you want to place this order?');
    if (!confirmed) {
        e.preventDefault();
    }
});

// Validate delivery date
document.getElementById('delivery_date').addEventListener('change', function() {
    const selectedDate = new Date(this.value);
    const today = new Date();
    const minDate = new Date(today);
    minDate.setDate(today.getDate() + 1);

    if (selectedDate < minDate) {
        alert('Please select a date at least one day from today');
        this.value = '';
    }
});
</script>

<style>
.checkout-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.order-summary, #checkoutForm {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group textarea {
    height: 100px;
    resize: vertical;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.total-row {
    font-weight: bold;
    background-color: #f9f9f9;
}

.error-message {
    color: #dc3545;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #dc3545;
    border-radius: 4px;
    background-color: #f8d7da;
}

.success-message {
    color: #28a745;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #28a745;
    border-radius: 4px;
    background-color: #d4edda;
}

@media (max-width: 768px) {
    .checkout-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'footer.php'; ?>