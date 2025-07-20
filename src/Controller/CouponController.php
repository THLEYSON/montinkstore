<?php

namespace App\Controllers;

use App\Models\Coupon;
use App\Support\View;
use App\Support\Flash;

class CouponController
{
    private readonly Coupon $coupon;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(): void
    {
        $coupons = $this->coupon->all();
        View::render('coupon/index', compact('coupons'), 'Coupons');
    }

    public function create(): void
    {
        View::render('coupon/form', ['coupon' => null], 'Create Coupon');
    }

    public function edit(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if (!$id) {
            Flash::error('Invalid coupon ID.');
            $this->redirect('/coupon');
        }

        $coupon = $this->coupon->find($id);

        if (!$coupon) {
            Flash::error('Coupon not found.');
            $this->redirect('/coupon');
        }

        View::render('coupon/form', compact('coupon'), 'Edit Coupon');
    }

    public function store(): void
    {
        $id = $_POST['id'] ?? null;
        $data = [
            'code'         => trim($_POST['code'] ?? ''),
            'discount'     => (float)($_POST['discount'] ?? 0),
            'expires_at'   => $_POST['expires_at'] ?? '',
            'min_subtotal' => (float)($_POST['min_subtotal'] ?? 0)
        ];

        if (!$data['code'] || $data['discount'] <= 0) {
            Flash::error('Please fill in all required fields.');
            $this->redirect('/coupon');
        }

        if ($id) {
            $this->coupon->update((int)$id, $data);
            Flash::success('Coupon updated.');
        } else {
            $this->coupon->create($data);
            Flash::success('Coupon created.');
        }

        $this->redirect('/coupon');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id) {
            $this->coupon->delete($id);
            Flash::success('Coupon deleted.');
        } else {
            Flash::error('Invalid coupon ID.');
        }

        $this->redirect('/coupon');
    }

    private function redirect(string $url): void
    {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        }
    }
}
