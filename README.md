> **Warning:** This package is still under development and has not been released in any version. Please do not use it in production environments as it is subject to significant changes.

# Introduction
The `fet/raisenow-api` package is a PHP implementation of the RaiseNow API (see [References](#references)).

# Installation
To install this package, use Composer:

```bash
composer require fet/postcard-api
```

> Make sure you have Composer installed on your system before running this command.

# Configuration
Create a new `Fet\RaiseNowApi\RaiseNow` instance using your API credentials:

```php
use Fet\RaiseNowApi\RaiseNow;

$raiseNow = RaiseNow::create([
    'uri' => 'RAISENOW_API_URI', // The base URL of the RaiseNow API
    'client_id' => 'RAISENOW_API_CLIENT_ID', // Your RaiseNow API client ID
    'client_secret' => 'RAISENOW_API_CLIENT_SECRET', // Your RaiseNow API client secret
]);
```

# References
## RaiseNow EPayment API
- https://docs.raisenow.com/api

# Roadmap
Here you'll find the implementation status of all the features the RaiseNow API provides.

- [x] AUTHENTICATION
- [x] ORGANISATIONS
- [ ] ACCOUNTS
- [ ] PAYMENTS
- [ ] PAYMENT SOURCES
- [ ] SUPPORTERS
- [ ] SUBSCRIPTIONS
- [ ] EVENT HUB
- [ ] ONBOARDING PROCEDURE

