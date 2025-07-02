<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']); // đảm bảo là số nguyên

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
  } else {
    $_SESSION['cart'][$id] = 1;
  }

  header("Location: cart.php");
  exit();
} else {
  echo "Không có sản phẩm được chọn.";
}
