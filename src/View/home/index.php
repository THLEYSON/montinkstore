<div class="container py-4">
  <h2 class="mb-4">🛒 Welcome to the Store</h2>

  <!-- CEP Form -->
  <div class="mb-4 d-flex justify-content-between align-items-center gap-3 flex-wrap">
    <form id="cepForm" class="d-flex align-items-center gap-2 flex-wrap mb-0">
      <label for="cep" class="mb-0">Enter ZIP Code:</label>
      <input type="text" name="cep" id="cep" class="form-control" maxlength="9" required style="max-width: 150px;">
      <button type="submit" class="btn btn-primary">Check CEP</button>
    </form>

    <button id="openCartBtn" class="btn btn-outline-dark position-relative">
      🛒 View Cart
      <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        0
      </span>
    </button>
  </div>

  <div id="cepResult" class="alert alert-info d-none"></div>

  <!-- Products -->
  <div class="row">
    <?php if (!empty($products) && is_array($products)): ?>
      <?php foreach ($products as $product): ?>
        <?php if (is_array($product)): ?>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name'] ?? '') ?></h5>
                <p><strong>Variation:</strong> <?= htmlspecialchars($product['variation'] ?? '') ?></p>
                <p><strong>Price:</strong> R$
                  <?= number_format(is_numeric($product['price'] ?? null) ? $product['price'] : 0, 2, ',', '.') ?>
                </p>
                <p><strong>In Stock:</strong>
                  <?= is_numeric($product['quantity'] ?? null) ? $product['quantity'] : 0 ?>
                </p>

                <form class="add-to-cart-form" method="POST" action="/cart/add">
                  <input type="hidden" name="stock_id" value="<?= htmlspecialchars($product['id'] ?? '') ?>">
                  <input type="number"
                         name="quantity"
                         min="1"
                         max="<?= is_numeric($product['quantity'] ?? null) ? $product['quantity'] : 1 ?>"
                         value="1"
                         class="form-control mb-2">
                  <button type="submit" class="btn btn-success w-100">🛍️ Add to Cart</button>
                </form>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">No products available at the moment.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Cart Summary Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="cartModalLabel">🛒 Cart Summary</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="cartContent">
        <form id="couponForm" class="d-flex gap-2 mb-3">
          <input type="text" name="code" class="form-control" placeholder="Enter coupon code..." required>
          <button type="submit" class="btn btn-outline-primary">Apply</button>
        </form>
        <div id="couponFeedback" class="alert d-none p-2"></div>
        <div id="cartContent">
          <p>Loading cart...</p>
        </div>
      </div>
      <div class="modal-footer">
        <a href="/cart" class="btn btn-primary">🧾 View Full Cart</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
