<div class="container py-5">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
      <h4 class="mb-0">🛒 Your Cart</h4>
    </div>
    <div class="card-body px-4 py-4">
      <?php if (!empty($cart) && is_array($cart)): ?>
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th>Variation</th>
              <th>Price (R$)</th>
              <th>Quantity</th>
              <th>Total (R$)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): ?>
              <?php if (is_array($item)): ?>
                <tr>
                  <td><?= htmlspecialchars(!empty($item['product_name']) ? $item['product_name'] : '') ?></td>
                  <td><?= htmlspecialchars(!empty($item['variation']) ? $item['variation'] : '') ?></td>
                  <td>
                    <?= number_format(is_numeric($item['price'] ?? null) ? $item['price'] : 0, 2, ',', '.') ?>
                  </td>
                  <td>
                    <?= is_numeric($item['quantity'] ?? null) ? $item['quantity'] : 0 ?>
                  </td>
                  <td>
                    <?php
                      $price = is_numeric($item['price'] ?? null) ? $item['price'] : 0;
                      $qty   = is_numeric($item['quantity'] ?? null) ? $item['quantity'] : 0;
                      echo number_format($price * $qty, 2, ',', '.');
                    ?>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="text-end mt-3">
          <p><strong>Subtotal:</strong> R$
            <?= number_format(is_numeric($subtotal ?? null) ? $subtotal : 0, 2, ',', '.') ?>
          </p>
          <p><strong>Discount:</strong> R$
            <?= number_format(is_numeric($discount ?? null) ? $discount : 0, 2, ',', '.') ?>
          </p>
          <p><strong>Frete:</strong> R$
            <?= number_format(is_numeric($freight ?? null) ? $freight : 0, 2, ',', '.') ?>
          </p>
          <h5><strong>Total:</strong> R$
            <?= number_format(is_numeric($total ?? null) ? $total : 0, 2, ',', '.') ?>
          </h5>
        </div>

        <form action="/cart/checkout" method="POST" class="text-end mt-3">
          <div class="mb-3 text-start">
            <label for="email" class="form-label"><strong>Email:</strong></label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="you@example.com">
            <div class="form-text text-muted">
              The purchase confirmation and order summary will be sent to this email.
            </div>
          </div>
          <button type="submit" class="btn btn-success">✅ Finalize Purchase</button>
        </form>
      <?php else: ?>
        <div class="alert alert-info text-center mb-0">
          Your cart is empty.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
