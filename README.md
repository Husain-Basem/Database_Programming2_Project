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

Remember to

```console
$ cd public_html
$ chmod -R 777 Database_Programming2_Project
```

## Built-in Users

The following users are added by default:

| Username | Password | Role   |
| -------- | -------- | ------ |
| bob      | 123      | Viewer |
| tom      | 123      | Author |
| admin1   | 123      | Admin  |

Further registered users will have stricter password requirements. 