# Admin Product Management Guide

This guide explains how administrators can manage products, upload images, set prices, manage categories, and monitor low stock alerts in the e-commerce system.

---

## Table of Contents

1. [Accessing Admin Panel](#accessing-admin-panel)
2. [Product Management Overview](#product-management-overview)
3. [Adding New Products](#adding-new-products)
4. [Editing Products](#editing-products)
5. [Managing Categories](#managing-categories)
6. [Monitoring Low Stock Alerts](#monitoring-low-stock-alerts)
7. [Best Practices](#best-practices)

---

## Accessing Admin Panel

### Login as Admin

1. **Navigate to the site**: http://127.0.0.1:8000
2. **Click on the user icon** (top-right corner)
3. **Login with admin credentials**:
   - Email: `admin@shadecom.com`
   - Password: `password`

### Access Product Management

After logging in, you have **three ways** to access product management:

#### Method 1: Via User Dropdown Menu
1. Click the **user icon** (top-right)
2. Select **"Manage Products"** from dropdown

#### Method 2: Via Admin Dashboard
1. Click **"Admin"** in the main navigation
2. On the dashboard, scroll to **"Quick Actions"**
3. Click **"Manage Products"** button

#### Method 3: Direct URL
- Navigate to: http://127.0.0.1:8000/admin/products

---

## Product Management Overview

The product management interface at `/admin/products` displays all products with:

```
┌────────────────────────────────────────────────────────────────┐
│                      MANAGE PRODUCTS                           │
│                                        [Add New Product Button]│
├────────────────────────────────────────────────────────────────┤
│ Image  │ Name    │ Category    │ Price      │ Stock │ Actions │
├────────────────────────────────────────────────────────────────┤
│ [IMG]  │ Product │ Electronics │ KSh 39.99  │  50   │ Edit    │
│        │  Name   │             │            │       │ Delete  │
├────────────────────────────────────────────────────────────────┤
│ [IMG]  │ Product │ Fashion     │ KSh 29.99  │   3   │ Edit    │
│        │  Name   │             │            │  ⚠️   │ Delete  │
└────────────────────────────────────────────────────────────────┘

Pagination: [Previous] 1 2 3 [Next]
```

### Stock Status Indicators

Products display color-coded stock indicators:

- 🟢 **Green** (>10 units): Healthy stock
- 🟡 **Yellow** (1-10 units): Low stock warning
- 🔴 **Red** (0 units): Out of stock

---

## Adding New Products

### Step-by-Step Guide

#### 1. Navigate to Create Product Page

- Click **"Add New Product"** button at top of products list
- Or go to: http://127.0.0.1:8000/admin/products/create

#### 2. Fill in Product Information

The create form requires the following fields:

##### **Required Fields**

| Field | Description | Example |
|-------|-------------|---------|
| **Product Name** | Full name of the product | "Wireless Bluetooth Headphones" |
| **Category** | Select from dropdown | "Electronics" |
| **Price** | Selling price in KSh | 2999.00 |
| **Stock Quantity** | Number of units available | 50 |

##### **Optional Fields**

| Field | Description | Example |
|-------|-------------|---------|
| **Original Price** | For showing discounts | 3999.00 |
| **Description** | Detailed product info | "High-quality wireless headphones..." |
| **Product Image** | Upload image file | headphones.jpg (max 2MB) |
| **Featured** | Checkbox for featured products | ☑️ Featured |

#### 3. Upload Product Image

**Image Requirements:**
- **Formats**: JPEG, PNG, JPG, GIF
- **Maximum Size**: 2 MB
- **Recommended Resolution**: 800x800px (square)
- **Aspect Ratio**: 1:1 recommended

**Steps to Upload:**
1. Click **"Choose File"** button
2. Select image from your computer
3. Preview will show if supported by browser
4. Image is uploaded when you click "Create Product"

**Where Images are Stored:**
- Path: `storage/app/public/products/`
- Accessible via: `public/storage/products/`

#### 4. Set Price

**Pricing Features:**
- **Regular Price**: Main selling price (required)
- **Original Price**: For showing discounts (optional)
- **Discount Display**: If original price > regular price, shows discount badge

**Example:**
```
Original Price: KSh 3,999.00
Regular Price:  KSh 2,999.00
→ Shows: "25% OFF" badge on product card
```

#### 5. Set Stock Quantity

**Stock Management:**
- Enter initial stock quantity
- Can be 0 for "Coming Soon" products
- Stock decrements automatically after successful payments
- Low stock alerts trigger when ≤ 10 units

#### 6. Select Category

**Available Categories** (from seeded database):
- Electronics
- Fashion
- Home & Kitchen
- Beauty
- Sports & Outdoors

**Note:** Categories are pre-defined. See [Managing Categories](#managing-categories) for adding new ones.

#### 7. Save the Product

1. Review all information
2. Click **"Create Product"** button
3. Success message appears
4. Redirected to products list
5. New product appears at top of list

---

## Editing Products

### Update Existing Products

#### 1. Navigate to Edit Page

**From Products List:**
1. Find the product you want to edit
2. Click **"Edit"** button in the Actions column

**Direct URL:**
- http://127.0.0.1:8000/admin/products/{id}/edit

#### 2. Modify Product Details

All fields can be updated:
- ✅ Product name
- ✅ Category
- ✅ Description
- ✅ Price
- ✅ Original price
- ✅ Stock quantity
- ✅ Product image
- ✅ Featured status

#### 3. Update Product Image

**To Change Image:**
1. Click **"Choose File"**
2. Select new image
3. Old image is automatically deleted when you save
4. New image is uploaded and stored

**To Keep Existing Image:**
- Simply don't select a new file
- Existing image remains unchanged

#### 4. Update Stock Quantity

**Important Notes:**
- ⚠️ Stock updates here are **manual overrides**
- Automatic decrements happen via payment confirmations
- Use this to:
  - Add restocked inventory
  - Correct stock discrepancies
  - Mark items as unavailable (set to 0)

**Example Stock Update Scenarios:**

| Scenario | Current Stock | Update To | Reason |
|----------|--------------|-----------|--------|
| Restock | 3 | 53 | +50 units received from supplier |
| Correction | 15 | 12 | 3 units damaged, removed from inventory |
| Clearance | 8 | 0 | Discontinuing product |

#### 5. Save Changes

1. Review all modifications
2. Click **"Update Product"** button
3. Success message confirms update
4. Redirected to products list

---

## Managing Categories

### View Existing Categories

Categories are used to organize products. Currently available categories are defined in the database seeder.

#### Current Categories

```sql
-- View categories in database
sqlite3 database/database.sqlite "SELECT id, name, icon FROM categories;"
```

**Default Categories:**
1. Electronics (icon: devices)
2. Fashion (icon: apparel)
3. Home & Kitchen
4. Beauty
5. Sports & Outdoors

### Adding New Categories

Currently, categories are managed through the database or seeders. To add a new category:

#### Method 1: Via Database (Quick)

```bash
# Access database
sqlite3 database/database.sqlite

# Add new category
INSERT INTO categories (name, icon, created_at, updated_at) 
VALUES ('Books', 'book', datetime('now'), datetime('now'));

# Verify
SELECT * FROM categories;

# Exit
.quit
```

#### Method 2: Via Seeder (Recommended)

1. Edit the seeder:
```bash
nano database/seeders/DatabaseSeeder.php
```

2. Add new category in the categories array:
```php
Category::create([
    'name' => 'Books',
    'icon' => 'book', // Material Symbols icon name
]);
```

3. Refresh database:
```bash
php artisan migrate:fresh --seed
```

### Category Icons

Categories use **Material Symbols** icons. Common icons:

| Category | Icon Name |
|----------|-----------|
| Electronics | `devices` |
| Fashion | `apparel` |
| Books | `book` |
| Food | `restaurant` |
| Toys | `toys` |
| Music | `music_note` |
| Automotive | `directions_car` |
| Garden | `local_florist` |

Full icon list: https://fonts.google.com/icons

---

## Monitoring Low Stock Alerts

The system provides **proactive low stock monitoring** to prevent stockouts and lost sales.

### Accessing Low Stock Alerts

#### Via Admin Dashboard

1. Login as admin
2. Click **"Admin"** in navigation
3. View the dashboard at: http://127.0.0.1:8000/admin/dashboard

### Dashboard Overview

```
╔════════════════════════════════════════════════════════════╗
║                    ADMIN DASHBOARD                         ║
╠════════════════════════════════════════════════════════════╣
║                     STATS OVERVIEW                         ║
║  ┌──────────────┬──────────────┬──────────────┬─────────┐ ║
║  │ Total Revenue│ Pending Pay  │ Total Products│ Low Stock║
║  │ KSh 25,450   │      3       │      50      │    5     ║
║  └──────────────┴──────────────┴──────────────┴─────────┘ ║
╠════════════════════════════════════════════════════════════╣
║                   LOW STOCK ALERT                          ║
║  Product Name        Category    Stock   Price    Action  ║
║  ────────────────────────────────────────────────────────  ║
║  🔴 USB-C Cable      Electronics   0    KSh 299  [Edit]   ║
║  🟠 Dress Shirt      Fashion       3    KSh 999  [Edit]   ║
║  🟡 Smart Watch      Electronics   8    KSh 4999 [Edit]   ║
╠════════════════════════════════════════════════════════════╣
║                   PENDING PAYMENTS                         ║
║  Order #         Customer       Total      Action          ║
║  ────────────────────────────────────────────────────────  ║
║  ORD-XXX123      John Doe    KSh 1,299  [Mock Payment]    ║
╠════════════════════════════════════════════════════════════╣
║                    QUICK ACTIONS                           ║
║  [Manage Products]  [Add New Product]                      ║
╚════════════════════════════════════════════════════════════╝
```

### Alert Thresholds

The system triggers alerts when stock falls to **10 units or below**:

| Stock Level | Alert Color | Urgency | Recommended Action |
|------------|-------------|---------|-------------------|
| 0 units | 🔴 Red | **CRITICAL** | Immediate restocking required |
| 1-5 units | 🟠 Orange | **HIGH** | Urgent - Order within 24-48 hours |
| 6-10 units | 🟡 Yellow | **MODERATE** | Plan restocking this week |

### Low Stock Alert Features

#### Visual Indicators

Products in the alert section have:
- **Color-coded rows**: Red (0), Yellow (1-5), or neutral (6-10)
- **Product thumbnail**: Quick visual identification
- **Category information**: For restocking organization
- **Current stock count**: Exact units remaining
- **Price**: For budget planning
- **Quick edit link**: Direct access to update stock

#### Information Displayed

Each alert shows:
```
Product Name: Wireless Headphones
SKU: WH-BT-001
Category: Electronics
Current Stock: 3 units
Price: KSh 2,999.00
[Edit] [View Product]
```

### Taking Action on Low Stock

#### Quick Edit from Dashboard

1. In the Low Stock Alert section
2. Click **"Edit"** next to the product
3. Update stock quantity
4. Save changes

#### Bulk Restocking Workflow

For multiple low-stock items:

1. **Review Alerts**: Note all products needing restock
2. **Plan Orders**: Group by category or supplier
3. **Update Stock**: As items arrive, edit each product
4. **Verify**: Check dashboard - alerts disappear when stock > 10

#### Stock Update Example

**Scenario**: USB-C Cable shows 0 units in stock

1. Navigate to low stock alerts
2. Click "Edit" next to USB-C Cable
3. Supplier delivered 100 units
4. Update stock from `0` to `100`
5. Click "Update Product"
6. Product no longer appears in alerts ✅

### Preventing Stockouts

#### Best Practices

1. **Daily Check**: Review dashboard every morning
2. **Set Reorder Points**: When stock hits 10, initiate order
3. **Lead Time Planning**: Account for supplier delivery time
4. **Safety Stock**: Keep extra units for fast-moving items
5. **Seasonal Adjustment**: Increase stock before peak seasons

#### Monitoring High-Velocity Products

Track products that frequently appear in low stock alerts:

```sql
-- Find products that sold out fastest
SELECT 
    p.name,
    p.stock as current_stock,
    COUNT(oi.id) as times_ordered,
    SUM(oi.quantity) as total_sold
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id
ORDER BY total_sold DESC
LIMIT 10;
```

---

## Best Practices

### Product Images

#### Recommended Specifications

- **Resolution**: 800x800px minimum
- **Format**: JPEG or PNG
- **File Size**: Under 1MB (optimized)
- **Aspect Ratio**: 1:1 (square)
- **Background**: White or transparent
- **Lighting**: Well-lit, no shadows
- **Focus**: Product-centered

#### Image Optimization Tips

```bash
# Resize images to 800x800 (using ImageMagick)
convert original.jpg -resize 800x800 -quality 85 optimized.jpg

# Compress PNG files
optipng -o7 product.png
```

### Pricing Strategy

#### Setting Competitive Prices

1. **Research Market**: Check competitor pricing
2. **Calculate Costs**: Product cost + shipping + margin
3. **Consider VAT**: System adds 8% tax at checkout
4. **Round Strategically**: KSh 2,999 vs KSh 3,000

#### Discount Display

Use **Original Price** field to show savings:

```
Original Price: KSh 4,999
Sale Price: KSh 3,999
→ Customer sees: "Save KSh 1,000 (20% off)"
```

### Stock Management

#### Initial Stock Levels

| Product Type | Recommended Initial Stock |
|-------------|---------------------------|
| Fast-moving electronics | 50-100 units |
| Seasonal clothing | 30-50 units |
| High-value items | 10-20 units |
| New/untested products | 20-30 units |

#### Reorder Thresholds

Set reorder points based on:
- **Sales velocity**: Units sold per week
- **Lead time**: Days to receive new stock
- **Safety buffer**: Extra units for unexpected demand

**Formula:**
```
Reorder Point = (Weekly Sales × Lead Time in Weeks) + Safety Stock
```

**Example:**
```
Product sells 10 units/week
Lead time: 2 weeks
Safety stock: 5 units

Reorder Point = (10 × 2) + 5 = 25 units
```

### Category Organization

#### Choosing the Right Category

- **Be Specific**: "Electronics" > "Products"
- **Customer Perspective**: How would buyers search?
- **Consistent Structure**: Similar products in same category
- **Avoid Over-categorization**: Too many categories confuse

#### Category Best Practices

✅ **Do:**
- Use clear, descriptive names
- Assign every product to a category
- Keep category count manageable (5-15)
- Use recognizable icons

❌ **Don't:**
- Create too many subcategories
- Leave products uncategorized
- Use confusing or similar names
- Mix different product types

---

## Troubleshooting

### Common Issues

#### Image Upload Fails

**Problem**: "Image upload failed" error

**Solutions:**
1. Check file size (must be < 2MB)
2. Verify file format (JPEG, PNG, JPG, GIF only)
3. Check storage permissions:
```bash
chmod -R 775 storage/
php artisan storage:link
```

#### Product Not Showing in Shop

**Problem**: Product saved but doesn't appear in shop

**Check:**
1. **Stock level**: Products with 0 stock are hidden
2. **Category assigned**: Uncategorized products may not display
3. **Clear cache**: `php artisan cache:clear`

#### Stock Not Updating After Sale

**Problem**: Stock remains unchanged after payment

**This is correct!** Stock only decrements after:
- Payment callback received
- Payment status = "success"
- Database transaction completes

See: [ADMIN_INVENTORY_CONTROL.md](ADMIN_INVENTORY_CONTROL.md) for details

#### Low Stock Alert Not Showing

**Problem**: Product has low stock but no alert

**Check:**
1. Stock must be ≤ 10 to trigger alert
2. Refresh dashboard page
3. Clear cache: `php artisan cache:clear`

---

## Quick Reference

### URLs

| Function | URL |
|----------|-----|
| Admin Dashboard | http://127.0.0.1:8000/admin/dashboard |
| Manage Products | http://127.0.0.1:8000/admin/products |
| Add Product | http://127.0.0.1:8000/admin/products/create |
| Edit Product | http://127.0.0.1:8000/admin/products/{id}/edit |

### Key Files

| Component | Location |
|-----------|----------|
| Product Controller | `app/Http/Controllers/Admin/ProductController.php` |
| Product Model | `app/Models/Product.php` |
| Products List View | `resources/views/admin/products/index.blade.php` |
| Create Form | `resources/views/admin/products/create.blade.php` |
| Edit Form | `resources/views/admin/products/edit.blade.php` |
| Admin Dashboard | `resources/views/admin/dashboard.blade.php` |
| Image Storage | `storage/app/public/products/` |

### Database Commands

```bash
# View all products
sqlite3 database/database.sqlite "SELECT name, price, stock FROM products;"

# View low stock products
sqlite3 database/database.sqlite "SELECT name, stock FROM products WHERE stock <= 10;"

# View all categories
sqlite3 database/database.sqlite "SELECT id, name FROM categories;"

# Update product stock manually
sqlite3 database/database.sqlite "UPDATE products SET stock = 50 WHERE id = 1;"
```

---

## Summary

The admin panel provides comprehensive tools for:

✅ **Product Management**
- Add new products with images and prices
- Edit existing products
- Delete discontinued items
- Manage stock levels

✅ **Image Handling**
- Upload product images (max 2MB)
- Automatic storage management
- Old images deleted on update

✅ **Category Organization**
- Assign products to categories
- Filter and organize inventory
- Customer-friendly navigation

✅ **Low Stock Monitoring**
- Real-time alerts (≤10 units)
- Color-coded urgency levels
- Quick-edit access
- Dashboard visibility

✅ **Stock Management**
- Automatic decrements after payment
- Manual adjustments for restocking
- Complete audit trail
- Prevents overselling

---

**Last Updated**: January 26, 2026  
**System Version**: Laravel 12.48.1 | PHP 8.4.11

For technical details on inventory management, see: [ADMIN_INVENTORY_CONTROL.md](ADMIN_INVENTORY_CONTROL.md)
