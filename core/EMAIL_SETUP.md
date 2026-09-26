# Email setup – so the system can send emails

To have order emails (and other notifications) sent from the system, configure **two** places in the admin panel.

---

## 1. Global template (wrapper + sender)

**Path:** Admin → **Notification** → **Global Template** (first tab)

This is the **same layout for all emails** (to the client and to you): logo, body, signature.

- **Email From** – the address that appears as “From” (e.g. `info@travelsim.live`).  
  **Admin order notifications** are also sent **to** this address.
- **Email From Name** – the name shown as sender (e.g. your site name).
- **Email Body** – the **wrapper** for every letter:
  - **Logo** – add your company logo (e.g. `<img src="https://yoursite.com/logo.png" alt="{{site_name}}" style="max-height:60px;" />`).
  - **{{message}}** – **must be present**; here the system inserts the actual text (thank you for purchase, 24h, eSIM, etc. for the client; new order details for you).
  - **Signature / footer** – e.g. “© {{site_name}}. All rights reserved.”

So: one design (logo + {{message}} + signature) for both customer and admin emails. The text inside {{message}} comes from **Notification Templates** (see below).

**URL (if needed):**  
`/admin/notification/global/email`  
(e.g. `http://localhost/viserlab-esim-remake/admin/notification/global/email`)

---

## 2. Email sending method (SMTP / PHP Mail / API)

**Path:** Admin → **Notification** → **Email Setting** (second tab)

Here you choose **how** mail is sent and enter the credentials:

| Method        | What to set |
|---------------|-------------|
| **PHP Mail**  | No extra config. Uses server’s default mail. Often unreliable. |
| **SMTP**      | Host, Port, Encryption (SSL/TLS), Username, Password. Use this for Gmail, Outlook, or your host’s SMTP. |
| **SendGrid**  | App key from SendGrid. |
| **Mailjet**   | Public key and secret key from Mailjet. |

**Recommended:** Use **SMTP** with your provider, for example:

- **Gmail:** Host `smtp.gmail.com`, Port `587`, TLS, your Gmail address, and an [App Password](https://support.google.com/accounts/answer/185833) (not your normal password).
- **Outlook/Office365:** Host `smtp.office365.com`, Port `587`, TLS, your email and password.

**URL (if needed):**  
`/admin/notification/email/setting`  
(e.g. `http://localhost/viserlab-esim-remake/admin/notification/email/setting`)

---

## Where is the actual letter text? (thank you, 24h, eSIM, etc.)

**Path:** Admin → **Notification** → **Notification Templates**

- **Order - Placed (Customer)** – text for the **client**: “Thank you for your purchase”, “within 24 hours you’ll get the eSIM”, “check spam”, etc. This is what goes into {{message}} when we send to the customer.
- **Order - Placed (Admin notification)** – text for **you**: “New order”, customer name, email, order number. This is what goes into {{message}} when we send to the admin.

You can edit **Subject** and **Email Body** there. The **Global Template** (logo, {{message}}, signature) is the same for both; only the text inside {{message}} changes (customer vs admin).

---

## Quick checklist

1. **Global Template** – set “Email From”, “Email From Name”, and **Email Body** (logo, `{{message}}`, signature), save.
2. **Notification Templates** – edit “Order - Placed (Customer)” and “Order - Placed (Admin)” if you want to change the wording.
3. **Email Setting** – choose method (e.g. SMTP), fill in host/port/username/password (or API keys), save.
4. Use **“Test Mail”** on the Email Setting page to send a test and confirm it works.

After that, order emails (to the customer and to you) will be sent by the system using these settings.

---

## Avoiding Gmail / spam folder

If Gmail (or other providers) marks your emails as spam, do the following.

### 1. **SPF, DKIM, DMARC (most important)**

Your **sending domain** (e.g. `travelsim.live` if you send from `info@travelsim.live`) must have correct DNS records. Without them, Gmail often treats mail as spam.

- **SPF** – DNS TXT record that says which servers are allowed to send for your domain. Your host (Plesk / Hostme) usually has docs or a panel to add it.
- **DKIM** – Signs messages so providers can verify they’re from you. Again, set in Plesk/hosting (Mail settings → DKIM) and add the TXT record they give you.
- **DMARC** – Optional but useful: policy for what to do if SPF/DKIM fail. A simple policy reduces spam complaints.

**Where to set:** Plesk → Domains → your domain → Mail → Authentication (SPF/DKIM). Or ask your host for “SPF and DKIM for travelsim.live”.

### 2. **Sender address**

- Use **From** on your own domain (e.g. `info@travelsim.live` or `noreply@travelsim.live`), not a free address that doesn’t match the site.
- **Email From Name** in Global Template: use your site/product name so it’s recognisable in the inbox.

### 3. **Content**

- The system already sends **HTML + plain text**; avoid “spammy” wording (all caps, too many “FREE”, “Click here”, etc.).
- In Admin → Notification → Notification Templates you can tweak the text so it looks like normal transactional mail (order confirmation, support).

### 4. **Reputation**

- New domains/IPs often land in spam at first. After SPF/DKIM are correct, ask recipients to move the first mail to “Inbox” and mark “Not spam”; over time delivery improves.
- Avoid sending huge volumes at once from a new setup.

**Quick check:** Send a test to a Gmail address, then open the letter → three dots → “Show original”. Look for “SPF: PASS” and “DKIM: PASS”. If both pass, you’re in good shape; if not, fix SPF/DKIM on the domain first.
