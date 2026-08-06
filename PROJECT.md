# Party Poppers Commerce

Self-hosted Laravel ecommerce application using MySQL, Blade, and vanilla browser behavior. Both the application and automated tests use MySQL.

## Local Development

WAMP MySQL must be running with the database credentials configured in `.env`.

- Application database: `party_poppers`
- Automated test database: `party_poppers_test`

```powershell
cd D:\party-poppers-design\application
php artisan migrate:fresh --seed
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Seeded Accounts

- Admin: `admin@partypoppers.pk` / `password`
- Customer: `customer@example.com` / `password`

Change both passwords before using the application outside local development.

## Architecture

- MySQL owns catalog, inventory, customers, orders, payments, fulfillment, promotions, and audit data.
- Prices are stored as integer PKR amounts.
- Checkout locks inventory rows inside a database transaction to prevent overselling.
- COD implements the internal `PaymentProvider` interface.
- Internal delivery implements the replaceable `DeliveryProvider` interface.
- Customer customization uploads are stored on the private local disk.

## Verification

```powershell
php artisan test
php artisan view:cache
php artisan route:list --except-vendor
```
