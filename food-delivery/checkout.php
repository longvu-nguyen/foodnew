<?php
session_start();
include 'db.php';
include 'header.php';
?>

<div class="container mt-4">
  <h2>💳 Thanh toán</h2>

  <?php
  if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<p>Giỏ hàng của bạn đang trống.</p>";
  } else {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $id_str = implode(",", $ids);

    $rs = $conn->query("SELECT * FROM products WHERE id IN ($id_str)");

    if ($rs && $rs->num_rows > 0) {
      echo '<table class="table table-bordered"><thead><tr><th>Tên món</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th></tr></thead><tbody>';
      $total = 0;
      $items_text = "";

      while ($row = $rs->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $price = $row['price'];
        $qty = $_SESSION['cart'][$id];
        $sub = $price * $qty;
        $total += $sub;
        $items_text .= "$name x $qty\n";

        echo "<tr>
                <td>$name</td>
                <td>" . number_format($price * 1000) . "đ</td>
                <td>$qty</td>
                <td>" . number_format($sub * 1000) . "đ</td>
              </tr>";
      }

      echo "<tr><td colspan='3'><strong>Tổng cộng</strong></td>
                <td><strong>" . number_format($total * 1000) . "đ</strong></td></tr>
            </tbody></table>";
  ?>

      <form method="post">
        <div class="form-group">
          <label><strong>Chọn phương thức thanh toán:</strong></label><br>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_method" value="Tiền mặt" required>
            <label class="form-check-label">💵 Tiền mặt</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="payment_method" value="Chuyển khoản" required>
            <label class="form-check-label">🏦 Chuyển khoản</label>
          </div>
        </div>
        <button name="checkout" class="btn btn-success mt-2">✅ Xác nhận thanh toán</button>
      </form>

  <?php
    } else {
      echo "<p>Không tìm thấy sản phẩm trong giỏ hàng!</p>";
    }
  }

  if (isset($_POST['checkout']) && isset($items_text)) {
    $method = $_POST['payment_method'];
    $user = $_SESSION['user'];
    $status = "Đã chuyển cho tài xế";
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO orders (username, items, total, method, status, created_at)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $user, $items_text, $total, $method, $status, $now);
    $stmt->execute();

    unset($_SESSION['cart']);

    echo "<div class='alert alert-success mt-4'>
      ✅ Đã thanh toán bằng <strong>$method</strong><br>
      🧾 Tổng cộng: <strong>" . number_format($total * 1000) . "đ</strong><br>
      📦 Trạng thái: <strong>$status</strong>
    </div>";
  }
  ?>
</div>

<?php include 'footer.php'; ?>
