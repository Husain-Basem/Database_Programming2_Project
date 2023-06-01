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

- [x] Users
  - [x] Login, Logout & Register
  - [x] Hashed passwords
  - [x] Data validation
  - [x] User profile
- [ ] Home page
  - [x] Navbar
  - [ ] Search bar
  - [ ] News list
- [ ] Search page
- [x] Comments and rating
  - [x] Comments
  - [x] Ratings
- [x] Author panel
  - [x] Add/Edit articles
  - [x] Image upload
  - [x] Attachments
- [x] Admin panel
  - [x] Manage users
    - [x] Delete users
    - [x] Edit users
    - [x] Register authors
  - [x] Manage all news articles
    - [x] Approve articles
    - [x] Edit/remove articles
    - [x] delete Comments
  - [x] Manage own articles
  - [x] Admin report dashboard
- [x] Display news article page 
  - [x] Display article
  - [x] Comments
  - [x] Ratings
  - [x] Attachments
