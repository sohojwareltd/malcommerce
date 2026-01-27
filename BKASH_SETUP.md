# bKash Payment Gateway Integration Setup

This document explains how to set up and configure the bKash payment gateway integration.

## API Documentation Reference

- Official bKash API Docs: https://developer.bka.sh/
- Create Payment API: https://developer.bka.sh/reference/createpaymentusingpost
- Checkout Process: https://developer.bka.sh/docs/checkout-url-process-overview

## Environment Variables

Add the following environment variables to your `.env` file:

```env
# bKash Payment Gateway Configuration
# Base URL will be provided by bKash during onboarding
BKASH_BASE_URL=https://tokenized.sandbox.bka.sh/v1.2.0-beta
BKASH_USERNAME=your_bkash_username
BKASH_PASSWORD=your_bkash_password
BKASH_APP_KEY=your_bkash_app_key
BKASH_APP_SECRET=your_bkash_app_secret
BKASH_SANDBOX=true
```

### For Production

When you're ready to go live, change the following:

```env
BKASH_BASE_URL=https://tokenized.pay.bka.sh/v1.2.0-beta
BKASH_SANDBOX=false
```

**Note:** The base URL will be provided by bKash during your merchant onboarding process. Use the exact URL they provide.

## Getting bKash API Credentials

1. Register for a bKash merchant account at [bKash Merchant Portal](https://merchant.bka.sh/)
2. Apply for Tokenized Checkout API access
3. Once approved, you'll receive:
   - Username
   - Password
   - App Key
   - App Secret

## Database Migration

Run the migration to add payment fields to the orders table:

```bash
php artisan migrate
```

## Features

### Payment Methods
- **Cash on Delivery (COD)**: Traditional payment on delivery
- **bKash**: Online payment via bKash

### Payment Flow

1. Customer selects payment method during checkout
2. If bKash is selected:
   - Order is created with `payment_status: pending`
   - Customer is redirected to bKash payment page
   - After payment, callback is received
   - Payment status is updated automatically
3. If COD is selected:
   - Order is created normally
   - Payment status remains pending until delivery

### Payment Status Check

Customers can check their bKash payment status from the order success page if payment is still pending or processing.

## API Endpoints

### Application Routes
- `GET|POST /payment/bkash/initiate` - Initiate bKash payment (redirects to bKash payment page)
- `GET /payment/bkash/callback` - bKash payment callback (called by bKash with paymentID and status)
- `GET /payment/bkash/cancel/{orderId}` - Cancel payment and delete order
- `POST /payment/check-status` - Check payment status via API

### bKash API Endpoints Used
- `POST {base_URL}/tokenized/checkout/token/grant` - Get access token
- `POST {base_URL}/tokenized/checkout/payment/create` - Create payment (mode: 0011)
- `POST {base_URL}/tokenized/checkout/payment/execute/{paymentID}` - Execute payment
- `GET {base_URL}/tokenized/checkout/payment/query/{paymentID}` - Query payment status

## Testing

For sandbox testing, use bKash test credentials provided by bKash. The sandbox environment allows you to test the complete payment flow without real transactions.

## Support

For bKash API documentation and support, visit:
- [bKash Developer Portal](https://developer.bka.sh/)
- bKash Merchant Support
