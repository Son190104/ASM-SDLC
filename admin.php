<?php
require 'db/db.php';
session_start();

// Fetch all products with category names
$query = "SELECT products.*, categories.name AS category_name 
          FROM products 
          LEFT JOIN categories ON products.category_id = categories.id";
$stmt = $conn->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for dropdowns
$query = "SELECT id, name FROM categories";
$stmt = $conn->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle add product form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_dir = 'uploads/products/';
        
        // Check if the directory exists, create it if not
        if (!is_dir($image_dir)) {
            mkdir($image_dir, 0777, true); // Create the directory with permissions
        }
        
        $image_name = basename($_FILES['image']['name']);
        $image_path = $image_dir . $image_name;

        // Validate the uploaded file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image = $image_path;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid image format. Allowed formats: jpg, jpeg, png, gif.";
        }
    }
    

    // Validate inputs
    if (empty($name) || $price <= 0 || empty($description) || $quantity <= 0 || $category_id <= 0) {
        $error_message = "Please fill in all fields with valid values.";
    } else {
        try {
            $query = "INSERT INTO products (name, price, description, quantity, category_id, image) 
                      VALUES (:name, :price, :description, :quantity, :category_id, :image)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':description' => $description,
                ':quantity' => $quantity,
                ':category_id' => $category_id,
                ':image' => $image
            ]);
            $success_message = "Product added successfully!";
        } catch (PDOException $e) {
            $error_message = "Failed to add product: " . $e->getMessage();
        }
    }
}

// Handle edit product form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);

    // Handle image upload (for editing)
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_dir = 'uploads/products/';
        
        // Check if the directory exists, create it if not
        if (!is_dir($image_dir)) {
            mkdir($image_dir, 0777, true); // Create the directory with permissions
        }
        
        $image_name = basename($_FILES['image']['name']);
        $image_path = $image_dir . $image_name;

        // Validate the uploaded file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image = $image_path;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid image format. Allowed formats: jpg, jpeg, png, gif.";
        }
    }

    // Validate inputs
    if (empty($name) || $price <= 0 || empty($description) || $quantity <= 0 || $category_id <= 0 || $id <= 0) {
        $error_message = "Please fill in all fields with valid values.";
    } else {
        try {
            $query = "UPDATE products 
                      SET name = :name, price = :price, description = :description, 
                          quantity = :quantity, category_id = :category_id, image = :image 
                      WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':description' => $description,
                ':quantity' => $quantity,
                ':category_id' => $category_id,
                ':image' => $image,
                ':id' => $id
            ]);
            $success_message = "Product updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Failed to update product: " . $e->getMessage();
        }
    }
}

// Handle delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    try {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);

        $success_message = "Product deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to delete product: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Management</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <header>
    <h1>Product Management</h1>
    <nav>
      <ul>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <section>
    <!-- Display Success and Error Messages -->
    <?php if (isset($success_message)): ?>
      <div class="success_message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
      <div class="error_message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <h2>Product List</h2>
    <table>
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Category</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
          <td><?php echo htmlspecialchars($product['name']); ?></td>
          <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
          <td><?php echo $product['quantity']; ?></td>
          <td><?php echo htmlspecialchars($product['category_name']); ?></td>
          <td>
            <?php if ($product['image']): ?>
              <img src="<?php echo $product['image']; ?>" alt="Product Image" width="100">
            <?php else: ?>
              No Image
            <?php endif; ?>
          </td>
          <td>
            <a href="#edit_<?php echo $product['id']; ?>" class="edit_btn" onclick="showEditForm(<?php echo $product['id']; ?>)">Edit</a> | 
            <a href="?delete=<?php echo $product['id']; ?>" class="delete_btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Add New Product Form -->
    <h2>Add New Product</h2>
    <form method="POST" action="admin.php" enctype="multipart/form-data">
      <label for="name">Product Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="price">Price:</label>
      <input type="number" id="price" name="price" step="1" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" required></textarea>

      <label for="quantity">Quantity:</label>
      <input type="number" id="quantity" name="quantity" required>

      <label for="category_id">Category:</label>
      <select id="category_id" name="category_id" required>
        <?php foreach ($categories as $category): ?>
        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="image">Image:</label>
      <input type="file" id="image" name="image">

      <button type="submit" name="add_product">Add Product</button>
    </form>

             
      <!-- Edit Product Form -->
    <div id="edit_form" style="display: none;">
    <h2>Edit Product</h2>
    <form method="POST" action="admin.php" enctype="multipart/form-data">
      <input type="hidden" id="edit_id" name="id">
      
      <label for="edit_name">Product Name:</label>
      <input type="text" id="edit_name" name="name" required>

      <label for="edit_price">Price:</label>
      <input type="number" id="edit_price" name="price" step="1" required>

      <label for="edit_description">Description:</label>
      <textarea id="edit_description" name="description" required></textarea>

      <label for="edit_quantity">Quantity:</label>
      <input type="number" id="edit_quantity" name="quantity" required>

      <label for="edit_category_id">Category:</label>
      <select id="edit_category_id" name="category_id" required>
        <?php foreach ($categories as $category): ?>
        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
        <?php endforeach; ?>
      </select>

      <label for="edit_image">Image:</label>
      <input type="file" id="edit_image" name="image">

      <button type="submit" name="edit_product">Save Changes</button>
      <button type="button" onclick="hideEditForm()">Cancel</button>
    </form>
    </div>
  </section>
   


  <script>
function showEditForm(id) {
  // Lấy sản phẩm tương ứng từ danh sách sản phẩm (products)
  const product = <?php echo json_encode($products); ?>.find(p => p.id == id);
  
  if (product) {
    // Hiển thị form và điền dữ liệu
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_quantity').value = product.quantity;
    document.getElementById('edit_category_id').value = product.category_id;

    // Hiển thị Form chỉnh sửa
    document.getElementById('edit_form').style.display = 'block';
  }
}

function hideEditForm() {
  // Ẩn Form chỉnh sửa
  document.getElementById('edit_form').style.display = 'none';
}
</script>


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
