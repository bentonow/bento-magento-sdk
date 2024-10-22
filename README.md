# Bento SDK for Magento 2
🍱 Simple, powerful email marketing for Magento websites.

Track events, update data, record LTV and more. Data is stored in your Bento account so you can easily research and investigate what's going on.

> [!TIP]
> 👋 Need help? Join our [Discord](https://discord.gg/ssXXFRmt5F) or email jesse@bentonow.com for personalized support.

🐶 Tested on Magento 2.4.x

> [!IMPORTANT]  
> Please install the Bento plugin on a development or staging site before using it in production. This ensures that all your modules are compatible and there are no conflicts. Additionally, make sure you have a recent backup handy. While we've tested this plugin on clean installs of Magento 2, we can't guarantee there will be no issues. Use at your own risk (which you can mitigate with testing on a staging site and backing up properly!).

## Requirements

- Bento account and your API keys
- Magento 2.4.x
- PHP 7.4 or higher

## Installation

1. Create a new directory in your Magento installation: `app/code/Bentonow/Bento`
2. Copy all plugin files into this directory
3. From your Magento root directory, run the following commands:
```bash
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```
4. In the Magento admin panel, navigate to Stores > Configuration > Bento
5. Enter your Bento API credentials:
    - BENTO_PUBLISHABLE_KEY
    - BENTO_SECRET_KEY
    - BENTO_SITE_UUID

## Configuration
<p align="center"><img src="/artwork/configuration_options.webp" alt="Bento job queue"></p>

The Bento module can be configured in the Magento admin panel under Stores > Configuration > Bento. Here you can:

- Enable/disable customer registration events
- Configure customer registration tags
- View and manage the job queue



## Event Tracking

### Customer Events

```
$Subscriber

Sent when a new customer registers
Keys:
- email (customer's email)
- first_name (customer's first name)
- last_name (customer's last name)
- tags (configurable from admin, defaults to "lead,mql")
- remove_tags (fixed value "customers")

Notes:
- All subscriber data is wrapped in a "subscribers" array
- Tags can be configured in Stores > Configuration > Bento
- Event can be disabled in configuration
```

### Order Events

```
$Purchase

Sent when a new order is placed
Keys:
- email (customer's email)
- type (fixed value "$purchase")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (order total in cents)
- details.cart.items[] (array of ordered products)
  - product_sku (product SKU)
  - product_name (product name)
  - quantity (ordered quantity)

Notes:
- Amount is always in cents (multiply by 100)
- All order data is wrapped in an "events" array
- Cart items include all products in the order
```

```
$OrderShipped

Sent when an order is marked as shipped
Keys:
- email (customer's email)
- type (fixed value "$order_shipped")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (order total in cents)
- details.shipment.id (shipment increment ID)
- details.shipment.tracking[] (array of tracking information)
  - carrier (shipping carrier name)
  - number (tracking number)

Notes:
- Multiple tracking numbers will create multiple entries in tracking array
- Triggered on shipment save
```

```
$OrderRefunded

Sent when an order is refunded
Keys:
- email (customer's email)
- type (fixed value "$order_refunded")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (refunded amount in cents)
- details.refund.id (credit memo increment ID)
- details.refund.reason (customer provided reason if available)

Notes:
- Amount reflects only the refunded portion, not original order total
- Works for both partial and full refunds
- Triggered when credit memo is saved
```

```
$OrderHeld

Sent when an order is placed on hold
Keys:
- email (customer's email)
- type (fixed value "$order_held")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (order total in cents)
- details.held_at (timestamp of hold action)

Notes:
- Triggered when order status changes to 'holded'
- Includes full order amount
```

```
$OrderCanceled

Sent when an order is canceled
Keys:
- email (customer's email)
- type (fixed value "$order_canceled")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (order total in cents)
- details.canceled_at (timestamp of cancellation)
- details.reason (cancellation reason if provided)

Notes:
- Triggered when order status changes to 'canceled'
- Includes full original order amount
```

```
$OrderClosed

Sent when an order is closed
Keys:
- email (customer's email)
- type (fixed value "$order_closed")
- fields.first_name (customer's first name)
- details.unique.key (order increment ID)
- details.value.currency (order currency code)
- details.value.amount (order total in cents)
- details.closed_at (timestamp when order was closed)

Notes:
- Triggered when order status changes to 'closed'
- Represents final state of order
```


### Job Queue

Jobs are stored with the following data:
```
Database Fields:
- job_id (auto-increment primary key)
- job_type (event type being sent)
- status (pending/completed/error)
- data (serialized event data)
- error_message (any error response)
- http_status_code (response code from API)
- created_at (job creation timestamp)
- updated_at (last update timestamp)

Notes:
- Jobs processed every 5 minutes via cron
- Failed jobs can be requeued from admin
- Delete action permanently removes job
```

### Job Queue

Events are stored in the queue table with the following information:
- Job Type (subscriber, purchase, etc.)
- Status (pending, completed, error)
- HTTP Status Code
- Raw Response Data
- Created/Updated Timestamps

Failed jobs can be requeued from the admin panel, and the queue can be monitored for any delivery issues.

## Job Queue Management
<p align="center"><img src="/artwork/jobqueue.webp" alt="Bento job queue"></p>

The module includes a robust job queue system that:

1. Queues all events for reliable delivery
2. Shows job status in the admin panel
3. Displays HTTP response codes for debugging
4. Allows manual requeuing of failed jobs
5. Provides job cleanup capabilities

To access the job queue:
1. Log into your Magento admin panel
2. Navigate to the Bento menu
3. Select "Bento Jobs"

## Cron Jobs

The module uses Magento's cron system to process the job queue. Events are processed every 5 minutes by default.

To check if cron is running properly:
```bash
bin/magento cron:status
```

## Troubleshooting

### Common Issues

1. Jobs not processing
    - Check if cron is running
    - Verify API credentials in configuration
    - Check system logs for errors

2. Missing events
    - Ensure the module is properly installed
    - Clear cache and check if observers are registered
    - Verify event-specific settings in configuration

3. Authentication errors
    - Double-check API credentials in configuration
    - Ensure Site UUID is correct
    - Check for proper Basic Auth encoding

### Debugging

The module logs all API communication and errors to:
- `var/log/system.log`
- `var/log/debug.log`

## Contributing

We welcome contributions! Please see our [contributing guidelines](CODE_OF_CONDUCT.md) for details on how to submit pull requests, report issues, and suggest improvements.

## License

The Bento SDK for Laravel is available as open source under the terms of the [MIT License](LICENSE.md).