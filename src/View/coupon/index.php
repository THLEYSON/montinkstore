<div class="container py-4">
  <h2 class="mb-4">🧾 Coupons</h2>

  <a href="/coupon/create" class="btn btn-primary mb-3">➕ New Coupon</a>

  <?php if (empty($coupons) || !is_array($coupons)): ?>
    <div class="alert alert-info">No coupons found.</div>
  <?php else: ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Code</th>
          <th>Discount (R$)</th>
          <th>Minimum Subtotal</th>
          <th>Expires At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($coupons as $coupon): ?>
          <?php if (is_array($coupon)): ?>
            <tr>
              <td><?= is_numeric($coupon['id'] ?? null) ? $coupon['id'] : '' ?></td>
              <td><?= htmlspecialchars($coupon['code'] ?? '') ?></td>
              <td>R$ <?= number_format(is_numeric($coupon['discount'] ?? null) ? $coupon['discount'] : 0, 2, ',', '.') ?></td>
              <td>R$ <?= number_format(is_numeric($coupon['min_subtotal'] ?? null) ? $coupon['min_subtotal'] : 0, 2, ',', '.') ?></td>
              <td>
                <?php
                  $expiresAt = $coupon['expires_at'] ?? '';
                  echo !empty($expiresAt) && strtotime($expiresAt) ? date('d/m/Y', strtotime($expiresAt)) : '';
                ?>
              </td>
              <td>
                <form method="POST" action="/coupon/edit" class="d-inline">
                  <input type="hidden" name="id" value="<?= htmlspecialchars($coupon['id'] ?? '') ?>">
                  <button type="submit" class="btn btn-sm btn-warning">✏️ Edit</button>
                </form>
                <button type="button" class="btn btn-sm btn-outline-danger"
                  data-bs-toggle="modal"
                  data-bs-target="#confirmDeleteModal"
                  data-id="<?= htmlspecialchars($coupon['id'] ?? '') ?>"
                  data-code="<?= htmlspecialchars($coupon['code'] ?? '') ?>">
                  🗑️ Delete
                </button>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deleteCouponForm" method="POST" action="/coupon/delete">
      <input type="hidden" name="id" id="deleteCouponId">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete coupon <strong id="deleteCouponCode"></strong>?
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  const deleteModal = document.getElementById('confirmDeleteModal');
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id') ?? '';
    const code = button.getAttribute('data-code') ?? '';

    document.getElementById('deleteCouponId').value = id;
    document.getElementById('deleteCouponCode').textContent = code;
  });
</script>
