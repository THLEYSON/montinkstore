$(function () {
    function updateCartCount() {
        $.getJSON('/cart/summary', function (res) {
            const count = res.cart ? res.cart.length : 0;
            $('#cartCount').text(count);
        });
    }

    function loadCartSummary() {
        $.getJSON('/cart/summary', function (res) {
            if (!res || !res.cart) return;

            let html = '<table class="table table-sm">';
            html += '<thead><tr><th>Product</th><th>Variation</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead><tbody>';

            res.cart.forEach(item => {
                html += `<tr>
        <td>${item.product_name}</td>
        <td>${item.variation}</td>
        <td>${item.quantity}</td>
        <td>R$ ${parseFloat(item.price).toFixed(2).replace('.', ',')}</td>
        <td>R$ ${(item.price * item.quantity).toFixed(2).replace('.', ',')}</td>
        <td>
          <button class="btn btn-sm btn-outline-danger remove-from-cart" data-id="${item.stock_id}">🗑️</button>
        </td>
      </tr>`;
            });

            html += `</tbody></table><hr>`;

            if (res.coupon) {
                html += `<p><strong>Coupon:</strong> <span class="text-info">${res.coupon}</span></p>`;
            }

            if (res.discount > 0) {
                html += `<p><strong>Discount:</strong> - R$ ${res.discount.toFixed(2).replace('.', ',')}</p>`;
            }

            html += `
            <p><strong>Subtotal:</strong> R$ ${res.subtotal.toFixed(2).replace('.', ',')}</p>
            <p><strong>Freight:</strong> R$ ${res.freight.toFixed(2).replace('.', ',')}</p>
            <p><strong>Total:</strong> <span class="text-success fw-bold">R$ ${res.total.toFixed(2).replace('.', ',')}</span></p>
            `;

            html += `
            <hr>
            <form id="couponForm" class="input-group mb-3">
                <input type="text" class="form-control" name="code" placeholder="Enter coupon code" required>
                <button class="btn btn-outline-primary" type="submit">Apply Coupon</button>
            </form>
            <div id="couponFeedback" class="alert d-none mt-2"></div>
            `;

            $('#cartContent').html(html);
        });
    }

    $(document).on('submit', '#couponForm', function (e) {
        e.preventDefault();

        const code = $(this).find('input[name="code"]').val();
        const $feedback = $('#couponFeedback');

        $.ajax({
            url: '/cart/apply-coupon',
            method: 'POST',
            data: { code },
            dataType: 'json',
            headers: {
            'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
            $feedback
                .removeClass('d-none alert-danger alert-success')
                .addClass(res.status === 'success' ? 'alert-success' : 'alert-danger')
                .text(res.message);

            if (res.status === 'success') {
                loadCartSummary();
            }
            },
            error: function () {
            $feedback
                .removeClass('d-none alert-success')
                .addClass('alert-danger')
                .text('Failed to apply coupon.');
            }
        });
    });



    $('#openCartBtn').on('click', function () {
        loadCartSummary();
        $('#cartModal').modal('show');
    });

    $('.add-to-cart-form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const data = form.serialize();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: data,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                if (res.status === 'success') {
                    loadCartSummary();
                    updateCartCount();
                    $('#cartModal').modal('show');
                } else {
                    alert(res.message || 'Error adding to cart.');
                }
            },
            error: function () {
                alert('Server communication failed.');
            }
        });
    });

    $(document).on('click', '.remove-from-cart', function () {
        const stockId = $(this).data('id');

        $.ajax({
            url: '/cart/remove',
            method: 'POST',
            data: { stock_id: stockId },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                if (res.status === 'success') {
                    loadCartSummary();
                    updateCartCount();
                } else {
                    alert(res.message || 'Error removing item.');
                }
            },
            error: function () {
                alert('Failed to communicate with server.');
            }
        });
    });

    $('#couponForm').on('submit', function (e) {
        e.preventDefault();
        const code = $(this).find('input[name="code"]').val();
        const $feedback = $('#couponFeedback');

        $.post('/cart/apply-coupon', { code: code }, function (res) {
            if (res.status === 'success') {
                $feedback
                    .removeClass('d-none alert-danger')
                    .addClass('alert-success')
                    .text(`✅ ${res.message}`);
                loadCartSummary();
            } else {
                $feedback
                    .removeClass('d-none alert-success')
                    .addClass('alert-danger')
                    .text(`❌ ${res.message}`);
            }
        }, 'json').fail(function () {
            $feedback
                .removeClass('d-none alert-success')
                .addClass('alert-danger')
                .text('❌ Failed to apply coupon.');
        });
    });


    updateCartCount();
});
