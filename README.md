# Simplio CMS

Un Content Management System moderno con page builder drag-and-drop, costruito con Laravel 12 e Vue 3.

## Caratteristiche

### Core Features
- **Page Builder Visuale** - Interfaccia drag-and-drop per creare pagine con blocchi riutilizzabili
- **Sistema di Temi** - Design tokens configurabili con editor visuale
- **Multi-sito** - Gestisci più siti web da un'unica installazione
- **11 Tipi di Blocchi** - Text, Heading, Image, Gallery, Video, HTML, Button, Divider, Spacer, Container, Columns
- **Blocchi Annidati** - Supporto completo per gerarchie parent-child (Container e Columns)
- **API RESTful** - Backend API completo con autenticazione Sanctum
- **Temi Predefiniti** - 3 temi professionali inclusi (Modern Light, Dark Mode, Minimal)
- **Responsive** - Layout responsive con preview desktop/tablet/mobile
- **SEO-Friendly** - Meta tags, slug personalizzabili

### Media & Assets
- **Media Manager** - Libreria completa per gestione file e immagini
- **Varianti Automatiche** - Generazione automatica di thumbnail (150px, 300px, 768px, 1200px)
- **Integrazione Page Builder** - Media picker integrato per Image e Gallery blocks
- **Metadata EXIF** - Estrazione automatica dimensioni e informazioni immagine

### User Management & Security
- **Gestione Utenti** - Interfaccia admin completa per CRUD utenti
- **Ruoli e Permessi** - Sistema a 3 livelli (Admin, Editor, User)
- **Autenticazione Token** - Laravel Sanctum con protezione rotte
- **Middleware Autorizzazione** - Controlli granulari per azioni admin

### Performance & Caching
- **Page Caching** - Cache automatica 1 ora per pagine pubbliche con header X-Cache
- **Invalidazione Intelligente** - Observer pattern per invalidare cache su modifiche
- **Cache su 3 Livelli** - Site home, page specifica, invalidazione blocchi

### Testing & CI/CD
- **42 Test Automatici** - PHPUnit test suite completa (Feature + Unit)
- **GitHub Actions** - Pipeline CI/CD automatica con MySQL 8.0
- **Code Quality** - PHPStan e PHP CS Fixer integrati
- **Security Audit** - Controllo automatico vulnerabilità dipendenze

## Stack Tecnologico

- **Backend:** Laravel 12.38.1, PHP 8.4
- **Frontend:** Vue 3 (Composition API), Pinia, Vue Router
- **Styling:** Tailwind CSS 4.0, Vite
- **Database:** MySQL 8+ / PostgreSQL / SQLite
- **Autenticazione:** Laravel Sanctum (Token-based API)
- **Storage:** Local filesystem / S3-compatible
- **Media Processing:** Intervention Image
- **Testing:** PHPUnit, Laravel TestCase
- **CI/CD:** GitHub Actions

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

### Credenziali di Test

Dopo aver eseguito i seeder, puoi accedere con:
- Email: `test@example.com`
- Password: `password`

Il primo utente creato avrà automaticamente ruolo **admin**.

## Struttura del Progetto

```
app/
├── Http/
│   ├── Controllers/Api/    # API Controllers
│   ├── Requests/           # Form Request Validation
│   └── Middleware/         # Custom Middleware
├── Models/                 # Eloquent Models
│   ├── User.php           # Utenti (con ruoli)
│   ├── Site.php           # Siti web
│   ├── Page.php           # Pagine
│   ├── PageBlock.php      # Blocchi contenuto
│   ├── Theme.php          # Temi
│   └── Media.php          # File caricati
├── Policies/              # Authorization Policies
├── Observers/             # Model Observers (cache invalidation)
│   ├── SiteObserver.php
│   ├── PageObserver.php
│   └── PageBlockObserver.php
└── Services/              # Business Logic
    └── PageCacheService.php

database/
├── migrations/            # Database schema
└── seeders/
    ├── ThemeSeeder.php   # Temi predefiniti
    └── DatabaseSeeder.php

resources/
├── js/
│   ├── components/       # Vue 3 components
│   │   ├── builder/     # Page Builder components
│   │   ├── media/       # Media Manager components
│   │   └── users/       # User Management components
│   ├── stores/          # Pinia stores
│   │   ├── auth.js
│   │   ├── sites.js
│   │   ├── pages.js
│   │   ├── blocks.js
│   │   ├── media.js
│   │   └── users.js
│   ├── views/           # Page views
│   ├── router/          # Vue Router configuration
│   └── app.js
└── css/
    └── app.css           # Tailwind CSS

routes/
├── api.php               # API routes (autenticazione + gestione)
└── web.php               # Web routes (rendering pubblico)

tests/
├── Feature/             # Feature tests
│   ├── AuthTest.php
│   ├── UserManagementTest.php
│   ├── SiteManagementTest.php
│   ├── PageManagementTest.php
│   ├── PageBlockTest.php
│   └── PublicPageRenderingTest.php
└── Unit/                # Unit tests
    └── UserModelTest.php

.github/
└── workflows/
    └── ci.yml           # GitHub Actions pipeline
```

## API Documentation

### Autenticazione
- `POST /api/register` - Registra nuovo utente
- `POST /api/login` - Login utente
- `POST /api/logout` - Logout utente
- `GET /api/me` - Profilo utente corrente

### User Management (Admin Only)
- `GET /api/users` - Lista utenti (con search e filtri)
- `POST /api/users` - Crea nuovo utente
- `GET /api/users/{id}` - Dettagli utente
- `PUT /api/users/{id}` - Aggiorna utente
- `DELETE /api/users/{id}` - Elimina utente
- `GET /api/users/roles` - Lista ruoli disponibili

### Sites
- `GET /api/sites` - Lista siti
- `POST /api/sites` - Crea sito
- `GET /api/sites/{id}` - Dettagli sito
- `PUT /api/sites/{id}` - Aggiorna sito
- `DELETE /api/sites/{id}` - Elimina sito
- `POST /api/sites/{id}/publish` - Pubblica sito
- `POST /api/sites/{id}/unpublish` - Rimuovi pubblicazione
- `POST /api/sites/{id}/duplicate` - Duplica sito
- `POST /api/sites/{id}/theme` - Aggiorna tema sito

### Pages
- `GET /api/sites/{id}/pages` - Lista pagine
- `POST /api/sites/{id}/pages` - Crea pagina
- `GET /api/pages/{id}` - Dettagli pagina
- `PUT /api/pages/{id}` - Aggiorna pagina
- `DELETE /api/pages/{id}` - Elimina pagina
- `POST /api/pages/{id}/publish` - Pubblica pagina
- `POST /api/pages/{id}/unpublish` - Rimuovi pubblicazione
- `POST /api/pages/{id}/duplicate` - Duplica pagina
- `POST /api/pages/{id}/set-home` - Imposta come home page

### Blocks
- `GET /api/pages/{id}/blocks` - Lista blocchi (solo root)
- `POST /api/pages/{id}/blocks` - Crea blocco
- `PUT /api/blocks/{id}` - Aggiorna blocco
- `DELETE /api/blocks/{id}` - Elimina blocco
- `POST /api/blocks/reorder` - Riordina blocchi

### Media
- `GET /api/sites/{id}/media` - Lista media (con filtri)
- `POST /api/sites/{id}/media` - Upload file
- `GET /api/media/{id}` - Dettagli media
- `PUT /api/media/{id}` - Aggiorna metadata
- `DELETE /api/media/{id}` - Elimina media
- `POST /api/media/{id}/regenerate` - Rigenera varianti

### Themes
- `GET /api/themes` - Lista temi
- `GET /api/themes/global` - Temi globali

### Public Routes
- `GET /sites/{slug}` - Visualizza home page sito
- `GET /sites/{slug}/{page}` - Visualizza pagina specifica

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

### Modalità Development

```bash
# Terminal 1 - Frontend build con hot reload
npm run dev

# Terminal 2 - Laravel server
php artisan serve
```

Naviga su `http://localhost:8000` per vedere l'applicazione.

### Testing

Simplio include 72+ test automatici per backend e funzionalità core.

```bash
# Run all tests
php artisan test

# Run tests in parallel (più veloce)
php artisan test --parallel

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/UserManagementTest.php

# Run with coverage report
php artisan test --coverage

# Run specific test method
php artisan test --filter test_admin_can_create_user
```

**Test Coverage:**
- ✅ Authentication (12 tests)
- ✅ User Management (15 tests)
- ✅ Site Management (10 tests)
- ✅ Page Management (18 tests)
- ✅ Block Management (14 tests)
- ✅ Public Rendering (18 tests)
- ✅ User Model (5 tests)

Vedi [TESTING.md](TESTING.md) per guida completa ai test.

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Run static analysis (se configurato)
./vendor/bin/phpstan analyse

# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### CI/CD Pipeline

Il progetto include un workflow GitHub Actions completo che:

1. **Backend Tests** - Esegue tutti i test PHPUnit con MySQL 8.0
2. **Frontend Build** - Compila asset Vue/Vite e carica artifacts
3. **Code Quality** - Esegue PHPStan e PHP CS Fixer (opzionale)
4. **Security Audit** - Controlla vulnerabilità in dipendenze
5. **Deploy** - Pronto per deployment automatico (da configurare)

**Workflow Triggers:**
- Push su branch: `main`, `develop`, `claude/**`
- Pull request verso: `main`, `develop`

Visualizza i risultati nella tab **Actions** di GitHub.

## Funzionalità Implementate

- ✅ Autenticazione completa (login/register/logout)
- ✅ Page Builder Vue 3 con drag-and-drop
- ✅ 11 tipi di blocchi funzionanti
- ✅ Media Manager con upload e varianti automatiche
- ✅ Rendering pubblico pagine con cache
- ✅ User Management con ruoli (Admin/Editor/User)
- ✅ Theme Customization con editor visuale
- ✅ Blocchi annidati (Container e Columns)
- ✅ Page caching con invalidazione automatica
- ✅ Test suite completa (72+ test)
- ✅ CI/CD pipeline con GitHub Actions
- ✅ Protezione rotte con middleware
- ✅ Responsive preview (desktop/tablet/mobile)

## Roadmap Futura

- [ ] Revisione sistema e aggiunta permissions granulari
- [ ] Sistema plugin/estensioni con hooks
- [ ] Marketplace temi e blocchi
- [ ] Export/import siti completi
- [ ] Versioning e cronologia modifiche
- [ ] A/B testing integrato
- [ ] Analytics dashboard
- [ ] Multi-language support (i18n)
- [ ] Headless CMS API mode
- [ ] GraphQL API endpoint
- [ ] Webhook system per integrazioni
- [ ] Redis caching layer opzionale
- [ ] CDN integration per assets
- [ ] SEO audit automatico
- [ ] Accessibility checker

## Ruoli e Permessi

Simplio utilizza un sistema a 3 livelli:

### Admin
- Gestione completa utenti (CRUD)
- Accesso a tutte le funzionalità
- Configurazione globale sistema

### Editor
- Gestione siti e contenuti
- Creazione/modifica pagine e blocchi
- Upload media
- Pubblicazione contenuti

### User
- Accesso limitato ai siti assegnati
- Visualizzazione contenuti
- Modifica profilo personale

## Licenza

MIT License
