# Quick Reference Guide - Admin & Inventory Control

## Quick Commands

### Check System Status

```bash
# Navigate to project
cd /home/shad/Desktop/shop/shad-ecom

# Run demonstration script
./demo-admin-features.sh

# Check server status
ps aux | grep "php.*artisan serve"

# View logs in real-time
tail -f storage/logs/laravel.log
```

---

## SQL Queries for Testing

### Stock Management Queries

```sql
-- Check current stock levels
SELECT id, name, stock, price 
FROM products 
ORDER BY stock ASC 
LIMIT 10;

-- Find products in specific order
SELECT p.name, oi.quantity, p.stock as current_stock
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 1;

-- Check order payment status
SELECT id, order_number, payment_status, status, total
FROM orders
ORDER BY created_at DESC
LIMIT 10;

-- View stock change history (from logs)
SELECT 
    p.id,
    p.name,
    p.stock as current_stock,
    COUNT(oi.id) as times_ordered,
    SUM(oi.quantity) as total_quantity_sold
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
GROUP BY p.id
ORDER BY total_quantity_sold DESC;
```

### Low Stock Alert Queries

```sql
-- Get all low stock products with severity
SELECT 
    p.id,
    p.name,
    c.name as category,
    p.stock,
    p.price,
    CASE 
        WHEN p.stock = 0 THEN 'CRITICAL - OUT OF STOCK'
        WHEN p.stock <= 5 THEN 'HIGH - VERY LOW'
        WHEN p.stock <= 10 THEN 'MODERATE - LOW'
        ELSE 'OK'
    END as alert_level,
    CASE 
        WHEN p.stock = 0 THEN 1
        WHEN p.stock <= 5 THEN 2
        WHEN p.stock <= 10 THEN 3
        ELSE 4
    END as priority
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.stock <= 10
ORDER BY priority ASC, p.stock ASC;

-- Count products by alert level
SELECT 
    CASE 
        WHEN stock = 0 THEN 'OUT_OF_STOCK'
        WHEN stock <= 5 THEN 'VERY_LOW'
        WHEN stock <= 10 THEN 'LOW'
        ELSE 'OK'
    END as alert_level,
    COUNT(*) as count
FROM products
GROUP BY alert_level;

-- Products that need immediate attention
SELECT id, name, stock 
FROM products 
WHERE stock = 0;

-- Calculate potential revenue loss from out-of-stock
SELECT 
    SUM(p.price * 10) as estimated_lost_revenue
FROM products p
WHERE p.stock = 0;
```

### Invoice & Order Queries

```sql
-- Get all paid orders ready for invoicing
SELECT 
    o.id,
    o.order_number,
    o.payment_status,
    u.name as customer,
    u.email,
    o.total,
    datetime(o.created_at, 'localtime') as order_date
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.payment_status = 'paid'
ORDER BY o.created_at DESC;

-- Get invoice details for specific order
SELECT 
    o.order_number,
    o.created_at as invoice_date,
    u.name as customer_name,
    u.email as customer_email,
    o.shipping_address,
    o.shipping_city,
    o.shipping_zip,
    o.subtotal,
    o.tax,
    o.total
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.id = 1;

-- Get invoice line items
SELECT 
    p.name as product_name,
    oi.quantity,
    oi.price as unit_price,
    (oi.quantity * oi.price) as line_total
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 1;

-- Calculate total invoices issued this month
SELECT 
    COUNT(*) as invoices_issued,
    SUM(total) as total_invoiced
FROM orders
WHERE payment_status = 'paid'
AND created_at >= date('now', 'start of month');
```

---

## Testing Scenarios

### Scenario 1: Test Stock Decrement After Payment

```bash
# Step 1: Check initial stock
sqlite3 database/database.sqlite "
SELECT id, name, stock FROM products WHERE id = 38;
"

# Step 2: Check for pending order
sqlite3 database/database.sqlite "
SELECT id, order_number FROM orders WHERE payment_status = 'pending' LIMIT 1;
"

# Step 3: Login to admin dashboard
# http://127.0.0.1:8000/admin/dashboard
# Email: admin@shadecom.com
# Password: password

# Step 4: Click "Mock Payment" button

# Step 5: Verify stock reduced
sqlite3 database/database.sqlite "
SELECT id, name, stock FROM products WHERE id = 38;
"

# Step 6: Check logs
tail -20 storage/logs/laravel.log | grep "Stock reduced"
```

### Scenario 2: Test Low Stock Alert

```bash
# Step 1: Manually reduce stock to trigger alert
sqlite3 database/database.sqlite "
UPDATE products SET stock = 5 WHERE id = 1;
"

# Step 2: Verify alert appears
sqlite3 database/database.sqlite "
SELECT name, stock FROM products WHERE stock <= 10;
"

# Step 3: View in admin dashboard
# http://127.0.0.1:8000/admin/dashboard
# Should see product in "VERY LOW" section

# Step 4: Restore stock
sqlite3 database/database.sqlite "
UPDATE products SET stock = 50 WHERE id = 1;
"
```

### Scenario 3: Generate PDF Invoice

```bash
# Step 1: Ensure you have a paid order
sqlite3 database/database.sqlite "
SELECT id, order_number FROM orders WHERE payment_status = 'paid' LIMIT 1;
"

# Step 2: Access invoice URL (replace {ID} with actual order ID)
# http://127.0.0.1:8000/orders/{ID}/invoice

# Step 3: Or via web interface:
# http://127.0.0.1:8000/orders
# Click on a paid order
# Click "Download Invoice"

# Step 4: Verify PDF opens and contains:
# - Order number
# - Customer details
# - Shipping address
# - Order items
# - Subtotal, tax, total
# - All in KSh currency
```

---

## Artisan Commands

### Create Test Data

```bash
# Seed database with products
php artisan db:seed --class=ProductSeeder

# Create a test order
php artisan tinker
```

```php
// In tinker
$user = User::first();
$product = Product::first();

$order = Order::create([
    'user_id' => $user->id,
    'order_number' => 'ORD-TEST-' . strtoupper(uniqid()),
    'total' => 119.97,
    'subtotal' => 111.09,
    'tax' => 8.88,
    'status' => 'pending',
    'payment_status' => 'pending',
    'shipping_address' => '123 Test St',
    'shipping_city' => 'Nairobi',
    'shipping_zip' => '00100'
]);

$order->items()->create([
    'product_id' => $product->id,
    'quantity' => 3,
    'price' => $product->price
]);

echo "Order created: " . $order->order_number;
exit;
```

### Clear Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize for production
php artisan optimize
```

---

## Database Commands

### Access Database

```bash
# Open SQLite database
sqlite3 database/database.sqlite

# Run query and exit
sqlite3 database/database.sqlite "SELECT * FROM products LIMIT 5;"

# Export to CSV
sqlite3 -header -csv database/database.sqlite "SELECT * FROM products;" > products.csv

# Import from SQL
sqlite3 database/database.sqlite < backup.sql
```

### Backup & Restore

```bash
# Backup database
cp database/database.sqlite database/database.backup.$(date +%Y%m%d).sqlite

# Restore database
cp database/database.backup.20260126.sqlite database/database.sqlite

# Export schema
sqlite3 database/database.sqlite .schema > schema.sql

# List all tables
sqlite3 database/database.sqlite ".tables"
```

---

## Log Analysis Commands

### View Recent Activity

```bash
# All recent logs
tail -100 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Filter by keyword
grep -i "stock reduced" storage/logs/laravel.log | tail -20
grep -i "payment" storage/logs/laravel.log | tail -20
grep -i "invoice" storage/logs/laravel.log | tail -20
grep -i "error" storage/logs/laravel.log | tail -20

# Count occurrences
grep -c "Stock reduced" storage/logs/laravel.log

# Show logs from today
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log

# Show only errors
grep "\[error\]" storage/logs/laravel.log
```

### Analyze Stock Changes

```bash
# Extract stock reduction events
grep "Stock reduced" storage/logs/laravel.log | \
  grep -oP '(?<=product_id":)\d+|(?<=quantity_reduced":)\d+|(?<=remaining_stock":)\d+' | \
  paste - - - | \
  awk '{print "Product:", $1, "| Reduced:", $2, "| Remaining:", $3}'
```

---

## API Endpoints

### Order Status API

```bash
# Check order status (replace {ID} with order ID)
curl http://127.0.0.1:8000/api/orders/{ID}/status \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"

# Example response:
# {
#   "status": "paid",
#   "payment_status": "paid",
#   "updated_at": "2026-01-26T12:00:00+00:00"
# }
```

### Mock Payment Confirmation

```bash
# Simulate M-Pesa callback (internal use)
curl -X POST http://127.0.0.1:8000/api/payment/mpesa-callback \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1,
    "transaction_id": "MPESA-TEST-12345",
    "amount": 129.57,
    "status": "success"
  }'
```

---

## Performance Monitoring

### Check Response Times

```bash
# Enable query logging in .env
# DB_LOG_QUERIES=true

# Monitor slow queries
tail -f storage/logs/laravel.log | grep "slow query"

# Check database size
ls -lh database/database.sqlite
```

### Memory Usage

```bash
# Check PHP memory usage
php -r "echo 'Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;"

# Monitor during PDF generation
php artisan tinker
```

```php
// In tinker
memory_get_usage(true); // Before
$order = Order::with(['items.product', 'user'])->first();
$html = view('orders.invoice', compact('order'))->render();
$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();
memory_get_usage(true); // After
exit;
```

---

## Useful URLs

### Admin Access
- Dashboard: http://127.0.0.1:8000/admin/dashboard
- Login: admin@shadecom.com / password

### Customer Access
- Shop: http://127.0.0.1:8000/shop
- Cart: http://127.0.0.1:8000/cart
- Orders: http://127.0.0.1:8000/orders
- Checkout: http://127.0.0.1:8000/checkout

### Direct Links
- Invoice: http://127.0.0.1:8000/orders/{ID}/invoice
- Order Status API: http://127.0.0.1:8000/api/orders/{ID}/status
- Order Details: http://127.0.0.1:8000/orders/{ID}

---

## Troubleshooting

### Stock Not Decrementing

```bash
# 1. Check if payment callback reached
tail -50 storage/logs/laravel.log | grep "M-Pesa Callback"

# 2. Verify order status
sqlite3 database/database.sqlite "
SELECT id, order_number, payment_status, status 
FROM orders 
WHERE id = 1;
"

# 3. Check for errors
grep -i "error" storage/logs/laravel.log | tail -20

# 4. Verify product exists
sqlite3 database/database.sqlite "
SELECT id, name, stock FROM products WHERE id IN (
    SELECT product_id FROM order_items WHERE order_id = 1
);
"
```

### Low Stock Alerts Not Showing

```bash
# 1. Verify products have low stock
sqlite3 database/database.sqlite "
SELECT COUNT(*) FROM products WHERE stock <= 10;
"

# 2. Clear cache
php artisan cache:clear

# 3. Check admin authentication
# Ensure logged in as admin@shadecom.com

# 4. Manually set low stock for testing
sqlite3 database/database.sqlite "
UPDATE products SET stock = 3 WHERE id = 1;
"
```

### PDF Generation Fails

```bash
# 1. Check DomPDF installation
composer show dompdf/dompdf

# 2. Verify storage permissions
chmod -R 775 storage/
chown -R $USER:$USER storage/

# 3. Increase memory limit (if needed)
php -d memory_limit=512M artisan tinker

# 4. Test basic PDF generation
php artisan tinker
```

```php
// In tinker
$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml('<h1>Test</h1>');
$dompdf->render();
echo "PDF test successful!";
exit;
```

### Server Not Running

```bash
# Start server
php artisan serve

# Or specify port
php artisan serve --port=8000

# Or bind to specific host
php artisan serve --host=0.0.0.0 --port=8000

# Check if already running
ps aux | grep "php.*artisan serve"

# Kill existing process
pkill -f "php.*artisan serve"
```

---

## Database Maintenance

### Optimize Database

```bash
# Vacuum database (reclaim space)
sqlite3 database/database.sqlite "VACUUM;"

# Analyze database (update statistics)
sqlite3 database/database.sqlite "ANALYZE;"

# Check integrity
sqlite3 database/database.sqlite "PRAGMA integrity_check;"
```

### Reset Database (CAUTION!)

```bash
# Full reset
php artisan migrate:fresh --seed

# Keep migrations, re-seed only
php artisan db:seed --force
```

---

## Environment Variables

### Key Settings in .env

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Application
APP_DEBUG=true
APP_ENV=local

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Session
SESSION_DRIVER=file
```

---

## Daily Operations Checklist

### Morning Routine (Admin)

```bash
# 1. Check low stock alerts
sqlite3 database/database.sqlite "
SELECT name, stock FROM products WHERE stock <= 10;
"

# 2. Review pending orders
sqlite3 database/database.sqlite "
SELECT COUNT(*) FROM orders WHERE payment_status = 'pending';
"

# 3. Check yesterday's revenue
sqlite3 database/database.sqlite "
SELECT SUM(total) FROM orders 
WHERE payment_status = 'paid' 
AND date(created_at) = date('now', '-1 day');
"

# 4. Access admin dashboard
# http://127.0.0.1:8000/admin/dashboard
```

### Weekly Maintenance

```bash
# 1. Backup database
cp database/database.sqlite backups/database.$(date +%Y%m%d).sqlite

# 2. Rotate logs
mv storage/logs/laravel.log storage/logs/laravel.$(date +%Y%m%d).log

# 3. Check disk space
df -h .

# 4. Vacuum database
sqlite3 database/database.sqlite "VACUUM;"
```

---

## Quick Stats Commands

```bash
# Total products
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM products;"

# Total orders
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM orders;"

# Total revenue
sqlite3 database/database.sqlite "
SELECT printf('KSh %.2f', SUM(total)) 
FROM orders 
WHERE payment_status = 'paid';
"

# Top selling products
sqlite3 database/database.sqlite "
SELECT p.name, SUM(oi.quantity) as sold
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE o.payment_status = 'paid'
GROUP BY p.id
ORDER BY sold DESC
LIMIT 10;
"

# Average order value
sqlite3 database/database.sqlite "
SELECT printf('KSh %.2f', AVG(total))
FROM orders
WHERE payment_status = 'paid';
"
```

---

**Last Updated:** January 26, 2026  
**System Version:** Laravel 12.48.1 | PHP 8.4.11
