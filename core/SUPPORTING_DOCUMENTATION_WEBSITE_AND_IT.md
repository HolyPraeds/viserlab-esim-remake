# Supporting Documentation: Website Development & Technical Support / IT

**Purpose:** Supporting documentation for acquirer onboarding (website development and technical support & IT).

**Version:** 1.0  
**Last updated:** February 2026

---

## 1. Website development

### 1.1 Purpose and scope of the website

The website is an e-commerce platform for selling eSIM data plans. Users can:

- Browse data plans by country or region, view pricing and details.
- Purchase eSIMs as guests or registered users.
- Pay via the integrated payment gateway (card and other methods as configured).
- After payment, receive eSIM activation details (QR code and instructions).
- Top up account balance (deposits) for registered users.
- Contact support via contact form or support tickets (registered users).
- View policy pages (Privacy, Terms, Refund, Cookies, etc.).

Administrators manage orders, users, payment gateways, content, and support tickets through a separate admin area.

### 1.2 Technology used

- **Backend:** PHP 8.3, Laravel 11 (MVC framework).
- **Frontend:** Server-rendered Blade templates, responsive layout; CSS and JavaScript for UI (carousels, lightbox, counters, viewport utilities).
- **Database:** Relational database (MySQL in production); all business data (users, orders, plans, gateways, tickets) stored there.
- **Authentication:** Session-based for web users; API tokens (Laravel Sanctum) for programmatic access where used.
- **Payments:** Integration with payment gateway (acquirer). Card data is not entered or stored on our servers; payment is collected on the gateway’s page or via secure redirect. Our system only stores order references, amounts, and statuses.
- **Email:** Configurable SMTP or API-based mail service for transactional emails (order confirmation, support, etc.).
- **Hosting:** Web server (e.g. Apache/Nginx with PHP); production runs in a controlled environment with HTTPS.

### 1.3 Architecture (high level)

- **Public site:** Home page, country/region plan listings, search, destination pages, about/contact, policy pages, language switching. Served by the main web application.
- **User area:** Registration and login; purchase flow (plan selection → checkout → redirect to payment → return/success); order history and tracking; deposits; support tickets. All behind authentication where required.
- **Payment flow:** User selects plan and proceeds to pay → application creates an order and sends the user to the payment gateway → user completes payment on the gateway → gateway sends a server-to-server notification (webhook/callback) and/or redirects user back → application updates order status and, on success, delivers eSIM details (e.g. on success page or by email).
- **Admin area:** Separate entry point and routes; access restricted to staff. Used for orders, users, gateway configuration, content, and support ticket replies.
- **API:** Read-only HTTP API for countries, regions, plans, and currency rates; used for integration or front-end features. No payment or sensitive data exposed.
- **eSIM provider (eSIM Access):** After payment success, the application automatically orders the eSIM profile from eSIM Access (https://docs.esimaccess.com/) using the configured API key. Keys are set in `.env` as `ESIM_ACCESS_CODE` and `ESIM_SECRET_KEY`. The same key is used for fetching plans and ordering; if `ESIM_ACCESS_CODE` is set, it overrides the “Plan API” value in Admin → Manage APIs. Webhooks from eSIM Access (order status, data usage, etc.) are received at `POST /ipn/esimaccess`; configure this URL in the eSIM Access partner console if needed.

### 1.4 Development and maintenance

- Application code is kept under version control. Changes are tested before release.
- Configuration (database, URLs, gateway keys, mail, etc.) is stored in environment variables; production uses production-specific values and has debug mode disabled.
- Deployment: updated code and assets are deployed to the server; database schema changes are applied via migrations; application and config caches are cleared so changes take effect.
- Dependencies are managed with a standard PHP dependency manager; front-end libraries are included or loaded in a controlled way. No card data or gateway secrets are stored in the codebase.

### 1.5 Security (development and operation)

- All forms are protected against cross-site request forgery (CSRF).
- User input is validated and escaped to prevent injection and XSS.
- Passwords are hashed with a secure algorithm and not stored in plain text.
- Sensitive configuration (database credentials, API keys, gateway credentials) is kept in environment configuration and not committed to version control.
- Production runs with debug mode off so that technical details are not exposed to users.
- HTTPS is used in production for the entire site, including payment and login pages.
- Card data is never stored or processed on our servers; it is handled only by the payment gateway.

---

## 2. Technical support and IT

### 2.1 Support channels

- **Contact form:** Available on the website. Submissions are processed by the application (e.g. sent to support email or stored for handling). Used for general enquiries.
- **Support tickets:** Logged-in users can open tickets, attach files, view replies, and close tickets. Staff reply and update status via the admin interface. Used for order and account issues.
- **Email:** A dedicated support email address is used for correspondence; sending is configured in the application. Used for replies to contact form, tickets, and direct user emails.

### 2.2 Handling user requests and incidents

- **Support tickets:** Each ticket is stored in the database with status (e.g. open, replied, closed). Staff view and reply from the admin panel; users see the thread in their account. Target response times are defined internally (SLA).
- **Contact form:** Messages are received and processed by the backend; they are either forwarded to support email or stored and handled from the admin side. Responses follow the same support process.
- **Payment and order issues:** Investigated using order ID, transaction reference from the gateway, and gateway status. Resolution (refund, resend eSIM, etc.) is done in line with the published refund and support policies. Disputes or chargebacks are handled with the acquirer according to their process.

### 2.3 IT and infrastructure

- **Hosting:** The website and database are hosted on a managed or VPS environment. Provider, location, and SLA are defined per environment.
- **Database:** Production uses a relational database (MySQL). Backups and retention follow the hosting provider’s and/or internal procedures. Access to the database is restricted to authorised IT/admin personnel.
- **Logs:** The application writes logs (errors, important events). Logs are stored on the server and retained according to internal policy. They are used for troubleshooting and incident analysis.
- **Monitoring:** Server and application availability and errors can be monitored (e.g. uptime checks, log-based alerts). Responsibility and tools are assigned internally.

### 2.4 Access control and responsibilities

- **Code and deployment:** Only authorised developers/IT have access to the codebase and deployment process.
- **Admin panel:** Access to the Laravel admin (orders, users, gateways, content, tickets) is restricted to authorised staff. Credentials are managed securely; two-factor authentication is used if configured.
- **Servers and database:** Access to servers and direct database access are limited to IT/administrators under the principle of least privilege.

### 2.5 Change and incident management

- **Changes:** Code and configuration changes are deployed in a controlled way. Changes that affect payment or gateway integration are reviewed before production deployment.
- **Incidents:** Malfunctions (e.g. site down, payment errors) are prioritised, handled by IT/support, and escalated according to internal rules. Significant incidents can be reviewed afterwards to improve processes.

### 2.6 Policies and compliance

- The site publishes Privacy Policy, Terms of Service, Refund Policy, and Cookie Policy. Links are provided in the footer and, where required, on registration or checkout.
- This document describes how the website is developed and how technical support and IT are organised, for submission to the acquirer as supporting documentation.

---

## 3. Payment architecture & data security (for acquirer)

*This section is intended for the acquirer / compliance department. It describes the payment integration, PCI DSS scope, and measures taken to comply with PSD2 and card scheme requirements.*

### 3.1 Integration method: Hosted Payment Page (HPP)

We use a **Hosted Payment Page (HPP)** integration. The customer remains on our website until checkout; they are then redirected to the acquirer’s/gateway’s secure page to enter payment details and complete the transaction, and are redirected back to our site for the outcome.

- **Cardholder data:** No PAN, CVV, or expiry date is ever transmitted to or stored on our infrastructure. All card data entry and processing takes place on the acquirer’s/gateway’s PCI-DSS compliant environment.
- **PCI DSS scope:** Our platform is **out of scope** for sensitive cardholder data. We qualify for **PCI DSS SAQ A** (or equivalent, as determined by the acquirer) for this integration type.

### 3.2 Technical payment flow (step-by-step)

1. **Checkout:** The customer selects products (eSIM plans) and proceeds to the checkout/payment page on our server (`https://travelsim.live`).
2. **Order creation:** Our server creates an order and a payment session in our database (order reference, amount, currency, customer identifier).
3. **Payment initiation:** Our server sends a server-to-server API request to the payment gateway (using API credentials) to create a payment session. We send: amount, currency, order reference, return URL, webhook URL, and customer data (e.g. email, name, locale). We do **not** send any card data.
4. **Redirection:** The gateway returns a secure URL of the hosted payment page. Our server redirects the customer to that URL. All payment data entry (card details, 3D Secure, etc.) occurs **only** on the gateway’s servers.
5. **Customer authentication:** The customer enters card data on the gateway’s page. The gateway performs Strong Customer Authentication (SCA) as required (e.g. 3D Secure 2.0 challenge). Our servers do not participate in this step.
6. **Return:** After the transaction, the gateway redirects the customer back to our return URL (success or failure/cancel). We use this redirect only to show the appropriate outcome page; we do **not** rely on it alone to finalise the order.
7. **Confirmation:** Our server receives an asynchronous **webhook** (server-to-server notification) from the gateway with the final transaction status. We treat the webhook as the authoritative source for updating the order (completed or failed). Only after a successful webhook do we deliver the product (eSIM) and send order confirmation email.

### 3.3 Strong Customer Authentication (SCA) / PSD2

- **3D Secure:** Our integration uses the gateway’s Hosted Payment Page. All 3D Secure (3DS 2.0) flows—including challenge (e.g. SMS/push)—are handled by the gateway. We do not capture or process authentication responses; we only receive the final payment result via webhook and redirect.
- **Confirmation to acquirer:** We confirm that we use the gateway’s HPP and that SCA/3DS is applied by the gateway as per PSD2. We do not bypass or disable SCA for card payments.
- **Exemptions:** If we use any low-value or other exemptions (e.g. transactions below a threshold), we will ensure that such transactions are clearly indicated in the API request as per the gateway’s specification. Currently we do not rely on exemptions for the main flow.

### 3.4 Merchant / descriptor data (Mandate 2026/2027)

- **DBA (Doing Business As) name:** The trading name shown to the cardholder (e.g. on bank statement) is the one we configure in the gateway’s back office and/or pass in the API when supported. We ensure it is recognisable and consistent with our brand and contract with the acquirer.
- **Support contact in payment request:** Where the gateway’s API supports it, we pass our **support contact** (email and/or phone) in the payment session so that it can appear on the electronic receipt/statement. These details are the same as those published on our website and used for customer support.

### 3.5 Webhooks and status synchronisation

- **Webhook endpoint:** We expose a dedicated HTTPS endpoint that receives server-to-server notifications from the gateway (e.g. payment completed, declined, cancelled).
- **Signature verification:** Every incoming webhook request is verified using the gateway’s signature mechanism (e.g. HMAC-SHA256 over the raw body). If the signature does not match, we respond with an error and do **not** update the order. This prevents forged “success” notifications.
- **Idempotency:** We process webhooks in an idempotent way. For a given payment (identified by gateway payment ID and/or our order reference), we update the order status only from “pending”/“initiated” to “completed” or “failed” once. Repeated webhooks for the same payment do **not** result in double delivery of the product or double crediting of the user balance.
- **Authoritative source:** The final order status is set from the webhook result, not from the customer’s browser or redirect URL parameters.

### 3.6 Order summary / basket (Card Scheme requirements)

- **Data sent to gateway:** When creating the payment session we send at least: order reference, total amount, currency, and a short description (e.g. “eSIM Order #12345”). If the gateway’s API supports an **items** array (product name, quantity, unit price, VAT rate, total), we pass that so the transaction is clearly identifiable on the cardholder’s statement.
- **Currency:** The payment currency (e.g. EUR) is set per order and sent to the gateway. It matches the currency of our contract with the acquirer and the prices displayed to the customer.
- **Transparency:** We ensure that the description and/or line items sent to the gateway allow the cardholder to recognise the purchase on their statement, in line with applicable card scheme and mandate requirements (e.g. 2026/2027).

### 3.7 Security measures

- **API credentials:** Gateway API keys and signing secrets are stored only in environment variables (or secure configuration), never in source code or version control.
- **TLS:** All communication between our server, the customer’s browser, and the payment gateway uses **TLS 1.2 or higher**. We do not use SSL 3.0 or TLS 1.0 for payment or login.
- **IP (if required):** If the acquirer requires IP whitelisting, our server calls the gateway API from a known static IP, which we can provide upon request.

### 3.8 Go-live checklist (frontend and operations)

- **Payment page:** The checkout page clearly indicates that the customer will be redirected to a secure payment page to enter card details. We do not collect card data on our own pages.
- **Logos:** Where required by the acquirer or card schemes, we display the relevant logos (e.g. Visa, Mastercard, Maestro, acquirer logo) in the footer or on the checkout/payment selection page.
- **Order confirmation:** After we receive a successful webhook, we send the customer an **email confirmation** with order details (order number, amount, product summary). We do not send card data or full card numbers in any email.
- **Policies:** Terms of Service and Refund Policy are published on our site and linked from the footer and/or checkout. We provide the direct URLs to the acquirer when requested for production activation.

---

## 4. Documents and links to provide to the acquirer

When requested by the acquirer, we provide:

- **PCI DSS SAQ A** (or the applicable SAQ): completed self-assessment questionnaire.
- **Network / ASV scan report:** if required by the acquirer (e.g. for higher volumes), we provide a vulnerability scan report from an Approved Scanning Vendor (ASV), including the list of scanned URLs and IPs.
- **Terms of Service URL:** direct link to the Terms of Service page.
- **Refund Policy URL:** direct link to the Refund Policy page.
- **Privacy Policy URL:** direct link to the Privacy Policy page (for reference).

---

## 5. Summary

- **Website:** E-commerce platform for eSIM sales; Laravel (PHP 8.3), MySQL, Blade frontend; payment via external gateway with no card data on our systems; public site, user area (orders, deposits, tickets), admin area, and read-only API.
- **Development:** Version-controlled code, environment-based configuration, structured deployment and migrations, and security measures (CSRF, validation, hashing, HTTPS, no storage of card data).
- **Support and IT:** Contact form, support tickets, and email; tickets and orders managed in the application; IT responsible for hosting, backups, logs, access control, and change/incident management; policies published on the site.
- **Payments (for acquirer):** Hosted Payment Page (HPP); no card data on our infrastructure; PCI DSS SAQ A scope; SCA/3DS handled by gateway; webhook signature verification and idempotent processing; merchant/support data and order summary as per gateway API; TLS 1.2+; order confirmation email after webhook success.

---

## 6. Authorized representative

| Field | Value |
|-------|--------|
| **Company name** | BROOKBURN INTERNATIONAL LTD |
| **Website** | https://travelsim.live |
| **Registration number** | 14153895 |
| **Country of registration** | United Kingdom (UK) |
| **Authorized representative** | [Name] |
| **Title** | [e.g. CTO / Head of IT] |
| **Date** | February 2026 |

---

*This document is submitted as supporting documentation for website development, technical support / IT, and payment architecture for acquirer onboarding. Complete the authorized representative name and title in section 6 before signing.*
