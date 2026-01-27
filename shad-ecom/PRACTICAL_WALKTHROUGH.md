# Practical Walkthrough - Admin & Inventory Control Features

This guide walks you through testing all three features step-by-step with actual demonstrations.

---

## Prerequisites

✅ Server running: `php artisan serve` at http://127.0.0.1:8000  
✅ Database seeded with products  
✅ Admin account: admin@shadecom.com / password  
✅ Customer account created (or register a new one)

---

## Walkthrough 1: Stock Management - Preventing Overselling

### Goal
Demonstrate that stock **ONLY** decrements after payment confirmation, not during order creation.

### Steps

#### 1. Check Initial Stock

**Terminal:**
```bash
cd /home/shad/Desktop/shop/shad-ecom

# Check stock of a specific product
sqlite3 database/database.sqlite "
SELECT id, name, stock, price 
FROM products 
WHERE id = 38;
"
```

**Expected Output:**
```
38|Dress Shirt|63|39.99
```

📝 **Note:** Current stock is **63 units**

---

#### 2. Create Order as Customer

**Browser (Incognito/Private Window):**

1. Go to: http://127.0.0.1:8000
2. Click "Shop" in navigation
3. Find "Dress Shirt" product
4. Click "Add to Cart"
5. Enter quantity: **2**
6. Click "Add to Cart"
7. Click cart icon (top-right)
8. Click "Proceed to Checkout"
9. Fill shipping information:
   - Address: 123 Test Street
   - City: Nairobi
   - Postal Code: 00100
10. Click "Place Order"
11. You'll see M-Pesa payment notice

📝 **Note the Order Number** shown on success page (e.g., ORD-XXXXX)

---

#### 3. Verify Stock UNCHANGED After Order Creation

**Terminal:**
```bash
# Check stock again
sqlite3 database/database.sqlite "
SELECT id, name, stock, price 
FROM products 
WHERE id = 38;
"
```

**Expected Output:**
```
38|Dress Shirt|63|39.99
```

✅ **KEY POINT:** Stock is still **63 units** - unchanged!

**Also verify in database:**
```bash
sqlite3 database/database.sqlite "
SELECT order_number, payment_status, status 
FROM orders 
ORDER BY created_at DESC 
LIMIT 1;
"
```

**Expected Output:**
```
ORD-XXXXX|pending|pending
```

✅ Order exists but stock not affected yet!

---

#### 4. Confirm Payment via Admin Dashboard

**Browser (New Tab):**

1. Go to: http://127.0.0.1:8000/admin/dashboard
2. Login as admin:
   - Email: admin@shadecom.com
   - Password: password
3. Scroll to "Pending Payments" section
4. Find your order (ORD-XXXXX)
5. Click green **"Mock Payment"** button
6. See success message: "Payment confirmed..."

---

#### 5. Verify Stock DECREMENTED After Payment

**Terminal:**
```bash
# Check stock immediately after payment
sqlite3 database/database.sqlite "
SELECT id, name, stock, price 
FROM products 
WHERE id = 38;
"
```

**Expected Output:**
```
38|Dress Shirt|61|39.99
```

✅ **SUCCESS:** Stock decreased from **63 → 61** (reduced by 2)

**Verify order status:**
```bash
sqlite3 database/database.sqlite "
SELECT order_number, payment_status, status 
FROM orders 
ORDER BY created_at DESC 
LIMIT 1;
"
```

**Expected Output:**
```
ORD-XXXXX|paid|paid
```

✅ Payment confirmed!

---

#### 6. Check Audit Logs

**Terminal:**
```bash
# View stock reduction log
tail -30 storage/logs/laravel.log | grep "Stock reduced"
```

**Expected Output:**
```json
[2026-01-26 12:34:56] local.INFO: Stock reduced {
  "product_id": 38,
  "quantity_reduced": 2,
  "remaining_stock": 61
}
```

✅ Complete audit trail recorded!

---

### Summary: Stock Management

| Step | Stock Level | Order Status | Payment Status |
|------|-------------|--------------|----------------|
| Initial | 63 units | None | - |
| After Order Created | **63 units** ⏸️ | pending | pending |
| After Payment | **61 units** ✅ | paid | paid |

**Conclusion:** Stock **ONLY** changes after successful payment, preventing overselling!

---

## Walkthrough 2: Low Stock Alerts

### Goal
Demonstrate the proactive alert system that warns admins about low inventory.

### Steps

#### 1. Check Current Alert Status

**Browser:**
1. Go to: http://127.0.0.1:8000/admin/dashboard
2. Scroll to "Low Stock Alerts" section
3. **Initially:** May show "All products have sufficient stock"

---

#### 2. Create Low Stock Situation

**Terminal:**
```bash
# Manually reduce stock to trigger alerts
sqlite3 database/database.sqlite "
UPDATE products SET stock = 3 WHERE id = 1;
UPDATE products SET stock = 8 WHERE id = 2;
UPDATE products SET stock = 0 WHERE id = 3;
"

# Verify changes
sqlite3 database/database.sqlite "
SELECT id, name, stock FROM products WHERE id IN (1,2,3);
"
```

**Expected Output:**
```
1|Wireless Bluetooth Headphones|3
2|Smart Watch Fitness Tracker|8
3|USB-C Cable Fast Charge|0
```

---

#### 3. View Alerts in Admin Dashboard

**Browser:**
1. Refresh: http://127.0.0.1:8000/admin/dashboard
2. Scroll to "Low Stock Alerts" section

**Expected Display:**

```
╔══════════════════════════════════════════════════════════╗
║               LOW STOCK ALERTS                           ║
╠══════════════════════════════════════════════════════════╣
║ Product                    │ Category    │ Stock │ Alert ║
╠═══════════════════════════════════════════════════════════╣
║ USB-C Cable                │ Electronics │   0   │ 🔴 OUT║
║ Wireless Headphones        │ Electronics │   3   │ 🟠 LOW║
║ Smart Watch                │ Electronics │   8   │ 🟡 LOW║
╚══════════════════════════════════════════════════════════╝
```

**Color Coding:**
- 🔴 **Red (0 units)**: CRITICAL - Immediate action
- 🟠 **Orange (1-5 units)**: HIGH - Urgent restocking
- 🟡 **Yellow (6-10 units)**: MODERATE - Plan restocking

---

#### 4. Query Alert Breakdown

**Terminal:**
```bash
# Get detailed breakdown
sqlite3 database/database.sqlite "
SELECT 
    CASE 
        WHEN stock = 0 THEN 'OUT_OF_STOCK'
        WHEN stock <= 5 THEN 'VERY_LOW'
        WHEN stock <= 10 THEN 'LOW_STOCK'
    END as alert_level,
    COUNT(*) as count,
    GROUP_CONCAT(name, ', ') as products
FROM products
WHERE stock <= 10
GROUP BY alert_level;
"
```

**Expected Output:**
```
OUT_OF_STOCK|1|USB-C Cable Fast Charge
VERY_LOW|1|Wireless Bluetooth Headphones
LOW_STOCK|1|Smart Watch Fitness Tracker
```

---

#### 5. Simulate Restocking

**Terminal:**
```bash
# Admin decides to restock critical items
sqlite3 database/database.sqlite "
UPDATE products SET stock = 50 WHERE id = 3;  -- Restock USB-C Cable
UPDATE products SET stock = 30 WHERE id = 1;  -- Restock Headphones
"

# Verify restock
sqlite3 database/database.sqlite "
SELECT id, name, stock FROM products WHERE id IN (1,3);
"
```

**Expected Output:**
```
1|Wireless Bluetooth Headphones|30
3|USB-C Cable Fast Charge|50
```

---

#### 6. Verify Alerts Cleared

**Browser:**
1. Refresh: http://127.0.0.1:8000/admin/dashboard
2. Check "Low Stock Alerts" section

**Expected:** Only Smart Watch (8 units) remains in alerts

---

### Summary: Low Stock Alerts

| Product | Initial Stock | Alert Level | Action Taken | Final Stock |
|---------|--------------|-------------|--------------|-------------|
| USB-C Cable | 0 | 🔴 CRITICAL | Restocked | 50 ✅ |
| Headphones | 3 | 🟠 HIGH | Restocked | 30 ✅ |
| Smart Watch | 8 | 🟡 MODERATE | Monitoring | 8 ⚠️ |

**Conclusion:** Admin receives proactive alerts enabling timely restocking decisions!

---

## Walkthrough 3: PDF Invoice Generation

### Goal
Generate professional PDF invoices for paid orders using DomPDF.

### Steps

#### 1. Ensure You Have a Paid Order

**Terminal:**
```bash
# Check for paid orders
sqlite3 database/database.sqlite "
SELECT id, order_number, total, payment_status 
FROM orders 
WHERE payment_status = 'paid' 
ORDER BY created_at DESC 
LIMIT 1;
"
```

**If no paid orders:** Complete Walkthrough 1 first to create a paid order.

**Expected Output:**
```
1|ORD-69775EE8053B9|129.57|paid
```

📝 **Note the Order ID** (e.g., 1)

---

#### 2. Access Order as Customer

**Browser:**
1. Login as the customer who placed the order
2. Go to: http://127.0.0.1:8000/orders
3. Click on the order with status "Paid"

**You should see:**
- Order number
- Order items
- Shipping details
- Payment status: **Paid** (green badge)
- **"Download Invoice"** button (visible only for paid orders)

---

#### 3. Generate PDF Invoice

**Browser:**
1. On the order details page
2. Click **"Download Invoice"** button
3. Browser will open/download PDF: `invoice-ORD-XXXXX.pdf`

---

#### 4. Verify PDF Content

**Open the PDF and verify it contains:**

✅ **Header Section:**
- Company name: SHADECOM E-COMMERCE
- Company address and contact info

✅ **Invoice Details:**
- Invoice Number: ORD-XXXXX
- Invoice Date
- Payment Status: Paid
- Order Status: Paid

✅ **Customer Information:**
- Bill To: Customer name and email
- Ship To: Shipping address, city, postal code

✅ **Order Items Table:**
- Product names
- Quantities
- Unit prices (in KSh)
- Line totals (in KSh)

✅ **Summary Section:**
- Subtotal: KSh XXX.XX
- Tax (8%): KSh XX.XX
- **Total: KSh XXX.XX**

✅ **Footer:**
- Payment Terms: M-Pesa
- Thank you message

---

#### 5. Verify Invoice via Direct URL

**Browser:**
1. Copy the order ID from previous query (e.g., 1)
2. Navigate to: http://127.0.0.1:8000/orders/1/invoice
3. PDF should generate and display

**Security Test:**
- Try accessing another user's invoice (should get 403 Forbidden)
- Try accessing without login (should redirect to login)

---

#### 6. Check Invoice Details in Database

**Terminal:**
```bash
# Get complete invoice data
sqlite3 database/database.sqlite "
SELECT 
    'Order: ' || o.order_number,
    'Customer: ' || u.name,
    'Email: ' || u.email,
    'Total: KSh ' || printf('%.2f', o.total),
    'Items: ' || COUNT(oi.id)
FROM orders o
JOIN users u ON o.user_id = u.id
JOIN order_items oi ON oi.order_id = o.id
WHERE o.id = 1
GROUP BY o.id;
"
```

**Expected Output:**
```
Order: ORD-69775EE8053B9
Customer: John Doe
Email: john@example.com
Total: KSh 129.57
Items: 2
```

---

#### 7. Test Invoice with Multiple Line Items

**Terminal:**
```bash
# Check invoice line items
sqlite3 database/database.sqlite "
SELECT 
    p.name as product,
    oi.quantity,
    'KSh ' || printf('%.2f', oi.price) as unit_price,
    'KSh ' || printf('%.2f', oi.quantity * oi.price) as total
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = 1;
"
```

**Expected Output:**
```
Dress Shirt|2|KSh 39.99|KSh 79.98
Tie Silk|1|KSh 39.99|KSh 39.99
```

✅ All items properly formatted!

---

#### 8. Verify DomPDF Installation

**Terminal:**
```bash
# Check DomPDF version
composer show dompdf/dompdf
```

**Expected Output:**
```
name     : dompdf/dompdf
descrip. : DOMPDF is a CSS 2.1 compliant HTML to PDF converter
versions : * 3.0.x
```

✅ DomPDF properly installed!

---

### Summary: PDF Invoice Generation

| Component | Status | Details |
|-----------|--------|---------|
| DomPDF Installed | ✅ | Version 3.0.x |
| Invoice Generated | ✅ | PDF created successfully |
| Content Complete | ✅ | All order details present |
| Calculations Correct | ✅ | Subtotal + Tax = Total |
| Security Working | ✅ | Authorization checks pass |
| Format Professional | ✅ | A4 layout, proper styling |

**Conclusion:** Professional, compliant PDF invoices generated for business accounting!

---

## Complete Integration Test

### Scenario: Real-World Order Flow

Let's walk through a complete customer journey demonstrating all three features working together.

---

#### Phase 1: Customer Places Order

**Browser (Customer - Incognito):**

1. Browse shop: http://127.0.0.1:8000/shop
2. Add products to cart:
   - Smart Watch (Qty: 2)
   - Wireless Headphones (Qty: 1)
3. Proceed to checkout
4. Fill shipping info
5. Click "Place Order"

**Terminal (Check Status):**
```bash
# Get latest order
ORDER_ID=$(sqlite3 database/database.sqlite "SELECT id FROM orders ORDER BY created_at DESC LIMIT 1;")
echo "Order ID: $ORDER_ID"

# Check order status
sqlite3 database/database.sqlite "
SELECT order_number, payment_status, total 
FROM orders 
WHERE id = $ORDER_ID;
"

# Check stock UNCHANGED
sqlite3 database/database.sqlite "
SELECT p.name, p.stock 
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = $ORDER_ID;
"
```

📊 **Status:** Order pending, stock unchanged

---

#### Phase 2: Admin Monitors System

**Browser (Admin):**

1. Go to: http://127.0.0.1:8000/admin/dashboard
2. **Check Pending Payments:**
   - See new order in list
   - Order total visible
3. **Check Low Stock Alerts:**
   - Note if any products approaching threshold
4. **Review Statistics:**
   - Pending revenue updated

---

#### Phase 3: Payment Confirmed

**Browser (Admin):**

1. In "Pending Payments" section
2. Click "Mock Payment" for the order
3. See success message

**Terminal (Verify Changes):**
```bash
# Check order status changed
sqlite3 database/database.sqlite "
SELECT order_number, payment_status, status 
FROM orders 
WHERE id = $ORDER_ID;
"

# Check stock DECREMENTED
sqlite3 database/database.sqlite "
SELECT 
    p.name, 
    oi.quantity as ordered,
    p.stock as remaining
FROM order_items oi
JOIN products p ON oi.product_id = p.id
WHERE oi.order_id = $ORDER_ID;
"

# Check logs
tail -20 storage/logs/laravel.log | grep "Stock reduced"
```

📊 **Status:** Order paid, stock decremented, logs recorded

---

#### Phase 4: Low Stock Alert Triggered

**Browser (Admin):**

1. Refresh dashboard
2. **Check if any products now in low stock:**
   - If Smart Watch stock now ≤ 10, alert appears
   - Alert color indicates urgency

**Terminal:**
```bash
# Check which products triggered alerts
sqlite3 database/database.sqlite "
SELECT name, stock,
    CASE 
        WHEN stock = 0 THEN 'OUT OF STOCK'
        WHEN stock <= 5 THEN 'VERY LOW'
        WHEN stock <= 10 THEN 'LOW STOCK'
    END as alert
FROM products
WHERE stock <= 10;
"
```

📊 **Status:** Admin aware of low stock, can plan restocking

---

#### Phase 5: Customer Downloads Invoice

**Browser (Customer):**

1. Login as customer
2. Go to: http://127.0.0.1:8000/orders
3. Click on the paid order
4. See order status: **Paid** ✅
5. Click "Download Invoice"
6. PDF opens/downloads

**Verify PDF Contents:**
- ✅ All order details
- ✅ Correct calculations
- ✅ Professional formatting
- ✅ Ready for accounting

📊 **Status:** Invoice generated for customer records

---

#### Phase 6: Business Reporting

**Terminal:**
```bash
# Generate daily report
echo "=== Daily Business Report ==="
echo ""

# Total revenue
echo "Total Revenue (Paid Orders):"
sqlite3 database/database.sqlite "
SELECT 'KSh ' || printf('%.2f', SUM(total)) 
FROM orders 
WHERE payment_status = 'paid';
"
echo ""

# Pending revenue
echo "Pending Revenue:"
sqlite3 database/database.sqlite "
SELECT 'KSh ' || printf('%.2f', SUM(total)) 
FROM orders 
WHERE payment_status = 'pending';
"
echo ""

# Orders today
echo "Orders Today:"
sqlite3 database/database.sqlite "
SELECT COUNT(*) 
FROM orders 
WHERE date(created_at) = date('now');
"
echo ""

# Low stock count
echo "Products Needing Attention:"
sqlite3 database/database.sqlite "
SELECT COUNT(*) 
FROM products 
WHERE stock <= 10;
"
echo ""

# Invoices issued
echo "Invoices Issued:"
sqlite3 database/database.sqlite "
SELECT COUNT(*) 
FROM orders 
WHERE payment_status = 'paid';
"
```

---

### Final Summary: Integration Test

```
┌────────────────────────────────────────────────────────┐
│           COMPLETE ORDER LIFECYCLE SUMMARY             │
├────────────────────────────────────────────────────────┤
│                                                        │
│  ✅ Order Created      → Stock Unchanged (Protection)  │
│  ✅ Payment Confirmed  → Stock Decremented (Atomicity) │
│  ✅ Alert Triggered    → Admin Notified (Proactive)    │
│  ✅ Invoice Generated  → Documentation Ready (Compliance)│
│  ✅ Logs Recorded      → Audit Trail Complete         │
│                                                        │
│  RESULT: Fully functional e-commerce system with:     │
│  • Inventory protection                               │
│  • Real-time monitoring                               │
│  • Professional documentation                         │
│                                                        │
└────────────────────────────────────────────────────────┘
```

---

## Cleanup (Optional)

If you want to reset the system for another test:

```bash
# Backup current database
cp database/database.sqlite database/backup-$(date +%Y%m%d-%H%M%S).sqlite

# Reset database
php artisan migrate:fresh --seed

# Clear logs
> storage/logs/laravel.log

# Clear cache
php artisan cache:clear
```

---

## Next Steps

1. **Extend Stock Management:**
   - Add stock reservation during checkout
   - Implement automatic reorder points
   - Create stock adjustment interface

2. **Enhance Alerts:**
   - Email notifications to warehouse manager
   - SMS alerts for critical stock levels
   - Historical trend analysis

3. **Improve Invoices:**
   - Save PDFs to storage for archival
   - Email invoices automatically
   - Add company logo and branding
   - Support for credit notes/refunds

---

**Walkthrough Completed:** January 26, 2026  
**System Version:** Laravel 12.48.1 | PHP 8.4.11  
**Status:** All features operational ✅
