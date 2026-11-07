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

- PHP 8.1+
- Composer

## License

GPL-3.0-or-later — see `LICENSE`.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or
implied, including the warranties of merchantability and fitness for a
particular purpose. To the extent permitted by applicable law, the authors and
copyright holders, together with their parent, subsidiary, and affiliated
companies and contributors, are not liable for any direct, indirect,
incidental, or consequential damages arising from its use or inability to use
it, including loss of data, revenue, or site availability.

The software does not guarantee that your site is secure, ranks in search
results, or complies with privacy or other laws; that remains the site owner's
responsibility. AI-generated content can be inaccurate and should be reviewed
before publishing. Third-party services the software connects to are governed
by their own terms.

Keep a current backup and test changes on a staging site before applying them
to a live site. See sections 15 and 16 of the GNU General Public License,
version 3, for the full terms.
