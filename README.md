# Built North WP Config

A WordPress configuration management library based off of [roots/wp-config](https://github.com/roots/wp-config) 1.x.

This package only provides the `Config` class (`define` / `get` / `remove` / `apply`). Environment loading (`vlucas/phpdotenv`, `env()` helpers, etc.) belongs in your site bootstrap — the same split Roots used in 1.x.

## Installation

```bash
composer require builtnorth/wp-config
```

## Usage

### Basic Example

```php
use BuiltNorth\WPConfig\Config;

// Set configurations (values usually come from your own env loader)
Config::define('WP_DEBUG', true);
Config::define('WP_HOME', 'https://example.test');
Config::define('DB_NAME', 'wordpress');

// Apply them as PHP constants
Config::apply();
```

### Get Configuration Values

Values are read from the in-memory map (before or after `apply()`). There is no default argument — missing keys throw.

```php
$debug = Config::get('WP_DEBUG');
$home = Config::get('WP_HOME');
```

## Requirements

- PHP 8.0+
- Composer

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied. In no event shall the authors or copyright holders be liable for any claim, damages, or other liability, whether in an action of contract, tort, or otherwise, arising from, out of, or in connection with the software or the use or other dealings in the software.

Use at your own risk.
