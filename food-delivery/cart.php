<?php
session_start();
include 'db.php';
include 'header.php';

// Xử lý xóa sản phẩm
if (isset($_GET['remove'])) {
  $id = intval($_GET['remove']);
  unset($_SESSION['cart'][$id]);
  header("Location: cart.php");
  exit();
}
?>

<div class="container mt-4">
  <h2>🛒 Giỏ hàng</h2>

  <?php
  if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "<p>Trống.</p>";
  } else {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $id_str = implode(",", $ids);

    $rs = $conn->query("SELECT * FROM products WHERE id IN ($id_str)");

    if ($rs && $rs->num_rows > 0) {
      echo '<table class="table table-bordered">
              <thead><tr><th>Tên món</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th><th>Xoá</th></tr></thead>
              <tbody>';
      $total = 0;

      while ($row = $rs->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $price = $row['price'];
        $qty = $_SESSION['cart'][$id];
        $sub = $price * $qty;
        $total += $sub;

        echo "<tr>
                <td>$name</td>
                <td>" . number_format($price * 1000) . "đ</td>
                <td>$qty</td>
                <td>" . number_format($sub * 1000) . "đ</td>
                <td><a href='cart.php?remove=$id' class='btn btn-danger btn-sm'>Xoá</a></td>
              </tr>";
      }

      echo "<tr><td colspan='3'><strong>Tổng cộng</strong></td>
                <td><strong>" . number_format($total * 1000) . "đ</strong></td>
                <td></td></tr>";

      echo '</tbody></table>';
      echo '<a href="checkout.php" class="btn btn-success">🔁 Tiến hành thanh toán</a>';
    } else {
      echo "<p>Không tìm thấy sản phẩm trong giỏ hàng.</p>";
    }
  }
  ?>

</div>

<?php include 'footer.php'; ?>
