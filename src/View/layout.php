<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'My App' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="/">🛍️ Store Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">🏠 Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/product">➕ Add Product</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/stock/view-stock-details">📦 Edit Stock</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/coupon">🎟️ Coupons</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <div class="container py-4">
    <?php 
      $alertPath = __DIR__ . '/components/alert.php';
      if (file_exists($alertPath)) {
          require $alertPath;
      }
    ?>
    <?= $content ?? '' ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/js/cep.js"></script>
  <script src="/js/cart.js"></script>
  <?php if (!empty($customScript)) echo $customScript; ?>
</body>
</html>
