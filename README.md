# 🛍️ PHP MVC Inventory & Checkout System

A complete inventory and shopping cart system built with PHP, MVC architecture, Docker, and Bootstrap.

## 🚀 Features

- ✅ Product registration with name, variations, quantity, and price per variation
- ✅ Inventory control with dynamic stock management
- ✅ Update product names, prices, quantities, and variations
- ✅ Delete products and their variations
- ✅ Shopping cart (session-based)
- ✅ Freight rules based on subtotal:
  - Subtotal < R$52,00: R$20,00
  - R$52,00 ~ R$166,59: R$15,00
  - R$200,00+: **Free shipping**
- ✅ Discount coupons with expiration and subtotal rules
- ✅ Checkout with stock validation and automatic stock deduction
- ✅ Email confirmation using PHPMailer or Postmark (configurable)
- ✅ Flash messages and clean UI with Bootstrap 5

## 🧰 Technologies

- PHP 8.1+
- Apache
- MySQL
- Docker & Docker Compose
- Bootstrap 5
- MVC Architecture (custom, lightweight)

## ⚙️ How to Run

### 1. Clone the repository

```bash
git clone https://github.com/your-user/inventory-app.git
cd inventory-app
```

### 2. Start the containers

```bash
docker-compose up -d
```

Access the app: [http://localhost:8080](http://localhost:8080)

### 3. Configure the database

If you're using Docker, the MySQL database will be set up automatically with the configured volumes.  
To access it manually via **DBeaver** or any MySQL client, use the credentials from `docker-compose.yml`.

If you prefer to set it up manually, use the SQL schema below:

```sql
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variation VARCHAR(255) NOT NULL,
    quantity INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) UNIQUE,
    discount DECIMAL(10,2),
    expires_at DATE,
    min_subtotal DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10,2),
    shipping_cost DECIMAL(10,2),
    cep VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'pending'
);
```

> ✅ **Note:** If using Docker, you don't need to run the SQL manually — the database will be initialized automatically.

## 📂 Folder structure

```
src/
├── Controller/
├── Model/
├── Route/
├── Support/
├── View/
│   ├── home/
│   ├── product/
│   ├── stock/
│   ├── cart/
│   └── coupon/
└── components/
```

## 📫 Email Configuration

Checkout confirmation emails are sent to the buyer's email address.

- Default: PHPMailer (SMTP)
- Optional: Postmark integration (API key required)

Update the configuration in `src/Support/Mailer.php` as needed.

## 📄 License

MIT