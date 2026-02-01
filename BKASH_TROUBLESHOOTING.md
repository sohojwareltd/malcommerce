# bKash Payment Gateway Troubleshooting

## Current Issue: "App key does not exist" (Error Code: 9999)

### Problem
The bKash API is returning error code `9999` with message "App key does not exist". This means the `BKASH_APP_KEY` in your `.env` file is not recognized by bKash.

### Possible Causes

1. **Incorrect Credentials**: The APP_KEY doesn't match what bKash has on record
2. **Wrong Environment**: Using production credentials in sandbox or vice versa
3. **Expired/Revoked Credentials**: The credentials may have been revoked or expired
4. **Special Characters in Password**: The password contains special characters that might need escaping

### Current Configuration

From your `.env` file:
```
BKASH_BASE_URL=https://tokenized.sandbox.bka.sh/v1.2.0-beta
BKASH_USERNAME=01830413810
BKASH_PASSWORD=-";s3RU>zb|+"
BKASH_APP_KEY=v3ttZ77VbqHrUIVKQRfAl0Gttc
BKASH_APP_SECRET=kTYhEDCSYfvyuYhlr19V8parmaQW82QabQM4RAnY1xQQ1WPz9qTn
BKASH_SANDBOX=true
```

### Solutions

#### 1. Verify Credentials with bKash
- Log into your [bKash Merchant Portal](https://merchant.bka.sh/)
- Check if your APP_KEY matches exactly (no extra spaces, correct case)
- Verify you're using **sandbox credentials** for sandbox mode
- Confirm your account is active and API access is enabled

#### 2. Regenerate Credentials
If credentials are incorrect:
- Contact bKash support to regenerate your API credentials
- Update your `.env` file with the new credentials
- Clear config cache: `php artisan config:clear`

#### 3. Check Password Escaping
The password contains special characters: `-";s3RU>zb|+"`

If the password is not being read correctly, try:
- Wrapping it in quotes in `.env`: `BKASH_PASSWORD="-";s3RU>zb|+"`
- Or escaping special characters if needed

#### 4. Test API Connection
You can test the token grant manually:

```bash
curl -X POST https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "username: YOUR_USERNAME" \
  -H "password: YOUR_PASSWORD" \
  -d '{
    "app_key": "YOUR_APP_KEY",
    "app_secret": "YOUR_APP_SECRET"
  }'
```

### Next Steps

1. **Verify credentials** in bKash Merchant Portal
2. **Update `.env`** with correct credentials if needed
3. **Clear cache**: `php artisan config:clear`
4. **Test again** by creating a test order with bKash payment

### Support

- bKash Developer Portal: https://developer.bka.sh/
- bKash Merchant Support: Contact through merchant portal
- Check logs: `storage/logs/laravel.log` for detailed error messages
