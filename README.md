# 🛍️ PHP MVC Inventory System

A basic inventory and product management system using PHP, MVC, Docker, and Bootstrap.

## 🚀 Features

- Product registration with name, variations, quantity, and price per variation
- Inventory control with stock updates
- Update product names, prices, and variations
- Delete products and their variations
- Flash messages and user-friendly interface with Bootstrap

## 🧰 Technologies

- PHP 8.1+
- Apache
- MySQL
- Docker & Docker Compose
- Bootstrap 5
- MVC Architecture

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

### 3. Configure o banco de dados

O banco de dados é configurado automaticamente ao subir o ambiente Docker. Ao executar o comando `docker-compose up -d`, o MySQL será iniciado e o banco estará disponível para acesso via **DBeaver** (ou outro cliente MySQL).

Se quiser criar as tabelas manualmente (caso não esteja usando Docker), utilize o SQL abaixo:

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

> ✅ **Observação:** Ao iniciar o projeto com Docker, o banco será automaticamente criado e acessível pelo DBeaver através das credenciais configuradas no `docker-compose.yml`.

## 📂 Folder structure

```
src/
├── Controller/
├── Model/
├── Route/
├── Support/
├── View/
│   ├── home/
│   ├── products/
│   └── stock/
└── components/
```

## 📄 License

MIT