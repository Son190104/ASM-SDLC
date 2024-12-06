<?php
include('dbconnect.php');

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch products from the database
$query = "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
";
$stmt = $conn->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Initialize cart if not already set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update product in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Redirect to the shopping page
    header('Location: shoppingcart.php');
    exit;
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id']; // Logged in user ID
    $total = 0;

    // Calculate the total cost of the cart
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = :product_id");
        $stmt->execute([':product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $total += $product['price'] * $quantity;
    }

    // Insert the order into the orders table
    $query = "INSERT INTO orders (created_at, user_id, total, status) 
              VALUES (NOW(), :user_id, :total, 'Pending')";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $user_id, ':total' => $total]);
    $order_id = $conn->lastInsertId();

    // Insert order details into the order_detail table
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = :product_id");
        $stmt->execute([':product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $query = "INSERT INTO order_detail (order_id, product_id, price, amount) 
                  VALUES (:order_id, :product_id, :price, :amount)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':price' => $product['price'],
            ':amount' => $quantity
        ]);
    }

    // Clear the cart after checkout
    unset($_SESSION['cart']);
    header('Location: shoppingcart.php?success=1');
    exit;
}

// Remove product from cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];

    // Remove product from cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Redirect back to shopping cart page
    header('Location: shoppingcart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header>
    <div class="navbar">
            <div class="navbar-left">
                <a href="customer.php">KRIK </a>
                <div class="dropdown">
                    <a href="customer.php">MEN'S SHIRTS</a>
                    <div class="dropdown-content">
                        <a href="#">T-shirt + shirt</a>
                        <a href="#">Polo shirt + Hoodie</a>
                        <a href="#">Jacket + Blazer</a>
                        
                    </div>
                </div>

                <div class="dropdown">
                    <a href="customer.php">MEN'S PANTS</a>
                    <div class="dropdown-content">
                        <a href="#">Pants Jeans</a>
                        <a href="#">Pants trousers</a>
                        
                    </div>
                </div>

                <div class="dropdown">
                    <a href="customer.php">ACCESSORIES</a>
                    <div class="dropdown-content">
                        <a href="#">Belts</a>
                        <a href="#">Backpacks + Handbags</a>
                        <a href="#">Shoes</a>
                        
                    </div>

                </div>
                
                <div class="navbar-right">
                    <div class="search-bar">
                        <i class="fas fa-search">
                        </i>
                        <input placeholder="Search" type="text" />
                    </div>
                    <a href="login.php" onclick="openLoginModal()"><i class="fas fa-user"></i></a>
                    </a>
                    <a href="#">
                        <i class="fas fa-heart">
                        </i>
                    </a>
                    <div class="cart">
                        <a href="shoppingcart.php" onclick="toggleCartModal()">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        
                    </div>
                </div>
                <nav>
           
            </nav>
            </div>
    </header>
             <!-- Shopping Cart -->
        <section>
            <h2>Shopping Cart</h2>
            <?php if (!empty($_SESSION['cart'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $product_id => $quantity):
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
                            $stmt->execute([':id' => $product_id]);
                            $product = $stmt->fetch(PDO::FETCH_ASSOC);
                            $subtotal = $product['price'] * $quantity;
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <img 
                                        src="<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" 
                                        alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                        style="width: 100px; height: auto;">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td><?php echo number_format($subtotal); ?> VNĐ</td>
                                <td>
                                    <form method="POST" action="shoppingcart.php">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="remove_from_cart">DELETE</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4"><strong>Total:</strong></td>
                            <td><?php echo number_format($total); ?> VNĐ</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Checkout Form -->
                <form method="POST" action="shoppingcart.php">
                    <button type="submit" name="checkout">PLACE ODER</button>
                </form>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
            
            <!-- Success message when checkout is complete -->
        <?php if (isset($_GET['success'])): ?>
            <p style="color: green;">Your order has been placed successfully!</p>
        <?php endif; ?>
     <!-- banner -->
     <div class="row">
                <div class="banner col-12">
                    <img src="https://pos.nvncdn.com/be3159-662/bn/20240921_h3xCoY0r.gif" alt="">
                </div>
            </div>
</body>
<footer>
        <div class="footer">
            <div class="footer-column">
                <h3>
                    ABOUT US
                </h3>
                <p>
                    <strong>
                        KRIK Vietnam Co., Ltd.
                    </strong>
                </p>
                <p>
                    Address: No. 344 Cau Giay, Dich Vong Ward, Cau Giay District, Hanoi City
                </p>
                <p>
                    Business Registration Number: 0108901419 issued by the Hanoi Department of Planning and Investment on September 17, 2019
                </p>
                <p>
                    Phone: 0379.058.911
                </p>
            </div>
            <div class="footer-column">
                <h3>
                    POLICIES AND REGULATIONS
                </h3>
                <p>
                    <a href="cachdathang.html">
                        How to place an order
                    </a>
                </p>
                <p>
                    <a href="#">
                        Membership policy
                    </a>
                </p>
                <p>
                    <a href="#">
                        Delivery policy
                    </a>
                </p>
                <p>
                    <a href="#">
                        Return policy
                    </a>
                </p>
                <p>
                    <a href="#">
                        Privacy policy
                    </a>
                </p>
            </div>
            <div class="footer-column">
                <h3>
                    STORE LOCATIONS
                </h3>
                <p>
                    <strong>
                        ▶ STORE NO. 15
                    </strong>
                </p>
                <p>
                    BIG C Thang Long, 222 Tran Duy Hung, Trung Hoa Ward, Cau Giay District, Hanoi
                </p>
                <p>
                    Tel: 0379.058.911
                </p>
                <p>
                    <strong>
                        ▶ STORE NO. 14
                    </strong>
                </p>
                <p>
                    84 Pham Minh Duc, May To Ward, Ngo Quyen District, Hai Phong City
                </p>
                <p>
                    Tel: 0379.058.911
                </p>
                <p>
                    <strong>
                        <a href="">▶ VIEW ALL STORE LOCATIONS</a>
                    </strong>
                </p>
            </div>
            <div class="footer-column">
                <h3>
                    CONNECT WITH US
                </h3>
                <div class="social-icons">
                    <i class="fab fa-facebook-f">
                    </i>
                    <i class="fas fa-shopping-bag">
                    </i>
                    <i class="fas fa-comment-dots">
                    </i>
                </div>
                <div class="contact-info">
                    <p>
                        ORDER INQUIRIES (08:30 - 22:00)
                    </p>
                    <p>
                        <strong>
                            0379.058.911
                        </strong>
                    </p>
                    <p>
                        FEEDBACK & COMPLAINTS (08:30 - 22:00)
                    </p>
                    <p>
                        <strong>
                            0379.058.911
                        </strong>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</html>