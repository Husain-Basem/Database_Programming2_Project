# Database_Programming2_Project

Database Programming 2 Project

# Developement

For database settings, put the following in the file
`/home/username/public_html/connectionSettings.php`:

```php
<?php

define("DB_HOSTNAME", "localhost");
define("DB_DATABASE", "db20xxxxxxx");
define("DB_USERNAME", "u20xxxxxxx");
define("DB_PASSWORD", "u20xxxxxxx");
```

## Built-in Users

The following users are added by default:

| Username | Password | Role   |
| -------- | -------- | ------ |
| bob      | 123      | Viewer |
| tom      | 123      | Author |
| admin1   | 123      | Admin  |

## Progress

- [ ] Users
  - [x] Login, Logout & Register
  - [x] Hashed passwords
  - [x] Data validation
  - [ ] User profile
- [ ] Home page
  - [x] Navbar
  - [ ] Search bar
  - [ ] News list
- [ ] Search page
- [ ] Comments and rating
- [ ] Author panel
  - [ ] Add/Edit articles
  - [ ] File upload
- [ ] Admin panel
  - [ ] Manage users
  - [ ] Manage all news articles
  - [ ] Manage Comments
- [ ] Display news article page
- [ ] Admin report dashboard
