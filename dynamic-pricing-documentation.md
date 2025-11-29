# Dynamic Pricing System Documentation

## Overview

This document explains the implementation of the dynamic pricing system that supports multiple pricing tiers based on purchase quantity. The system allows products to have different prices depending on the quantity purchased, such as retail pricing for small quantities and wholesale pricing for bulk purchases.

## Requirements

The system was designed to meet the following requirements:
- Support for two pricing models: Retail (Ecer) and Wholesale (Grosir)
- Wholesale pricing applies when purchasing more than 100 pieces
- Dynamic and flexible pricing structure that can handle multiple tiers

## Database Structure

### New Columns in `skus` Table

1. `pricing_tiers` (JSON) - Stores the pricing tiers configuration
2. `use_dynamic_pricing` (Boolean) - Flag to enable/disable dynamic pricing

### Pricing Tiers Format

The `pricing_tiers` column stores an array of pricing tier objects:

```json
[
  {
    "quantity": 1,
    "price": 10000,
    "label": "Eceran"
  },
  {
    "quantity": 100,
    "price": 8000,
    "label": "Grosir"
  }
]
```

Each tier object contains:
- `quantity`: Minimum quantity required to qualify for this price
- `price`: Price per unit for this tier
- `label`: Display label for this pricing tier

## Implementation Details

### Model Changes

The [Sku](file:///d:/laragon/www/buatserba/app/Models/Sku.php#L8-L62) model was enhanced with two new methods:

1. `getPriceForQuantity(int $quantity): float` - Returns the appropriate price based on the quantity
2. `getPricingTiersForDisplay(): array` - Returns all available pricing tiers for display purposes

### Pricing Logic

1. When `use_dynamic_pricing` is enabled and `pricing_tiers` is populated:
   - The system sorts tiers by quantity in descending order
   - It finds the highest tier where quantity >= tier quantity
   - Returns the price for that tier

2. When dynamic pricing is disabled:
   - Falls back to the existing wholesale logic
   - Uses `selling_price` for quantities less than `wholesale_min_quantity`
   - Uses `wholesale_price` for quantities greater than or equal to `wholesale_min_quantity`

### Frontend Changes

1. **Product Detail Page**:
   - Displays all available pricing tiers in a clear table
   - Shows the label for each tier (Eceran, Grosir, etc.)
   - Provides information about automatic price adjustment

2. **Catalog Page**:
   - Shows wholesale pricing information when available
   - Displays a "Grosir" badge with price and minimum quantity

### Database Seeder

A new seeder ([DynamicPricingSeeder](file:///d:/laragon/www/buatserba/database/seeders/DynamicPricingSeeder.php#L12-L87)) was created to:
1. Update existing products with wholesale pricing to use the new dynamic system
2. Create sample products with complex pricing tiers for testing

## Usage Examples

### Setting Up Dynamic Pricing for a Product

```php
$sku = new Sku([
    'sku' => 'PRODUCT-001',
    'base_price' => 100000,
    'selling_price' => 90000,
    'weight' => 500,
    'stock_quantity' => 500,
    'is_active' => true,
    'use_dynamic_pricing' => true,
    'pricing_tiers' => [
        [
            'quantity' => 1,
            'price' => 90000,
            'label' => 'Eceran'
        ],
        [
            'quantity' => 100,
            'price' => 75000,
            'label' => 'Grosir'
        ]
    ]
]);

$product->skus()->save($sku);
```

### Getting Price for a Specific Quantity

```php
// For quantities less than 100, returns retail price (90000)
$price = $sku->getPriceForQuantity(50);

// For quantities 100 or more, returns wholesale price (75000)
$price = $sku->getPriceForQuantity(150);
```

## Benefits

1. **Flexibility**: Supports any number of pricing tiers
2. **Backward Compatibility**: Works with existing wholesale logic
3. **Clear Display**: Customers can easily see all available pricing options
4. **Automatic Calculation**: Prices are automatically calculated based on quantity
5. **Easy Configuration**: Simple JSON structure for pricing tiers

## Testing

Unit tests were created to verify:
1. Price calculation based on quantity tiers
2. Fallback to existing wholesale logic
3. Proper display of pricing tiers

To run the tests:
```bash
php artisan test --filter=DynamicPricingTest
```