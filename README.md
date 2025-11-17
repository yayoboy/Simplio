# Simplio CMS

Un Content Management System moderno con page builder drag-and-drop, costruito con Laravel 12 e Vue 3.

## Caratteristiche

- **Page Builder Visuale** - Interfaccia drag-and-drop per creare pagine con blocchi riutilizzabili
- **Sistema di Temi** - Design tokens configurabili per personalizzazione completa
- **Multi-sito** - Gestisci più siti web da un'unica installazione
- **11 Tipi di Blocchi** - Text, Heading, Image, Gallery, Video, HTML, Button, Divider, Spacer, Container, Columns
- **API RESTful** - Backend API completo con autenticazione Sanctum
- **Temi Predefiniti** - 3 temi professionali inclusi (Modern Light, Dark Mode, Minimal)
- **Responsive** - Layout responsive con configurazioni per mobile/tablet/desktop
- **SEO-Friendly** - Meta tags, slug personalizzabili, sitemap automatica

## Stack Tecnologico

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3, Tailwind CSS 4.0, Vite
- **Database:** MySQL 8+ / PostgreSQL / SQLite
- **Autenticazione:** Laravel Sanctum
- **Storage:** Local filesystem / S3-compatible

## Installazione

```bash
# Clone repository
git clone <repository-url>
cd Simplio

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_DATABASE=simplio
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Build frontend
npm run build

# Start server
php artisan serve
```

Visita `http://localhost:8000` nel browser.

## Struttura del Progetto

```
app/
├── Http/
│   ├── Controllers/Api/    # API Controllers
│   ├── Requests/           # Form Request Validation
│   └── Middleware/         # Custom Middleware
├── Models/                 # Eloquent Models
│   ├── Site.php           # Siti web
│   ├── Page.php           # Pagine
│   ├── PageBlock.php      # Blocchi contenuto
│   ├── Theme.php          # Temi
│   └── Media.php          # File caricati
├── Policies/              # Authorization Policies
└── Services/              # Business Logic
    ├── SiteService.php
    ├── PageService.php
    └── BlockService.php

database/
├── migrations/            # Database schema
└── seeders/
    ├── ThemeSeeder.php   # Temi predefiniti
    └── DatabaseSeeder.php

resources/
├── js/
│   ├── components/       # Vue 3 components
│   └── app.js
└── css/
    └── app.css           # Tailwind CSS

routes/
├── api.php               # API routes
└── web.php               # Web routes
```

## API Documentation

Vedi [API.md](API.md) per la documentazione completa delle API.

### Endpoints Principali

- `GET /api/sites` - Lista siti
- `POST /api/sites` - Crea sito
- `GET /api/sites/{id}/pages` - Lista pagine
- `POST /api/sites/{id}/pages` - Crea pagina
- `POST /api/pages/{id}/blocks` - Aggiungi blocco
- `POST /api/blocks/reorder` - Riordina blocchi
- `GET /api/themes` - Lista temi

## Database Schema

### Sites
- Informazioni sito, dominio, tema, settings, stato pubblicazione

### Pages
- Titolo, slug, layout, SEO meta tags, template, ordinamento

### PageBlocks
- Tipo blocco, contenuto (JSON), proprietà stile, posizione grid, visibilità

### Themes
- Nome, design tokens (colors, typography, spacing, radius, shadows)

### Media
- File, path, disk, dimensioni, alt text, variants (thumbnails, webp)

## Temi Predefiniti

1. **Modern Light** - Design pulito con accenti blu
2. **Dark Mode** - Tema scuro professionale
3. **Minimal** - Focus sulla tipografia

Ogni tema include design tokens per:
- Colori (primary, secondary, accent, background, text)
- Tipografia (font, dimensioni, pesi)
- Spacing (xs, sm, md, lg, xl)
- Border radius
- Shadows

## Sviluppo

```bash
# Development mode
npm run dev
php artisan serve

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint

# Clear cache
php artisan optimize:clear
```

## Prossimi Step

- [ ] Implementare autenticazione (login/register)
- [ ] Completare page builder Vue 3
- [ ] Aggiungere upload media con ottimizzazione
- [ ] Implementare rendering pubblico pagine
- [ ] Aggiungere cache layer (Redis)
- [ ] Setup CI/CD pipeline
- [ ] Sistema plugin/estensioni
- [ ] Marketplace temi

## Licenza

MIT License
