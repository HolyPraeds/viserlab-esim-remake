# Three storefronts, one code, one database

Host picks the skin. Users, plans, and orders stay in the current MySQL database.

| Host | Brand |
|---|---|
| travelsim.live / travelsim.test | TravelSim |
| payersim.com / payersim.test | PayerSim |
| travelpal.live / travelpal.test | TravelPal |

Admin stays on TravelSim (`/admin`). Storefront name/color/logo come from `core/config/brands.php`, not from overwriting General Settings.

## Local (XAMPP)

1. Add to `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1 travelsim.test
127.0.0.1 payersim.test
127.0.0.1 travelpal.test
```

2. `httpd-vhosts.conf` already includes [apache-vhosts-brands.conf](apache-vhosts-brands.conf). Restart Apache.

3. Open http://payersim.test and http://travelpal.test

Without vhosts you can set `SITE_BRAND=payersim` in `core/.env` and keep using the usual localhost URL.

## Arena

Do **not** clone extra folders or create extra databases.

On the existing TravelSim nginx vhost (`/etc/nginx/sites-available/travelsim`) add the extra names to `server_name`:

```
server_name travelsim.live www.travelsim.live payersim.com www.payersim.com travelpal.live www.travelpal.live;
```

Keep `root /var/www/travelsim;`. Then:

```
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d payersim.com -d www.payersim.com -d travelpal.live -d www.travelpal.live
cd /var/www/travelsim/core && php artisan migrate --force && php artisan optimize:clear
```

Point Cloudflare A records for those domains at the same arena IP as TravelSim.

## After deploy

```
cd core
php artisan migrate
```

New orders store `orders.brand`. Purchase emails use that brand even if the webhook hits travelsim.live.
