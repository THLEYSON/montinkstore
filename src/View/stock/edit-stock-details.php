<div class="container py-5" style="max-width: 900px;">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">✏️ Edit Products, Variations, Prices & Quantities</h4>
    </div>
    <div class="card-body px-4 py-4">
      <?php if (!empty($stocks) && is_array($stocks)): ?>
        <form action="/stock/edit-stock-details" method="POST">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>Product</th>
                <th>Variation</th>
                <th>Price (R$)</th>
                <th>Quantity</th>
                <th>Action</th> 
              </tr>
            </thead>
            <tbody>
              <?php foreach ($stocks as $stock): ?>
                <?php if (is_array($stock)): ?>
                  <?php
                    $id          = htmlspecialchars($stock['id'] ?? '');
                    $productId   = htmlspecialchars($stock['product_id'] ?? '');
                    $name        = htmlspecialchars($stock['product_name'] ?? '');
                    $variation   = htmlspecialchars($stock['variation'] ?? '');
                    $price       = is_numeric($stock['price'] ?? null) ? $stock['price'] : '0.00';
                    $quantity    = is_numeric($stock['quantity'] ?? null) ? $stock['quantity'] : '0';
                  ?>
                  <tr>
                    <td><?= $name ?></td>
                    <td>
                      <input 
                        type="text" 
                        name="variations[<?= $id ?>]" 
                        class="form-control" 
                        value="<?= $variation ?>" 
                        required>
                    </td>
                    <td>
                      <input 
                        type="number" 
                        step="0.01" 
                        name="prices[<?= $id ?>]" 
                        class="form-control" 
                        value="<?= htmlspecialchars($price) ?>" 
                        required>
                    </td>
                    <td>
                      <input 
                        type="number" 
                        name="quantities[<?= $id ?>]" 
                        class="form-control" 
                        value="<?= htmlspecialchars($quantity) ?>" 
                        required>
                    </td>
                    <td>
                      <button type="button"
                          class="btn btn-outline-danger btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#confirmDeleteModal"
                          data-product-id="<?= $productId ?>"
                          data-stock-id="<?= $id ?>">
                          🗑️
                      </button>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div class="text-end">
            <button type="submit" class="btn btn-success">💾 Save Changes</button>
          </div>
        </form>
      <?php else: ?>
        <div class="alert alert-info text-center mb-0">
          No products registered yet.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal for delete confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">⚠️ Confirm Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this product and its variation?
      </div>
      <div class="modal-footer">
        <form id="deleteForm" action="/stock/delete-product" method="POST">
          <input type="hidden" name="product_id" id="modalProductId">
          <input type="hidden" name="stock_id" id="modalStockId">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const confirmDeleteModal = document.getElementById('confirmDeleteModal');

  confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const productId = button.getAttribute('data-product-id') ?? '';
    const stockId = button.getAttribute('data-stock-id') ?? '';

    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalStockId').value = stockId;
  });
</script>
