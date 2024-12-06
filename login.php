<?php
include('dbconnect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($user['role'] === 'customer') {
            header('Location: customer.php');
        } else {
            $error = "Invalid role. Please contact support.";
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Sign Up Button -->
        <div class="signup-container">
            <p>Don't have an account? <a href="signup.php" class="signup-link">Sign Up</a></p>
        </div>
    </div>

                  <!-- banner -->
                  <div class="row">
                <div class="banner col-12">
                    <img src="https://pos.nvncdn.com/be3159-662/bn/20240921_h3xCoY0r.gif" alt="">
                </div>
            </div>
    </main>
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
</body>
</html>
