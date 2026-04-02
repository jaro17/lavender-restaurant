# Project Map

## Flow

`frontend/src/pug/index.pug`
-> frontend build
-> `public/index.html`
-> PHP server loads page
-> `src/Controller/Site/HomeController.php`
-> dynamic menu content is injected from the database

## What To Edit

- Website texts, contact details, gallery, footer:
  `frontend/src/pug/index.pug`
- Styling:
  `frontend/src/tailwind/tailwind.css`
- Generated frontend output:
  `public/index.html`
- Dynamic daily menu rendering:
  `src/Controller/Site/HomeController.php`
- Daily menu data loading/saving:
  `src/Service/WeeklyMenu.php`
- Menu data storage:
  `var/data/database.sqlite`

## Rules

- Edit `frontend/src/pug/index.pug`, not `public/index.html`.
- `public/index.html` is generated and can be overwritten by the frontend build.
- Daily menu items should not be maintained manually inside the Pug template.
- Menu content comes from the backend/database and is injected into the page at runtime.

## Typical Tasks

- Change contact info or gallery:
  edit `frontend/src/pug/index.pug`, then run frontend build
- Change menu layout shell:
  edit `frontend/src/pug/index.pug` and, if needed, `src/Controller/Site/HomeController.php`
- Change actual daily menu:
  update menu data in admin/database, no frontend template edit needed

## Useful Commands

- Frontend build:
  `cd frontend && npm run build`
- Frontend watch:
  `cd frontend && npm run watch`
- PHP server:
  `php -S localhost:8080 -t public`
