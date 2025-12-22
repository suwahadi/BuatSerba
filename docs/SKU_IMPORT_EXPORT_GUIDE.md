# SKU Import/Export Guide

## Export SKU Data

1. Go to `/admin/skus`
2. Click **"Export to Excel"** button (green button with download icon)
3. Select export options if needed
4. File will be downloaded as `skus-YYYY-MM-DD.xlsx`

### Exported Columns:
- ID
- Product ID
- Product Name
- SKU Code
- Base Price
- Selling Price
- Wholesale Price
- Wholesale Min Quantity
- Stock Quantity
- Weight (g)
- Length (cm)
- Width (cm)
- Height (cm)
- Is Active
- Created At
- Updated At

---

## Import SKU Data

1. Go to `/admin/skus`
2. Click **"Import from Excel"** button (blue button with upload icon)
3. Upload your Excel file (`.xlsx` or `.csv`)
4. Map columns if needed
5. Review and confirm import
6. Wait for import to complete

### Required Columns:
- **Product ID** - Must exist in products table
- **SKU Code** - Unique identifier
- **Selling Price** - Product selling price
- **Stock Quantity** - Initial stock quantity

### Optional Columns:
- Base Price
- Wholesale Price
- Wholesale Min Quantity
- Weight (grams)
- Length (cm)
- Width (cm)
- Height (cm)
- Is Active (1 = active, 0 = inactive)

---

## Excel Template Example

| product_id | sku | selling_price | stock_quantity | base_price | wholesale_price | wholesale_min_quantity | weight | length | width | height | is_active |
|------------|-----|---------------|----------------|------------|-----------------|------------------------|--------|--------|-------|--------|-----------|
| 1 | SKU-001 | 150000 | 100 | 100000 | 120000 | 10 | 500 | 10 | 5 | 2 | 1 |
| 1 | SKU-002 | 200000 | 50 | 150000 | 170000 | 5 | 750 | 15 | 8 | 3 | 1 |
| 2 | SKU-003 | 75000 | 200 | 50000 | 60000 | 20 | 300 | 8 | 4 | 1 | 1 |

---

## Notes

- **Update Existing**: If SKU code already exists, it will be updated
- **Create New**: If SKU code doesn't exist, new record will be created
- **Validation**: All data will be validated before import
- **Error Handling**: Failed rows will be reported with error messages
- **Progress**: You can see import progress in real-time
- **Notification**: You'll receive a notification when import is complete

---

## Tips

1. **Export first**: Export existing data to see the correct format
2. **Backup**: Always backup your data before importing
3. **Test**: Test with a small file first
4. **Product ID**: Make sure Product IDs exist in your database
5. **Unique SKU**: Each SKU code must be unique
6. **Numbers**: Use numbers without currency symbols or thousand separators
7. **Boolean**: Use 1 for true/active, 0 for false/inactive
