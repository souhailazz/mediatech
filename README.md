## mediatech

A compact, PHP-based demo website showcasing simple product pages and user interactions. It’s designed to be easy to run locally and to serve as a portfolio piece or small demo project on GitHub.

Why this project is useful

- Great for showing basic server-rendered PHP pages in a portfolio.
- Demonstrates common pages and flows found in small e-commerce/media sites (product view, sign-in, orders, reviews).
- Small, self-contained, and easy to adapt or extend.

Highlights

- Clean, single-folder PHP site (no heavy frameworks).
- Static and server-side pages for product listing, orders and reviews.
- Simple to run locally for demos and screenshots.

Quick list of notable files

- `homepage.php` — main entry page
- `signin.php` — sign-in page
- `product.php` — product listing / details
- `order.php` — order processing (basic)
- `review.php` — product reviews
- `favorites.php` — saved items
- `user.php` — user-related functions

Tech stack

- PHP (server-side rendering)
- HTML & CSS (layout and styling)
- Optional: MySQL (or any SQL) if you wire up persistent storage

Try it locally (quick)

1. Copy the project into your web server document root, for example `C:\xampp\htdocs\mediatech`.
2. Start Apache (and MySQL if you plan to use a database).
3. Open a browser and go to:

```powershell
http://localhost/mediatech/homepage.php
```

Or, to test quickly with PHP's built-in server from the project root:

```powershell
php -S localhost:8000
# then open http://localhost:8000/homepage.php
```

