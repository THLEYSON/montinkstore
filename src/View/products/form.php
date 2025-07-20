<div class="container py-5" style="max-width: 900px;">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">🛍️ Register Product</h4>
    </div>
    <div class="card-body px-4 py-4">
      <form action="/product/store" method="POST">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 required
                 value="<?= htmlspecialchars($product['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <h5>Variations</h5>
          <div id="variationList">
            <?php if (!empty($product['variations']) && is_array($product['variations'])): ?>
              <?php foreach ($product['variations'] as $index => $variation): ?>
                <div class="variation row mb-2">
                  <div class="col-md-4">
                    <input type="text" name="variations[]" class="form-control"
                      placeholder="Variation" required
                      value="<?= htmlspecialchars($variation['name'] ?? '') ?>">
                  </div>
                  <div class="col-md-3">
                    <input type="number" step="0.01" name="variation_prices[]" class="form-control"
                      placeholder="Price (R$)" required
                      value="<?= is_numeric($variation['price'] ?? null) ? $variation['price'] : '' ?>">
                  </div>
                  <div class="col-md-3">
                    <input type="number" name="quantities[]" class="form-control"
                      placeholder="Quantity" required
                      value="<?= is_numeric($variation['quantity'] ?? null) ? $variation['quantity'] : '' ?>">
                  </div>
                  <div class="col-md-2 d-grid">
                    <button type="button" class="btn btn-danger remove-variation">❌</button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- Default empty variation row -->
              <div class="variation row mb-2">
                <div class="col-md-4">
                  <input type="text" name="variations[]" class="form-control" placeholder="Variation" required>
                </div>
                <div class="col-md-3">
                  <input type="number" step="0.01" name="variation_prices[]" class="form-control" placeholder="Price (R$)" required>
                </div>
                <div class="col-md-3">
                  <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" required>
                </div>
                <div class="col-md-2 d-grid">
                  <button type="button" class="btn btn-danger remove-variation">❌</button>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" id="addVariation" class="btn btn-outline-primary mt-2">➕ Add Variation</button>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-success">💾 Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS para adicionar/remover variações -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function () {
    $('#addVariation').on('click', function () {
      $('#variationList').append(`
        <div class="variation row mb-2">
          <div class="col-md-4">
            <input type="text" name="variations[]" class="form-control" placeholder="Variation" required>
          </div>
          <div class="col-md-3">
            <input type="number" step="0.01" name="variation_prices[]" class="form-control" placeholder="Price (R$)" required>
          </div>
          <div class="col-md-3">
            <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" required>
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-danger remove-variation">❌</button>
          </div>
        </div>
      `);
    });

    $(document).on('click', '.remove-variation', function () {
      $(this).closest('.variation').remove();
    });
  });
</script>
