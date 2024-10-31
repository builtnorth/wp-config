# Built North WP Config

A WordPress configuration management library based off of [roots/wp-config](https://github.com/roots/wp-config).

## Installation

```bash
composer require builtnorth/wp-config
```

## Usage

### Basic Example

```php
use BuiltNorth\WPConfig\Config;

// Set configurations
Config::define('WP_DEBUG', true);
Config::define('WP_HOME', env('WP_HOME'));
Config::define('DB_NAME', env('DB_NAME'));

// Apply them
Config::apply();
```

### Get Configuration Values

```php
$debug = Config::get('WP_DEBUG', false);
$home = Config::get('WP_HOME');
```

## Requirements

-   PHP 7.4+
-   WordPress
-   Composer

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied. In no event shall the authors or copyright holders be liable for any claim, damages, or other liability, whether in an action of contract, tort, or otherwise, arising from, out of, or in connection with the software or the use or other dealings in the software.

Use at your own risk.
