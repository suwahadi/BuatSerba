# Midtrans Core API Testing Guide

## Quick Start Testing

### 1. Setup for Local Testing

First, expose your localhost with ngrok:
```bash
ngrok http 8000
# You'll get a URL like: https://abc123.ngrok.io
```

Update your `.env`:
```
MIDTRANS_SERVER_KEY=VT-sk-test_xxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=VT-ck-test_xxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_NOTIFICATION_URL=https://abc123.ngrok.io/midtrans/notification
```

Clear config cache:
```bash
php artisan config:cache
```

---

### 2. Simulate Complete Payment Flow

#### Option A: Using PHP Artisan Tinker

```bash
php artisan tinker
```

```php
// Step 1: Create an order manually
$order = \App\Models\Order::create([
    'order_number' => 'TEST-' . now()->format('YmdHis'),
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '081234567890',
    'shipping_address' => 'Jl. Test No. 1',
    'shipping_city' => 'Jakarta',
    'shipping_district' => 'Pusat',
    'shipping_province' => 'DKI Jakarta',
    'shipping_postal_code' => '12345',
    'shipping_method' => 'jne_regular',
    'shipping_cost' => 50000,
    'payment_method' => 'bank-transfer-bca',
    'payment_status' => 'pending',
    'status' => 'pending',
    'subtotal' => 100000,
    'service_fee' => 2000,
    'discount' => 0,
    'total' => 152000,
]);

// Step 2: Create payment via Midtrans Core API
$midtransService = new \App\Services\MidtransService();
$result = $midtransService->createTransaction($order, 'bank-transfer-bca');

if ($result['success']) {
    echo "✓ Payment created successfully!\n";
    echo "Order: " . $order->order_number . "\n";
    echo "Payment ID: " . $result['payment_id'] . "\n";
    echo "Instructions: " . json_encode($result['payment_instructions'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "✗ Payment creation failed: " . $result['message'] . "\n";
}

// Step 3: Get the payment record
$payment = \App\Models\Payment::find($result['payment_id']);
echo "Payment Status: " . $payment->transaction_status . "\n";
echo "Fraud Status: " . $payment->fraud_status . "\n";
```

#### Option B: Using cURL (Command Line)

```bash
# First, get your Midtrans credentials from https://dashboard.midtrans.com

# Create a payment
curl -X POST https://api.sandbox.midtrans.com/v2/charge \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic $(echo -n 'YOUR_SERVER_KEY:' | base64)" \
  -d '{
    "payment_type": "bank_transfer",
    "bank_transfer": {
      "bank": "bca"
    },
    "transaction_details": {
      "order_id": "TEST-'$(date +%s)'",
      "gross_amount": 100000
    },
    "customer_details": {
      "first_name": "Test",
      "email": "test@example.com",
      "phone": "081234567890"
    },
    "callbacks": {
      "notification_url": "https://your-ngrok-url/midtrans/notification"
    }
  }'
```

---

### 3. Test Webhook Notification

After payment is created, simulate Midtrans sending a notification:

```bash
# Get a real order number from your test
ORDER_NUMBER="ORD-20251216-xxxxx"
GROSS_AMOUNT="100000"
STATUS_CODE="200"

# Calculate signature (replace with your server key)
SERVER_KEY="VT-sk-test_xxxxxxxxxxxxxx"
SIGNATURE=$(echo -n "${ORDER_NUMBER}${STATUS_CODE}${GROSS_AMOUNT}${SERVER_KEY}" | sha512sum | awk '{print $1}')

# Send webhook notification
curl -X POST http://localhost:8000/midtrans/notification \
  -H "Content-Type: application/json" \
  -d "{
    \"order_id\": \"${ORDER_NUMBER}\",
    \"status_code\": \"${STATUS_CODE}\",
    \"gross_amount\": \"${GROSS_AMOUNT}\",
    \"signature_key\": \"${SIGNATURE}\",
    \"transaction_status\": \"settlement\",
    \"fraud_status\": \"accept\",
    \"payment_type\": \"bank_transfer\",
    \"transaction_id\": \"0123456789012345\",
    \"transaction_time\": \"$(date '+%Y-%m-%d %H:%M:%S')\",
    \"bank_transfer\": {
      \"bank\": \"bca\",
      \"va_number\": \"12345678901234567890\"
    }
  }"
```

---

### 4. Verify Order Status Updated

```php
php artisan tinker

$order = \App\Models\Order::where('order_number', 'ORD-20251216-xxxxx')->first();
echo "Order Status: " . $order->status . "\n";
echo "Payment Status: " . $order->payment_status . "\n";
echo "Paid At: " . $order->paid_at . "\n";

$payment = $order->payment;
echo "Transaction Status: " . $payment->transaction_status . "\n";
echo "Fraud Status: " . $payment->fraud_status . "\n";
```

---

### 5. Check Payment Instructions Display

Visit the payment page:
```
http://localhost/payment/ORD-20251216-xxxxx
```

You should see:
- Virtual Account Number
- Bank name
- Step-by-step payment instructions
- Amount to transfer

---

### 6. Monitor Logs

```bash
tail -f storage/logs/laravel.log | grep -i midtrans
```

Expected logs:
```
[2025-12-16 10:00:00] local.INFO: Midtrans Core API Request: https://api.sandbox.midtrans.com/v2/charge ...
[2025-12-16 10:00:01] local.INFO: Midtrans Core API Response: 201 ...
[2025-12-16 10:00:05] local.INFO: Payment notification created ...
[2025-12-16 10:00:05] local.INFO: Payment updated from notification ...
```

---

## Complete Test Scenario

### Scenario: Customer Places Order and Pays

1. **Create Order** (You):
   ```php
   // Go to checkout and place order normally through UI
   // Or use Tinker to create programmatically
   ```

2. **Payment Created** (Automatic):
   ```
   Midtrans Core API called
   Virtual Account created
   Payment record saved
   User sees payment instructions
   ```

3. **Customer Pays** (Simulate):
   ```bash
   # Send webhook from Midtrans
   ./scripts/test_midtrans_payment.sh ORD-20251216-xxxxx
   ```

4. **Verify Success**:
   ```php
   $order = Order::where('order_number', 'ORD-20251216-xxxxx')->first();
   assert($order->payment_status === 'paid');
   assert($order->status === 'processing');
   assert($order->paid_at !== null);
   ```

---

## Testing Different Payment Methods

### Bank Transfer - BCA
```php
$paymentResult = $midtransService->createTransaction($order, 'bank-transfer-bca');
// Returns: Virtual Account number for BCA
```

### Bank Transfer - BNI
```php
$paymentResult = $midtransService->createTransaction($order, 'bank-transfer-bni');
// Returns: Virtual Account number for BNI
```

### Bank Transfer - BRI
```php
$paymentResult = $midtransService->createTransaction($order, 'bank-transfer-bri');
// Returns: Virtual Account number for BRI
```

---

## Common Issues & Solutions

### Issue: "Invalid signature" warning

**Cause**: Server key mismatch or notification payload modified

**Solution**:
```php
// Verify your server key in .env matches Midtrans dashboard
echo env('MIDTRANS_SERVER_KEY');

// Check logs for signature validation failure
tail storage/logs/laravel.log | grep "Invalid Midtrans signature"
```

### Issue: Webhook not received

**Cause**: Notification URL not publicly accessible

**Solution**:
```bash
# Check ngrok is running and URL is correct
ngrok http 8000

# Update .env with correct URL
MIDTRANS_NOTIFICATION_URL=https://your-ngrok-id.ngrok.io/midtrans/notification

# Clear config cache
php artisan config:cache

# Test webhook delivery
curl -v https://your-ngrok-id.ngrok.io/midtrans/notification
```

### Issue: Order not marked as paid

**Cause**: transaction_status not 'settlement' or fraud_status not 'accept'

**Solution**:
```php
$payment = Payment::where('order_id', $orderId)->first();
dd([
    'transaction_status' => $payment->transaction_status,
    'fraud_status' => $payment->fraud_status,
    'should_be_paid' => in_array($payment->transaction_status, ['settlement', 'capture']) && 
                        ($payment->fraud_status === 'accept' || $payment->fraud_status === null)
]);
```

---

## Database Inspection

```php
php artisan tinker

// View payment details
$payment = Payment::with('order')->latest()->first();
echo $payment->toJson(JSON_PRETTY_PRINT);

// View all notifications for an order
$notifications = PaymentNotification::where('order_id', 'ORD-xxx')->get();
echo $notifications->toJson(JSON_PRETTY_PRINT);

// Check order status
$order = Order::where('order_number', 'ORD-xxx')->first();
echo "Payment Status: " . $order->payment_status . "\n";
echo "Order Status: " . $order->status . "\n";
```

---

## Performance Testing

Monitor webhook processing time:

```bash
# Check notification processing logs
grep "Payment notification created\|Payment updated from notification" storage/logs/laravel.log

# Should be < 100ms for each operation
```

---

## Security Checklist

- [ ] MIDTRANS_SERVER_KEY is secret (not in version control)
- [ ] Webhook signature validation enabled
- [ ] MIDTRANS_IS_PRODUCTION=false in development
- [ ] MIDTRANS_IS_PRODUCTION=true in production
- [ ] MIDTRANS_NOTIFICATION_URL uses HTTPS
- [ ] ngrok tunnel properly configured for local testing
- [ ] API responses logged without sensitive data
- [ ] Database transactions properly handled

---

## Going to Production

1. Switch MIDTRANS_IS_PRODUCTION to true
2. Get production API keys from Midtrans dashboard
3. Update MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY
4. Ensure MIDTRANS_NOTIFICATION_URL points to production domain
5. Test with small transaction first
6. Monitor logs for any issues
7. Set up error monitoring (Sentry, Rollbar, etc.)

