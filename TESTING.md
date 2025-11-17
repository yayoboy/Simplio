# Simplio CMS - Testing Guide

## Setup for Testing

```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Setup database (use SQLite for quick testing)
# In .env, set:
# DB_CONNECTION=sqlite

touch database/database.sqlite

# 4. Run migrations and seeders
php artisan migrate:fresh --seed

# 5. Start development servers (2 terminals)
# Terminal 1:
npm run dev

# Terminal 2:
php artisan serve
```

## Test Credentials

**Default Test User:**
- Email: `test@example.com`
- Password: `password`

**Seeded Themes:**
1. Modern Light (ID: 1)
2. Dark Mode (ID: 2)
3. Minimal (ID: 3)

---

## ✅ Test Checklist

### 1. Authentication Tests

#### Register New User
- [ ] Navigate to `/register`
- [ ] Enter name, email, password (min 8 chars), confirm password
- [ ] Click "Sign up"
- [ ] Should redirect to dashboard
- [ ] Token should be stored in localStorage

**Expected:** User created, auto-logged in, token saved

#### Login
- [ ] Navigate to `/login`
- [ ] Enter test@example.com / password
- [ ] Click "Sign in"
- [ ] Should redirect to dashboard

**Expected:** Successful login, redirect to dashboard

#### Logout
- [ ] Click "Logout" in navigation
- [ ] Should redirect to `/login`
- [ ] Token should be removed from localStorage

**Expected:** Logged out, cannot access protected routes

#### Protected Routes
- [ ] Try accessing `/` without token
- [ ] Try accessing `/sites` without token

**Expected:** Redirected to `/login`

---

### 2. Sites CRUD Tests

#### Create Site
- [ ] Login and navigate to `/sites`
- [ ] Click "Create Site" button
- [ ] Modal opens with form
- [ ] Fill in:
  - Name: "Test Site"
  - Slug: "test-site" (or leave empty for auto-generation)
  - Description: "My test website"
  - Domain: "test.example.com"
  - Theme: Select "Modern Light"
- [ ] Click "Create"
- [ ] Modal closes
- [ ] New site appears in grid

**Expected:** Site created successfully, appears in list

**API Call:** `POST /api/sites`
```json
{
  "name": "Test Site",
  "slug": "test-site",
  "description": "My test website",
  "domain": "test.example.com",
  "theme_id": 1
}
```

#### View Sites List
- [ ] Navigate to `/sites`
- [ ] Sites displayed in grid (3 columns on large screens)
- [ ] Each card shows:
  - Site name
  - Description
  - Page count
  - Published status badge (Draft/Published)
  - Edit icon
  - Delete icon
  - Manage link
  - Publish/Unpublish button
  - Duplicate button

**Expected:** All sites displayed with correct information

**API Call:** `GET /api/sites`

#### Edit Site
- [ ] Click edit icon (pencil) on a site card
- [ ] Modal opens with form pre-filled
- [ ] Change name to "Updated Test Site"
- [ ] Change description
- [ ] Click "Update"
- [ ] Modal closes
- [ ] Changes reflect in card immediately

**Expected:** Site updated, UI refreshed

**API Call:** `PUT /api/sites/{id}`

#### Delete Site
- [ ] Click delete icon (trash) on a site card
- [ ] Confirmation modal opens
- [ ] Displays site name and warning
- [ ] Click "Delete"
- [ ] Modal closes
- [ ] Site removed from grid

**Expected:** Site deleted, removed from list

**API Call:** `DELETE /api/sites/{id}`

#### Publish/Unpublish Site
- [ ] Find a site with "Draft" status
- [ ] Click "Publish" button
- [ ] Status badge changes to "Published" (green)
- [ ] Button text changes to "Unpublish"
- [ ] Click "Unpublish"
- [ ] Status changes back to "Draft" (gray)

**Expected:** Toggle works, status updates immediately

**API Calls:**
- `POST /api/sites/{id}/publish`
- `POST /api/sites/{id}/unpublish`

#### Duplicate Site
- [ ] Click "Duplicate" button on a site
- [ ] Modal opens with name input
- [ ] Default name is "{Site Name} (Copy)"
- [ ] Change name if desired
- [ ] Click "Duplicate"
- [ ] Modal closes
- [ ] New site appears at top of list
- [ ] Has all same properties except name

**Expected:** Site duplicated with new name

**API Call:** `POST /api/sites/{id}/duplicate`
```json
{
  "name": "Test Site (Copy)"
}
```

#### Empty State
- [ ] Delete all sites
- [ ] Empty state message appears
- [ ] "Create Your First Site" button visible
- [ ] Clicking button opens create modal

**Expected:** Proper empty state UI

---

### 3. Site Detail View Tests

#### Navigate to Site Detail
- [ ] Click "Manage →" link on a site card
- [ ] Redirects to `/sites/{id}`
- [ ] Shows site name and description
- [ ] Shows "Pages" section

**Expected:** Site detail page loads

**API Call:** `GET /api/sites/{id}`

---

### 4. Pages CRUD Tests

#### Create Page
- [ ] Navigate to a site detail page (`/sites/{id}`)
- [ ] Click "Create Page" button
- [ ] Modal opens with form
- [ ] Fill in:
  - Title: "About Us" (required)
  - Slug: "about-us" (or leave empty for auto-generation)
  - SEO Title: "About Us - Company Name"
  - SEO Description: "Learn more about our company"
  - Is Home: Check if you want this as home page
- [ ] Click "Create"
- [ ] Modal closes
- [ ] New page appears in table

**Expected:** Page created successfully, appears in list

**API Call:** `POST /api/sites/{siteId}/pages`
```json
{
  "title": "About Us",
  "slug": "about-us",
  "meta_title": "About Us - Company Name",
  "meta_description": "Learn more about our company",
  "is_home": false
}
```

#### View Pages List
- [ ] Navigate to a site detail page
- [ ] Pages displayed in table
- [ ] Each row shows:
  - Page title
  - Slug
  - Published status badge (Draft/Published)
  - Home page indicator (if is_home)
  - Set as Home icon (if not home)
  - Publish/Unpublish button
  - Edit icon
  - Duplicate icon
  - Delete icon

**Expected:** All pages displayed with correct information

**API Call:** `GET /api/sites/{siteId}/pages`

#### Edit Page
- [ ] Click edit icon (pencil) on a page row
- [ ] Modal opens with form pre-filled
- [ ] Change title to "Updated About Us"
- [ ] Change SEO fields
- [ ] Click "Update"
- [ ] Modal closes
- [ ] Changes reflect in table immediately

**Expected:** Page updated, UI refreshed

**API Call:** `PUT /api/pages/{id}`

#### Delete Page
- [ ] Click delete icon (trash) on a page row
- [ ] Confirmation modal opens
- [ ] Displays page title and warning
- [ ] Click "Delete"
- [ ] Modal closes
- [ ] Page removed from table

**Expected:** Page deleted, removed from list

**API Call:** `DELETE /api/pages/{id}`

#### Publish/Unpublish Page
- [ ] Find a page with "Draft" status
- [ ] Click "Publish" button
- [ ] Status badge changes to "Published" (green)
- [ ] Button text changes to "Unpublish"
- [ ] Click "Unpublish"
- [ ] Status changes back to "Draft" (gray)

**Expected:** Toggle works, status updates immediately

**API Calls:**
- `POST /api/pages/{id}/publish`
- `POST /api/pages/{id}/unpublish`

#### Duplicate Page
- [ ] Click "Duplicate" icon on a page
- [ ] Modal opens with title input
- [ ] Default title is "{Page Title} (Copy)"
- [ ] Change title if desired
- [ ] Click "Duplicate"
- [ ] Modal closes
- [ ] New page appears at top of table
- [ ] Has all same properties except title and is not published

**Expected:** Page duplicated with new title

**API Call:** `POST /api/pages/{id}/duplicate`
```json
{
  "title": "About Us (Copy)"
}
```

#### Set as Home Page
- [ ] Find a page that is not the home page
- [ ] Click the "Set as Home" icon (house)
- [ ] Page immediately shows "Home Page" badge
- [ ] Previous home page loses its badge
- [ ] Only one page shows as home

**Expected:** Home page changed, only one home page exists

**API Call:** `POST /api/pages/{id}/set-home`

#### Empty State
- [ ] Delete all pages from a site (or use site with no pages)
- [ ] Empty state message appears
- [ ] "Create Your First Page" button visible
- [ ] Clicking button opens create modal

**Expected:** Proper empty state UI

---

### 5. Page Builder Tests

#### Open Page Builder
- [ ] Navigate to a site detail page
- [ ] Click "Builder" button on any page
- [ ] Page Builder opens at `/sites/{siteId}/pages/{pageId}/builder`
- [ ] Shows page title and site name in header
- [ ] Three-column layout visible (Blocks Palette | Canvas | Properties)

**Expected:** Page Builder interface loads

#### Add Blocks from Palette
- [ ] Palette shows all 11 block types organized by category
- [ ] Categories: Content, Media, Interactive, Layout, Advanced
- [ ] Click "Text Block" from palette
- [ ] Text block appears in canvas
- [ ] Block is automatically selected
- [ ] Properties panel appears on right

**Expected:** Blocks can be added and selected

**API Call:** `POST /api/pages/{pageId}/blocks`

#### Search Blocks
- [ ] Type "button" in search box
- [ ] Only Button block shown
- [ ] Clear search
- [ ] All blocks visible again

**Expected:** Search filters blocks correctly

#### Edit Block Content (Text Block)
- [ ] Select a text block
- [ ] Properties panel shows on right
- [ ] Change text content in textarea
- [ ] Change text alignment to "center"
- [ ] Change font size to "large"
- [ ] See changes reflected in canvas immediately

**Expected:** Properties update block visually

**API Call:** `PUT /api/pages/{pageId}/blocks/{id}` (debounced)

#### Edit Heading Block
- [ ] Add a Heading block
- [ ] Change heading text
- [ ] Change level from H2 to H1
- [ ] Change alignment to "center"
- [ ] See changes in canvas

**Expected:** Heading properties work

#### Edit Image Block
- [ ] Add an Image block
- [ ] Enter image URL in properties
- [ ] Enter alt text
- [ ] Enter caption
- [ ] Change alignment
- [ ] See image displayed in canvas

**Expected:** Image block renders correctly

#### Edit Button Block
- [ ] Add a Button block
- [ ] Change button text
- [ ] Set link URL
- [ ] Check "Open in new tab"
- [ ] Change style (Primary, Secondary, Outline, Ghost)
- [ ] Change size (Small, Medium, Large)
- [ ] See button styled correctly

**Expected:** Button properties work

#### Edit Video Block
- [ ] Add a Video block
- [ ] Paste YouTube URL
- [ ] Video embed appears
- [ ] Change to Vimeo URL
- [ ] Vimeo embed appears
- [ ] Toggle autoplay
- [ ] Change aspect ratio

**Expected:** Video embeds work for YouTube and Vimeo

#### Edit HTML Block
- [ ] Add an HTML block
- [ ] Enter custom HTML in properties
- [ ] With sanitization enabled: warning shown
- [ ] Disable sanitization
- [ ] HTML renders in canvas
- [ ] Re-enable sanitization

**Expected:** HTML block with sanitization toggle

#### Edit Spacer Block
- [ ] Add a Spacer block
- [ ] Change height to "5rem"
- [ ] See visual indicator on hover
- [ ] Height changes reflected

**Expected:** Spacer creates vertical space

#### Edit Divider Block
- [ ] Add a Divider block
- [ ] Change style (Solid, Dashed, Dotted)
- [ ] Change color using color picker
- [ ] Change thickness
- [ ] Change spacing
- [ ] See divider styled correctly

**Expected:** Divider properties work

#### Edit Gallery Block
- [ ] Add a Gallery block
- [ ] Change number of columns (2, 3, 4, 5)
- [ ] Change gap size
- [ ] Change aspect ratio
- [ ] See "No images" empty state

**Expected:** Gallery properties update grid layout

**Note:** Image upload via Media Manager not yet implemented

#### Edit Container Block
- [ ] Add a Container block
- [ ] Change max width
- [ ] Change padding
- [ ] Change background color
- [ ] See "Nested blocks coming soon" message

**Expected:** Container properties work (nested blocks not yet implemented)

#### Edit Columns Block
- [ ] Add a Columns block
- [ ] Change number of columns (2, 3, 4)
- [ ] Change gap
- [ ] Change vertical alignment
- [ ] See columns layout update

**Expected:** Columns properties work (nested blocks not yet implemented)

#### Reorder Blocks with Drag-and-Drop
- [ ] Add multiple blocks to canvas
- [ ] Hover over a block
- [ ] Block toolbar appears with drag handle
- [ ] Drag block using drag handle
- [ ] Drop in new position
- [ ] Blocks reorder correctly

**Expected:** Drag-and-drop reordering works

**API Call:** `POST /api/blocks/reorder`

#### Block Visibility Toggle
- [ ] Select a block
- [ ] Click eye icon in toolbar
- [ ] Block becomes semi-transparent
- [ ] Icon changes to "eye-off"
- [ ] Click again
- [ ] Block becomes fully visible

**Expected:** Visibility toggle works

**API Call:** `PUT /api/pages/{pageId}/blocks/{id}`

#### Duplicate Block
- [ ] Select a block with content
- [ ] Click duplicate icon in toolbar
- [ ] Duplicate appears below original
- [ ] Duplicate has same content
- [ ] Duplicate has "(Copy)" suffix

**Expected:** Block duplicated with content

**API Call:** `POST /api/pages/{pageId}/blocks` (create with copied data)

#### Delete Block
- [ ] Click delete icon on a block
- [ ] Confirmation dialog appears
- [ ] Confirm deletion
- [ ] Block removed from canvas
- [ ] Properties panel closes if it was selected

**Expected:** Block deleted with confirmation

**API Call:** `DELETE /api/pages/{pageId}/blocks/{id}`

#### Deselect Block
- [ ] Select a block (properties panel opens)
- [ ] Click X button in properties panel header
- [ ] Properties panel closes
- [ ] Block deselected (no blue border)

**Expected:** Block can be deselected

#### Responsive Preview Modes
- [ ] Click "Desktop" button in header
- [ ] Canvas is wide (max-w-7xl)
- [ ] Click "Tablet" button
- [ ] Canvas shrinks to tablet size (max-w-3xl)
- [ ] Click "Mobile" button
- [ ] Canvas shrinks to mobile size (max-w-md)
- [ ] Switch back to Desktop

**Expected:** Preview modes change canvas width

#### Save Functionality
- [ ] Make changes to blocks
- [ ] "Save" button becomes enabled
- [ ] Click "Save"
- [ ] Button shows "Saving..."
- [ ] After save, button disabled again
- [ ] Unsaved changes flag cleared

**Expected:** Save button tracks changes

**Note:** Currently auto-saves via API, Save button just clears unsaved flag

#### Unsaved Changes Warning
- [ ] Make changes to a block
- [ ] Click browser back button
- [ ] Confirmation dialog appears
- [ ] Cancel
- [ ] Stay on page
- [ ] Click back again
- [ ] Confirm
- [ ] Navigate away

**Expected:** Warning before leaving with unsaved changes

#### Empty State
- [ ] Open Page Builder on a page with no blocks
- [ ] Empty state message shown
- [ ] "Click on blocks from the left sidebar" instruction
- [ ] SVG icon displayed

**Expected:** Proper empty state

#### Loading State
- [ ] Open Page Builder
- [ ] While blocks loading, see "Loading blocks..."
- [ ] After load, blocks displayed

**Expected:** Loading state shown during fetch

**API Call:** `GET /api/pages/{pageId}/blocks`

---

### 6. Dashboard Tests

#### View Dashboard
- [ ] Navigate to `/`
- [ ] Shows statistics cards (Total Sites, Published Sites, Total Pages)
- [ ] Shows "Quick Actions" section
- [ ] All values display correctly (initially 0)

**Expected:** Dashboard displays with stats

#### Quick Actions
- [ ] Click "Create New Site" in Quick Actions
- [ ] Should navigate to `/sites`

**Expected:** Navigation works

---

### 6. UI/UX Tests

#### Responsive Design
- [ ] Test on mobile viewport (375px)
- [ ] Test on tablet viewport (768px)
- [ ] Test on desktop viewport (1024px+)
- [ ] Grid should be 1 column on mobile, 2 on tablet, 3 on desktop

**Expected:** Responsive layout works

#### Loading States
- [ ] Refresh `/sites` page
- [ ] Should show "Loading..." message briefly
- [ ] Then show sites grid

**Expected:** Loading state visible during fetch

#### Form Validation
- [ ] Try creating site without name
- [ ] Should not submit
- [ ] Browser validation error appears

**Expected:** Required field validation works

#### Error Handling
- [ ] Stop backend server
- [ ] Try creating a site
- [ ] Error message should appear

**Expected:** Error displayed to user

#### Modal Interactions
- [ ] Open create modal
- [ ] Click backdrop (outside modal)
- [ ] Modal should close
- [ ] Click "Cancel" button
- [ ] Modal should close
- [ ] Press Escape key (if implemented)

**Expected:** Modal closes on cancel/backdrop click

---

### 7. State Management Tests

#### Pinia Stores
- [ ] Open Vue DevTools
- [ ] Navigate to Pinia tab
- [ ] Check `sites` store
- [ ] Should show:
  - `sites` array
  - `currentSite` object
  - `loading` boolean
  - `error` string
- [ ] Check `pages` store
- [ ] Should show:
  - `pages` array
  - `currentPage` object
  - `loading` boolean
  - `error` string

**Expected:** Store state visible in DevTools

#### Sites Store Actions
- [ ] Create a site
- [ ] Check Pinia DevTools
- [ ] `sites` array should update
- [ ] Edit a site
- [ ] Specific site object should update
- [ ] Delete a site
- [ ] Should be removed from array

**Expected:** Store updates reactively

#### Pages Store Actions
- [ ] Create a page
- [ ] Check Pinia DevTools
- [ ] `pages` array should update
- [ ] Edit a page
- [ ] Specific page object should update
- [ ] Delete a page
- [ ] Should be removed from array
- [ ] Set a page as home
- [ ] Page `is_home` property should update
- [ ] Other pages should have `is_home` set to false

**Expected:** Store updates reactively

---

### 8. API Integration Tests

#### Check Network Calls
- [ ] Open Browser DevTools Network tab
- [ ] Perform CRUD operations on Sites
- [ ] Verify correct API calls:
  - GET /api/sites (on sites page load)
  - POST /api/sites (create site)
  - PUT /api/sites/{id} (update site)
  - DELETE /api/sites/{id} (delete site)
  - POST /api/sites/{id}/publish
  - POST /api/sites/{id}/unpublish
  - POST /api/sites/{id}/duplicate
- [ ] Perform CRUD operations on Pages
- [ ] Verify correct API calls:
  - GET /api/sites/{siteId}/pages (on site detail load)
  - POST /api/sites/{siteId}/pages (create page)
  - PUT /api/pages/{id} (update page)
  - DELETE /api/pages/{id} (delete page)
  - POST /api/pages/{id}/publish
  - POST /api/pages/{id}/unpublish
  - POST /api/pages/{id}/duplicate
  - POST /api/pages/{id}/set-home
- [ ] Perform operations on Blocks in Page Builder
- [ ] Verify correct API calls:
  - GET /api/pages/{pageId}/blocks (load blocks)
  - POST /api/pages/{pageId}/blocks (create block)
  - PUT /api/pages/{pageId}/blocks/{id} (update block)
  - DELETE /api/pages/{pageId}/blocks/{id} (delete block)
  - POST /api/blocks/reorder (reorder blocks)

**Expected:** All API calls return 200/201/204 status

#### Authentication Headers
- [ ] Check network request headers
- [ ] Should include: `Authorization: Bearer {token}`
- [ ] Token should match localStorage

**Expected:** Auth token sent with requests

#### Response Handling
- [ ] Check API responses
- [ ] Should return proper JSON
- [ ] Site objects should have: id, name, slug, description, theme, etc.

**Expected:** Proper response format

---

## 🐛 Common Issues & Solutions

### Issue: Sites don't load
**Solution:** Check if migrations ran, check browser console for errors

### Issue: "Token not found" error
**Solution:** Login again, token may have expired or been cleared

### Issue: Theme dropdown empty
**Solution:** Run `php artisan db:seed --class=ThemeSeeder`

### Issue: Modal doesn't close
**Solution:** Check browser console, may be JavaScript error

### Issue: Changes don't reflect
**Solution:** Check Network tab, ensure API calls succeed

---

## 📊 Expected Results Summary

After completing all tests:

✅ User can register and login
✅ Protected routes work correctly
✅ Can create sites with themes
✅ Can edit existing sites
✅ Can delete sites with confirmation
✅ Can publish/unpublish sites
✅ Can duplicate sites
✅ Can create pages within sites
✅ Can edit existing pages
✅ Can delete pages with confirmation
✅ Can publish/unpublish pages
✅ Can duplicate pages
✅ Can set page as home (only one home page per site)
✅ Page Builder opens and displays correctly
✅ Can add all 11 block types from palette
✅ Can edit block properties in real-time
✅ Can reorder blocks with drag-and-drop
✅ Can duplicate blocks
✅ Can toggle block visibility
✅ Can delete blocks with confirmation
✅ Responsive preview modes (desktop/tablet/mobile) work
✅ Save functionality tracks unsaved changes
✅ Warning before leaving with unsaved changes
✅ All block types render correctly:
  - Text Block with formatting options
  - Heading Block (H1-H6)
  - Image Block with caption
  - Gallery Block with grid layout
  - Video Block (YouTube/Vimeo embeds)
  - HTML Block with sanitization
  - Button Block with variants
  - Divider Block with styles
  - Spacer Block with adjustable height
  - Container Block (nested blocks coming soon)
  - Columns Block (nested blocks coming soon)
✅ UI updates reactively
✅ Loading states work
✅ Error handling works
✅ Responsive design works
✅ All API calls succeed
✅ Pinia stores (sites, pages & blocks) update correctly

---

## 🚀 Next Features to Test (When Implemented)

- [ ] Media Manager (upload and manage images/files)
- [ ] Public page rendering (view published pages)
- [ ] Theme customization interface
- [ ] Site preview and live editing
- [ ] Nested blocks for Container and Columns
- [ ] Block templates and presets
- [ ] Multi-language support
- [ ] SEO optimization tools
- [ ] Analytics integration

---

## 📝 Notes for Developers

- Use Vue DevTools for debugging component state
- Check browser console for JavaScript errors
- Monitor Network tab for API issues
- Test both success and error scenarios
- Test with different user roles (when implemented)
- Test concurrent operations (multiple users)
- Test with large datasets (100+ sites)

---

## 🔄 Continuous Testing

After each new feature:
1. Run through relevant test scenarios
2. Check for regressions in existing features
3. Update this document with new test cases
4. Document any bugs found
5. Verify fixes with re-testing

---

## 🤖 Automated Testing & CI/CD

### Running PHPUnit Tests

Simplio CMS includes comprehensive automated tests for backend functionality.

#### Setup Test Environment

```bash
# Copy test environment file
cp .env.testing.example .env.testing

# Generate application key for testing
php artisan key:generate --env=testing

# Run migrations for test database
php artisan migrate --env=testing
```

#### Running Tests

```bash
# Run all tests
php artisan test

# Run tests in parallel (faster)
php artisan test --parallel

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/UserManagementTest.php

# Run specific test method
php artisan test --filter test_admin_can_create_user

# Run with coverage report
php artisan test --coverage
```

### Test Coverage

#### Feature Tests (tests/Feature/)

1. **AuthTest.php** - Authentication endpoints
   - User registration
   - User login/logout
   - Token validation
   - Protected routes access
   - Profile retrieval

2. **UserManagementTest.php** - User CRUD operations (Admin only)
   - List users with pagination
   - Create users with roles
   - Update user information
   - Delete users (with self-deletion protection)
   - Search and filter users
   - Role management
   - Permission checks

3. **SiteManagementTest.php** - Site CRUD operations
   - List sites
   - Create/update/delete sites
   - Publish/unpublish sites
   - Slug uniqueness validation
   - Authorization checks

#### Unit Tests (tests/Unit/)

1. **UserModelTest.php** - User model methods
   - isAdmin() role check
   - isEditor() role check
   - canManageUsers() permission
   - canManageSites() permission
   - Role constants validation

### CI/CD Pipeline

The project uses GitHub Actions for automated testing and deployment.

#### Workflow Triggers

- Push to `main`, `develop`, or `claude/**` branches
- Pull requests to `main` or `develop`

#### Pipeline Stages

1. **Backend Tests** (PHP 8.4 + MySQL 8.0)
   - Checkout code
   - Install PHP dependencies
   - Run database migrations
   - Execute PHPUnit tests in parallel

2. **Frontend Build** (Node.js 20)
   - Checkout code
   - Install npm dependencies
   - Run linter (if configured)
   - Build production assets
   - Upload build artifacts

3. **Code Quality Checks** (Optional)
   - PHPStan static analysis
   - PHP CS Fixer code style

4. **Security Checks** (Optional)
   - Composer audit for vulnerabilities
   - npm audit for dependencies

5. **Deploy** (Production only)
   - Triggers on push to `main`
   - Requires all tests to pass
   - Ready for deployment configuration

#### View Build Status

Check the Actions tab in GitHub to see:
- ✅ Test results
- ⏱️ Build time
- 📊 Test coverage
- 🔒 Security scan results

### Writing New Tests

#### Feature Test Example

```php
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_works(): void
    {
        // Arrange: Create test data
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act: Perform action
        $response = $this->postJson('/api/endpoint', ['data' => 'value']);

        // Assert: Verify results
        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('table', ['column' => 'value']);
    }
}
```

#### Unit Test Example

```php
class MyUnitTest extends TestCase
{
    public function test_method_returns_expected_value(): void
    {
        $model = new Model(['attribute' => 'value']);

        $result = $model->someMethod();

        $this->assertEquals('expected', $result);
    }
}
```

### Best Practices

1. **Use RefreshDatabase** - Ensures clean state for each test
2. **Test Authentication** - Use `Sanctum::actingAs()` for authenticated requests
3. **Test Permissions** - Verify admin-only endpoints reject non-admins
4. **Test Validation** - Ensure invalid data returns 422 with errors
5. **Test Edge Cases** - Check boundary conditions and error states
6. **Keep Tests Fast** - Use factories, avoid unnecessary DB calls
7. **Descriptive Names** - Test names should describe what they test

### Continuous Integration Benefits

- ✅ Catch bugs before they reach production
- ✅ Prevent regressions when adding features
- ✅ Ensure code quality standards
- ✅ Verify compatibility across environments
- ✅ Automate deployment process
- ✅ Build confidence in releases
