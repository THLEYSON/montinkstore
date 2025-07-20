<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['flash'])):
  $type = $_SESSION['flash']['type'] ?? 'info';
  $message = $_SESSION['flash']['message'] ?? '';
  unset($_SESSION['flash']);
?>
  <div class="alert alert-<?= $type ?> alert-dismissible fade show mt-3" role="alert">
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
