<div class="container py-4">
  <h2 class="mb-4"><?= $coupon ? '✏️ Edit Coupon' : '➕ Create Coupon' ?></h2>

  <form method="POST" action="/coupon/store">
    <?php if ($coupon): ?>
      <input type="hidden" name="id" value="<?= $coupon['id'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label for="code" class="form-label">Coupon Code</label>
      <input type="text" name="code" id="code" class="form-control" required value="<?= $coupon['code'] ?? '' ?>">
    </div>

    <div class="mb-3">
      <label for="discount" class="form-label">Discount (R$)</label>
      <input type="number" name="discount" id="discount" class="form-control" step="0.01" required value="<?= $coupon['discount'] ?? '' ?>">
    </div>

    <div class="mb-3">
      <label for="min_subtotal" class="form-label">Minimum Subtotal to Apply (R$)</label>
      <input type="number" name="min_subtotal" id="min_subtotal" class="form-control" step="0.01" value="<?= $coupon['min_subtotal'] ?? '0' ?>">
    </div>

    <div class="mb-3">
      <label for="expires_at" class="form-label">Expiration Date</label>
      <input type="date" name="expires_at" id="expires_at" class="form-control" required value="<?= $coupon['expires_at'] ?? '' ?>">
    </div>

    <button type="submit" class="btn btn-success">💾 Save Coupon</button>
    <a href="/coupon" class="btn btn-secondary">Back</a>
  </form>
</div>