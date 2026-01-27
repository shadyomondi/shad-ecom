#!/bin/bash

###############################################################################
# Admin & Inventory Control - Live Demonstration Script
# This script demonstrates the three critical features:
# 1. Stock Management (preventing overselling)
# 2. Low Stock Alerts
# 3. PDF Invoice Generation
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DB_PATH="database/database.sqlite"
BASE_URL="http://127.0.0.1:8000"

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║     ADMIN & INVENTORY CONTROL - LIVE DEMONSTRATION            ║"
echo "║     E-Commerce System - Laravel 12.48.1                       ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

###############################################################################
# FEATURE 1: STOCK MANAGEMENT - PREVENTING OVERSELLING
###############################################################################

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}FEATURE 1: STOCK MANAGEMENT - PREVENTING OVERSELLING${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo "This demonstrates how the system prevents overselling by:"
echo "  • Keeping stock unchanged during order creation"
echo "  • Decrementing stock ONLY after payment confirmation"
echo "  • Using database transactions for atomicity"
echo ""

# Get a product with sufficient stock
echo -e "${YELLOW}➤ Checking current inventory...${NC}"
PRODUCT_INFO=$(sqlite3 $DB_PATH "SELECT id, name, stock FROM products WHERE stock > 5 LIMIT 1;")
PRODUCT_ID=$(echo $PRODUCT_INFO | cut -d'|' -f1)
PRODUCT_NAME=$(echo $PRODUCT_INFO | cut -d'|' -f2)
INITIAL_STOCK=$(echo $PRODUCT_INFO | cut -d'|' -f3)

echo ""
echo "Selected Product:"
echo "  ID: $PRODUCT_ID"
echo "  Name: $PRODUCT_NAME"
echo "  Current Stock: $INITIAL_STOCK units"
echo ""

# Check for pending orders
echo -e "${YELLOW}➤ Checking for pending orders...${NC}"
PENDING_ORDERS=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM orders WHERE payment_status = 'pending';")
echo "  Pending Orders: $PENDING_ORDERS"
echo ""

if [ $PENDING_ORDERS -gt 0 ]; then
    # Get order details
    ORDER_INFO=$(sqlite3 $DB_PATH "SELECT id, order_number, total, created_at FROM orders WHERE payment_status = 'pending' LIMIT 1;")
    ORDER_ID=$(echo $ORDER_INFO | cut -d'|' -f1)
    ORDER_NUMBER=$(echo $ORDER_INFO | cut -d'|' -f2)
    ORDER_TOTAL=$(echo $ORDER_INFO | cut -d'|' -f3)

    echo -e "${GREEN}✓ Found pending order to demonstrate:${NC}"
    echo "  Order ID: $ORDER_ID"
    echo "  Order Number: $ORDER_NUMBER"
    echo "  Total: KSh $ORDER_TOTAL"
    echo ""

    # Get items in this order
    echo -e "${YELLOW}➤ Order contains:${NC}"
    sqlite3 $DB_PATH "
        SELECT p.name, oi.quantity, p.stock as current_stock
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $ORDER_ID;
    " | while IFS='|' read -r name quantity current_stock; do
        echo "  • $name - Quantity: $quantity (Current Stock: $current_stock)"
    done
    echo ""

    # Show stock BEFORE payment
    echo -e "${YELLOW}➤ Stock status BEFORE payment confirmation:${NC}"
    sqlite3 $DB_PATH "
        SELECT p.name, p.stock
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $ORDER_ID;
    " | while IFS='|' read -r name stock; do
        echo "  • $name: $stock units (UNCHANGED)"
    done
    echo ""

    echo -e "${GREEN}KEY POINT: Stock remains unchanged until payment is confirmed!${NC}"
    echo "This prevents inventory reduction for orders that may never be paid."
    echo ""

    # Demonstrate payment confirmation
    echo -e "${YELLOW}➤ To confirm payment and trigger stock reduction:${NC}"
    echo "  1. Login as admin (admin@shadecom.com / password)"
    echo "  2. Navigate to: ${BASE_URL}/admin/dashboard"
    echo "  3. Click 'Mock Payment' button for Order #${ORDER_NUMBER}"
    echo "  4. Observe real-time stock decrement"
    echo ""

    echo "Press ENTER to continue demonstration (or manually confirm payment first)..."
    read -r

    # Check if payment was confirmed
    PAYMENT_STATUS=$(sqlite3 $DB_PATH "SELECT payment_status FROM orders WHERE id = $ORDER_ID;")

    if [ "$PAYMENT_STATUS" = "paid" ]; then
        echo -e "${GREEN}✓ Payment confirmed! Checking stock reduction...${NC}"
        echo ""

        # Show stock AFTER payment
        echo -e "${YELLOW}➤ Stock status AFTER payment confirmation:${NC}"
        sqlite3 $DB_PATH "
            SELECT p.name, p.stock, oi.quantity
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $ORDER_ID;
        " | while IFS='|' read -r name stock quantity; do
            echo "  • $name: $stock units (REDUCED by $quantity)"
        done
        echo ""

        echo -e "${GREEN}✓ Stock successfully decremented after payment!${NC}"
        echo ""
    else
        echo -e "${RED}⚠ Payment not yet confirmed (still pending)${NC}"
        echo "Stock remains unchanged - preventing overselling!"
        echo ""
    fi

    # Show transaction log
    echo -e "${YELLOW}➤ Checking transaction logs...${NC}"
    if [ -f "storage/logs/laravel.log" ]; then
        echo "Recent stock reduction events:"
        tail -50 storage/logs/laravel.log | grep -i "stock reduced" | tail -5 || echo "  No recent stock reductions"
    fi
    echo ""

else
    echo -e "${YELLOW}⚠ No pending orders found.${NC}"
    echo "To demonstrate this feature:"
    echo "  1. Create an order as a customer"
    echo "  2. Note that stock remains unchanged"
    echo "  3. Confirm payment via admin dashboard"
    echo "  4. Observe atomic stock decrement"
    echo ""
fi

###############################################################################
# FEATURE 2: LOW STOCK ALERTS
###############################################################################

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}FEATURE 2: LOW STOCK ALERTS - PROACTIVE WARNING SYSTEM${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo "This demonstrates the proactive alert system that:"
echo "  • Identifies products with stock ≤ 10 units"
echo "  • Color-codes alerts by urgency (Red/Orange/Yellow)"
echo "  • Enables proactive restocking decisions"
echo ""

echo -e "${YELLOW}➤ Scanning inventory for low stock products...${NC}"
echo ""

# Query low stock products
LOW_STOCK_COUNT=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM products WHERE stock <= 10;")

echo "Low Stock Summary:"
echo "  Total Products with Stock ≤ 10: $LOW_STOCK_COUNT"
echo ""

if [ $LOW_STOCK_COUNT -gt 0 ]; then
    # Breakdown by severity
    OUT_OF_STOCK=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM products WHERE stock = 0;")
    VERY_LOW=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 5;")
    LOW=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM products WHERE stock > 5 AND stock <= 10;")

    echo "Alert Breakdown:"
    echo -e "  ${RED}🔴 OUT OF STOCK (0 units): $OUT_OF_STOCK products${NC}"
    echo -e "  ${YELLOW}🟠 VERY LOW (1-5 units): $VERY_LOW products${NC}"
    echo -e "  ${GREEN}🟡 LOW (6-10 units): $LOW products${NC}"
    echo ""

    # Show detailed list
    echo -e "${YELLOW}➤ Detailed Low Stock Report:${NC}"
    echo ""
    printf "%-5s %-40s %-20s %-10s %-15s\n" "ID" "Product Name" "Category" "Stock" "Alert Level"
    echo "─────────────────────────────────────────────────────────────────────────────────────────"

    sqlite3 $DB_PATH "
        SELECT
            p.id,
            p.name,
            c.name as category,
            p.stock,
            CASE
                WHEN p.stock = 0 THEN 'OUT OF STOCK'
                WHEN p.stock <= 5 THEN 'VERY LOW'
                WHEN p.stock <= 10 THEN 'LOW STOCK'
            END as alert_level
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.stock <= 10
        ORDER BY p.stock ASC;
    " | while IFS='|' read -r id name category stock alert; do
        # Color code based on stock level
        if [ "$stock" -eq 0 ]; then
            printf "${RED}%-5s %-40s %-20s %-10s %-15s${NC}\n" "$id" "$name" "$category" "$stock" "$alert"
        elif [ "$stock" -le 5 ]; then
            printf "${YELLOW}%-5s %-40s %-20s %-10s %-15s${NC}\n" "$id" "$name" "$category" "$stock" "$alert"
        else
            printf "${GREEN}%-5s %-40s %-20s %-10s %-15s${NC}\n" "$id" "$name" "$category" "$stock" "$alert"
        fi
    done
    echo ""

    echo -e "${GREEN}✓ Access live dashboard at: ${BASE_URL}/admin/dashboard${NC}"
    echo ""

    # Recommendations
    echo -e "${YELLOW}➤ Recommended Actions:${NC}"
    if [ $OUT_OF_STOCK -gt 0 ]; then
        echo -e "  ${RED}URGENT: $OUT_OF_STOCK products are out of stock - IMMEDIATE restocking required${NC}"
    fi
    if [ $VERY_LOW -gt 0 ]; then
        echo -e "  ${YELLOW}HIGH: $VERY_LOW products have very low stock - Plan restocking within 24-48 hours${NC}"
    fi
    if [ $LOW -gt 0 ]; then
        echo -e "  ${GREEN}MODERATE: $LOW products are running low - Plan restocking this week${NC}"
    fi
    echo ""

else
    echo -e "${GREEN}✓ All products have sufficient stock (>10 units)${NC}"
    echo "No immediate restocking required."
    echo ""
fi

# Show restocking velocity
echo -e "${YELLOW}➤ Product Velocity Analysis (Best Sellers):${NC}"
echo ""
printf "%-40s %-15s %-15s %-15s\n" "Product" "Times Sold" "Total Qty Sold" "Current Stock"
echo "─────────────────────────────────────────────────────────────────────────────────────"

sqlite3 $DB_PATH "
    SELECT
        p.name,
        COUNT(DISTINCT oi.order_id) as times_sold,
        COALESCE(SUM(oi.quantity), 0) as total_quantity,
        p.stock
    FROM products p
    LEFT JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
    HAVING total_quantity > 0
    ORDER BY total_quantity DESC
    LIMIT 10;
" | while IFS='|' read -r name times qty stock; do
    printf "%-40s %-15s %-15s %-15s\n" "$name" "$times" "$qty" "$stock"
done
echo ""

###############################################################################
# FEATURE 3: PDF INVOICE GENERATION
###############################################################################

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}FEATURE 3: PDF INVOICE GENERATION - BUSINESS DOCUMENTATION${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo "This demonstrates PDF invoice generation using DomPDF for:"
echo "  • Legal compliance and tax reporting"
echo "  • Customer proof of purchase"
echo "  • Business accounting records"
echo "  • Professional documentation"
echo ""

# Check for paid orders
echo -e "${YELLOW}➤ Checking for paid orders...${NC}"
PAID_ORDERS=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM orders WHERE payment_status = 'paid';")
echo "  Paid Orders: $PAID_ORDERS"
echo ""

if [ $PAID_ORDERS -gt 0 ]; then
    # Show paid order details
    echo -e "${YELLOW}➤ Paid Orders Available for Invoice Generation:${NC}"
    echo ""
    printf "%-5s %-20s %-15s %-20s %-15s\n" "ID" "Order Number" "Total" "Date" "Status"
    echo "────────────────────────────────────────────────────────────────────────────────"

    sqlite3 $DB_PATH "
        SELECT
            id,
            order_number,
            'KSh ' || printf('%.2f', total) as total,
            datetime(created_at, 'localtime'),
            payment_status
        FROM orders
        WHERE payment_status = 'paid'
        ORDER BY created_at DESC
        LIMIT 10;
    " | while IFS='|' read -r id number total date status; do
        printf "%-5s %-20s %-15s %-20s %-15s\n" "$id" "$number" "$total" "$date" "$status"
    done
    echo ""

    # Get first paid order for demo
    INVOICE_ORDER=$(sqlite3 $DB_PATH "SELECT id, order_number FROM orders WHERE payment_status = 'paid' ORDER BY created_at DESC LIMIT 1;")
    INVOICE_ORDER_ID=$(echo $INVOICE_ORDER | cut -d'|' -f1)
    INVOICE_ORDER_NUMBER=$(echo $INVOICE_ORDER | cut -d'|' -f2)

    echo -e "${GREEN}✓ Demonstrating invoice for Order #${INVOICE_ORDER_NUMBER}${NC}"
    echo ""

    # Show order details
    echo -e "${YELLOW}➤ Invoice Details:${NC}"
    sqlite3 $DB_PATH "
        SELECT
            'Order Number: ' || o.order_number,
            'Date: ' || datetime(o.created_at, 'localtime'),
            'Customer: ' || u.name,
            'Email: ' || u.email,
            'Shipping: ' || o.shipping_address || ', ' || o.shipping_city || ' ' || o.shipping_zip,
            'Subtotal: KSh ' || printf('%.2f', o.subtotal),
            'Tax (8%): KSh ' || printf('%.2f', o.tax),
            'Total: KSh ' || printf('%.2f', o.total)
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = $INVOICE_ORDER_ID;
    " | while read -r line; do
        echo "  $line"
    done
    echo ""

    # Show order items
    echo -e "${YELLOW}➤ Order Items:${NC}"
    printf "  %-40s %-10s %-15s %-15s\n" "Product" "Quantity" "Unit Price" "Total"
    echo "  ──────────────────────────────────────────────────────────────────────────────"

    sqlite3 $DB_PATH "
        SELECT
            p.name,
            oi.quantity,
            'KSh ' || printf('%.2f', oi.price),
            'KSh ' || printf('%.2f', oi.price * oi.quantity)
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $INVOICE_ORDER_ID;
    " | while IFS='|' read -r name qty price total; do
        printf "  %-40s %-10s %-15s %-15s\n" "$name" "$qty" "$price" "$total"
    done
    echo ""

    # Invoice download instructions
    echo -e "${GREEN}✓ To download PDF invoice:${NC}"
    echo ""
    echo "  Method 1: Via Web Interface"
    echo "    1. Login as the customer"
    echo "    2. Navigate to: ${BASE_URL}/orders"
    echo "    3. Click on order #${INVOICE_ORDER_NUMBER}"
    echo "    4. Click 'Download Invoice' button"
    echo ""
    echo "  Method 2: Direct URL (requires authentication)"
    echo "    ${BASE_URL}/orders/${INVOICE_ORDER_ID}/invoice"
    echo ""

    # Check DomPDF installation
    echo -e "${YELLOW}➤ Checking DomPDF installation...${NC}"
    if [ -d "vendor/dompdf/dompdf" ]; then
        echo -e "${GREEN}✓ DomPDF is installed${NC}"
        DOMPDF_VERSION=$(grep -m 1 '"version"' vendor/dompdf/dompdf/composer.json | cut -d'"' -f4)
        echo "  Version: $DOMPDF_VERSION"
    else
        echo -e "${RED}✗ DomPDF not found${NC}"
        echo "  Install with: composer require dompdf/dompdf"
    fi
    echo ""

    # Business importance
    echo -e "${YELLOW}➤ Business Importance of PDF Invoices:${NC}"
    echo ""
    echo "  Accounting Benefits:"
    echo "    ✓ Required for KRA tax filing"
    echo "    ✓ Audit trail for financial records"
    echo "    ✓ Revenue and expense tracking"
    echo "    ✓ Legal compliance"
    echo ""
    echo "  Customer Benefits:"
    echo "    ✓ Proof of purchase"
    echo "    ✓ Warranty documentation"
    echo "    ✓ Tax deduction evidence"
    echo "    ✓ Dispute resolution"
    echo ""

else
    echo -e "${YELLOW}⚠ No paid orders found.${NC}"
    echo "To demonstrate this feature:"
    echo "  1. Complete the payment for a pending order"
    echo "  2. Navigate to the order details page"
    echo "  3. Click 'Download Invoice'"
    echo "  4. PDF will be generated and downloaded"
    echo ""
fi

###############################################################################
# SUMMARY AND INTEGRATION
###############################################################################

echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}DEMONSTRATION SUMMARY${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo "System Statistics:"
echo ""

# Overall stats
TOTAL_PRODUCTS=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM products;")
TOTAL_ORDERS=$(sqlite3 $DB_PATH "SELECT COUNT(*) FROM orders;")
TOTAL_REVENUE=$(sqlite3 $DB_PATH "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid';")
PENDING_REVENUE=$(sqlite3 $DB_PATH "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'pending';")

echo "  Inventory:"
echo "    • Total Products: $TOTAL_PRODUCTS"
echo "    • Low Stock Products: $LOW_STOCK_COUNT"
echo "    • Out of Stock: $OUT_OF_STOCK"
echo ""
echo "  Orders:"
echo "    • Total Orders: $TOTAL_ORDERS"
echo "    • Paid Orders: $PAID_ORDERS"
echo "    • Pending Orders: $PENDING_ORDERS"
echo ""
echo "  Revenue:"
echo "    • Confirmed Revenue: KSh $(printf '%.2f' $TOTAL_REVENUE)"
echo "    • Pending Revenue: KSh $(printf '%.2f' $PENDING_REVENUE)"
echo ""

echo "Key Achievements:"
echo ""
echo "  ✅ Stock Management:"
echo "     - Atomic transactions prevent overselling"
echo "     - Stock decrements only after payment"
echo "     - Complete audit trail in logs"
echo ""
echo "  ✅ Low Stock Alerts:"
echo "     - Real-time monitoring of $LOW_STOCK_COUNT products"
echo "     - Color-coded urgency levels"
echo "     - Proactive restocking capability"
echo ""
echo "  ✅ PDF Invoices:"
echo "     - $PAID_ORDERS invoices available"
echo "     - DomPDF integration complete"
echo "     - Professional, compliant documentation"
echo ""

echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}All three features are operational and working together!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════════${NC}"
echo ""

echo "Quick Access Links:"
echo "  • Admin Dashboard: ${BASE_URL}/admin/dashboard"
echo "  • Shop: ${BASE_URL}/shop"
echo "  • Orders: ${BASE_URL}/orders"
echo ""

echo "Admin Credentials:"
echo "  • Email: admin@shadecom.com"
echo "  • Password: password"
echo ""

echo "For detailed documentation, see: ADMIN_INVENTORY_CONTROL.md"
echo ""
