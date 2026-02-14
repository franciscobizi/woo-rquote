# Woo RQuote

**Woo RQuote** is a production-ready WordPress plugin for WooCommerce that allows customers to add products to a quote list and submit a request instead of (or in addition to) purchasing directly.

## Features
- **AJAX-powered Cart**: Add, remove, and update quantities without page refreshes.
- **Dynamic Shortcode**: Display the quote list and request form using `[woo_rquote_list]`.
- **Custom Form**: Collect Name, Email, Address, Phone, Date, and Message.
- **Order Integration**: Automatically creates a "Pending" order in WooCommerce upon submission.
- **Custom Emails**: Professional HTML email templates sent to both admin and customer.
- **Admin Settings**: Options to hide prices and "Add to Cart" buttons globally.

## Installation
1. Download the `woo-rquote.zip` file.
2. In your WordPress admin, go to **Plugins > Add New**.
3. Click **Upload Plugin** and select the zip file.
4. **Activate** the plugin.
5. Ensure **WooCommerce** is installed and active.

## Configuration
1. Go to **WooCommerce > Quote Settings** to configure visibility options.
2. Create a new page and add the shortcode `[woo_rquote_list]`.

## AJAX Endpoints & Payloads
The plugin uses the following AJAX endpoints (via `admin-ajax.php`):

### 1. Add to Quote
- **Action**: `rquote_add_to_cart`
- **Payload**:
  ```json
  {
    "action": "rquote_add_to_cart",
    "product_id": 123,
    "quantity": 1,
    "nonce": "YOUR_NONCE"
  }
  ```

### 2. Update Quantity
- **Action**: `rquote_update_quantity`
- **Payload**:
  ```json
  {
    "action": "rquote_update_quantity",
    "product_id": 123,
    "quantity": 5,
    "nonce": "YOUR_NONCE"
  }
  ```

### 3. Remove Item
- **Action**: `rquote_remove_from_cart`
- **Payload**:
  ```json
  {
    "action": "rquote_remove_from_cart",
    "product_id": 123,
    "nonce": "YOUR_NONCE"
  }
  ```

### 4. Submit Request
- **Action**: `rquote_submit_request`
- **Payload**:
  ```json
  {
    "action": "rquote_submit_request",
    "form_data": "rquote_name=John&rquote_email=john@example.com&...",
    "nonce": "YOUR_NONCE"
  }
  ```

## Author
**Francisco Bizi**
