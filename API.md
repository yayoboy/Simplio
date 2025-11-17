# Simplio CMS - API Documentation

## Setup

### Requirements
- PHP 8.2+
- MySQL 8.0+ or PostgreSQL
- Node.js 18+
- Composer
- NPM/Yarn

### Installation

```bash
# Clone repository
git clone <repository-url>
cd Simplio

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simplio
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate --seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

## Authentication

All protected API endpoints require authentication using Laravel Sanctum.

### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Response:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxx"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xxxxxxxxxxxxxxxxxxx"
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

Response:
```json
{
  "message": "Logged out successfully"
}
```

### Get Authenticated User
```http
GET /api/me
Authorization: Bearer {token}
```

Response:
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com"
}
```

### Test User
Use the test user created by seeder:
- Email: `test@example.com`
- Password: `password`

### Using Token
Include the auth token in all protected requests:
```
Authorization: Bearer {token}
```

## API Endpoints

### Sites

#### List user sites
```http
GET /api/sites
```
Response:
```json
{
  "data": [
    {
      "id": 1,
      "name": "My Website",
      "slug": "my-website",
      "domain": null,
      "theme_id": 1,
      "is_published": false,
      "pages_count": 3,
      "theme": {
        "id": 1,
        "name": "Modern Light"
      }
    }
  ]
}
```

#### Create site
```http
POST /api/sites
Content-Type: application/json

{
  "name": "My New Site",
  "slug": "my-new-site",
  "description": "Site description",
  "theme_id": 1,
  "settings": {},
  "meta": {}
}
```

#### Get site
```http
GET /api/sites/{id}
```

#### Update site
```http
PUT /api/sites/{id}
Content-Type: application/json

{
  "name": "Updated Name",
  "is_published": true
}
```

#### Delete site
```http
DELETE /api/sites/{id}
```

#### Publish site
```http
POST /api/sites/{id}/publish
```

#### Unpublish site
```http
POST /api/sites/{id}/unpublish
```

#### Duplicate site
```http
POST /api/sites/{id}/duplicate
Content-Type: application/json

{
  "name": "Site Copy"
}
```

### Pages

#### List site pages
```http
GET /api/sites/{siteId}/pages
```

#### Create page
```http
POST /api/sites/{siteId}/pages
Content-Type: application/json

{
  "title": "Home Page",
  "slug": "home",
  "description": "Welcome page",
  "layout": {
    "grid": "12-columns",
    "responsive": true
  },
  "meta_title": "Home - My Site",
  "meta_description": "Welcome to my site",
  "is_home": true
}
```

#### Get page
```http
GET /api/sites/{siteId}/pages/{pageId}
```

#### Update page
```http
PUT /api/sites/{siteId}/pages/{pageId}
Content-Type: application/json

{
  "title": "Updated Title",
  "is_published": true
}
```

#### Delete page
```http
DELETE /api/sites/{siteId}/pages/{pageId}
```

#### Publish page
```http
POST /api/pages/{id}/publish
```

#### Unpublish page
```http
POST /api/pages/{id}/unpublish
```

#### Duplicate page
```http
POST /api/pages/{id}/duplicate
```

#### Set as home page
```http
POST /api/pages/{id}/set-home
```

### Page Blocks

#### List page blocks
```http
GET /api/pages/{pageId}/blocks
```

#### Create block
```http
POST /api/pages/{pageId}/blocks
Content-Type: application/json

{
  "type": "text",
  "name": "Hero Section",
  "content": {
    "text": "Welcome to my site",
    "html": "<h1>Welcome</h1>"
  },
  "properties": {
    "fontSize": "24px",
    "color": "#000000",
    "padding": "20px"
  },
  "position": {
    "row": 1,
    "column": 1,
    "width": 12,
    "height": 2
  },
  "order": 0,
  "is_visible": true
}
```

**Available block types:**
- `text` - Text content
- `heading` - Headings (H1-H6)
- `image` - Single image
- `gallery` - Image gallery
- `video` - Video embed
- `html` - Custom HTML
- `button` - Call-to-action button
- `divider` - Visual separator
- `spacer` - Empty space
- `container` - Layout container
- `columns` - Multi-column layout

#### Update block
```http
PUT /api/pages/{pageId}/blocks/{blockId}
Content-Type: application/json

{
  "content": {
    "text": "Updated content"
  },
  "is_visible": false
}
```

#### Delete block
```http
DELETE /api/pages/{pageId}/blocks/{blockId}
```

#### Reorder blocks
```http
POST /api/blocks/reorder
Content-Type: application/json

{
  "page_id": 1,
  "blocks": [5, 3, 1, 2, 4]
}
```

### Themes

#### List all themes
```http
GET /api/themes
```

#### Get global themes only
```http
GET /api/themes/global
```

#### Get theme
```http
GET /api/themes/{id}
```

Response:
```json
{
  "id": 1,
  "name": "Modern Light",
  "slug": "modern-light",
  "description": "A clean and modern light theme",
  "is_global": true,
  "design_tokens": {
    "colors": {
      "primary": "#3B82F6",
      "secondary": "#8B5CF6",
      "background": "#FFFFFF",
      "text": "#111827"
    },
    "typography": {
      "font-family": "Inter, system-ui, sans-serif",
      "font-size-base": "16px"
    },
    "spacing": {
      "sm": "1rem",
      "md": "1.5rem",
      "lg": "2rem"
    }
  }
}
```

#### Create custom theme
```http
POST /api/themes
Content-Type: application/json

{
  "name": "My Custom Theme",
  "description": "Custom brand colors",
  "design_tokens": {
    "colors": {
      "primary": "#FF5733",
      "secondary": "#33C4FF"
    }
  }
}
```

#### Update theme
```http
PUT /api/themes/{id}
Content-Type: application/json

{
  "name": "Updated Theme Name",
  "design_tokens": {
    "colors": {
      "primary": "#000000"
    }
  }
}
```

Note: Only custom themes (is_global=false) owned by the user can be updated/deleted.

#### Delete theme
```http
DELETE /api/themes/{id}
```

## Pre-seeded Themes

The system comes with 3 professional themes:

1. **Modern Light** - Clean design with blue accents
2. **Dark Mode** - Professional dark theme
3. **Minimal** - Typography-focused minimal design

Each theme includes complete design tokens for:
- Colors (primary, secondary, accent, background, text)
- Typography (fonts, sizes, weights, line-height)
- Spacing (xs, sm, md, lg, xl)
- Border radius (sm, md, lg, full)
- Shadows (sm, md, lg)

## Error Responses

All endpoints return consistent error responses:

```json
{
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

HTTP Status Codes:
- `200` - Success
- `201` - Created
- `204` - No Content (successful deletion)
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Testing

Run tests:
```bash
php artisan test
```

## Next Steps

- Implement authentication endpoints
- Build Vue 3 page builder frontend
- Add media upload functionality
- Implement public page rendering
- Add caching layer
- Setup CI/CD pipeline
