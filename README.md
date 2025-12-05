# Lulu Print & Shipping API Integration Plugins

This project contains two WordPress plugins that integrate WooCommerce with Lulu’s Print and Shipping APIs.

---

## 1. Lulu Print API Integration Plugin

Automates print job creation with Lulu after WooCommerce payment completion.

### Features

- Automatically sends print jobs to Lulu after WooCommerce payment.
- Supports multiple products and package configurations.
- Uses selected Lulu shipping level from checkout for print job creation.
- Integrates with Lulu webhooks to update order status when shipped.
- Saves tracking ID, carrier, and tracking URL to order meta when shipment is confirmed.
- Sends tracking emails to customers when their order ships.
- Displays tracking information on order details and in completed order emails.
- Logs key events and payloads for debugging.

### Usage

This plugin is provided for demonstration and educational purposes only.  
No permission is granted to copy, clone, share, modify, or use this code in any way.

For any use beyond viewing, please contact the author for permission:

- Email: umeunegbupascal@gmail.com  
- Website: [umeunegbupascal.netlify.app](https://umeunegbupascal.netlify.app/)

---

## 2. Lulu Shipping API Integration Plugin

Provides live Lulu shipping rates and a custom shipping method at WooCommerce checkout.

### Features

- Registers a custom Lulu shipping method in WooCommerce.
- Fetches real-time shipping rates from Lulu based on cart and address.
- Supports shipping for multiple Lulu products in a single order.
- Shows estimated delivery dates for each shipping option.
- Saves selected Lulu shipping level to order meta for order processing.
- Validates shipping address fields and ensures required fields are present.
- Customizes WooCommerce checkout for a streamlined shipping experience:
  - Hides "Ship to a different address" checkbox.
  - Makes County/State field required and clearly labeled.
  - Ensures a shipping method is always selected.
- Logs missing fields, API errors, and exceptions for troubleshooting.

### Usage

This plugin is provided for demonstration and educational purposes only.  
No permission is granted to copy, clone, share, modify, or use this code in any way.

For any use beyond viewing, please contact the author for permission:

- Email: umeunegbupascal@gmail.com  
- Website: [umeunegbupascal.netlify.app](https://umeunegbupascal.netlify.app/)

---

## License

See the [LICENSE](LICENSE) file for details.
