<?php

declare(strict_types=1);

namespace BuiltNorth\WPConfig;

use InvalidArgumentException;
use RuntimeException;


/**
 * Get an environment variable
 */
function env(string $key, mixed $default = null): mixed
{
	$value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

	if ($value === false) {
		return $default;
	}

	switch (strtolower($value)) {
		case 'true':
		case '(true)':
			return true;
		case 'false':
		case '(false)':
			return false;
		case 'null':
		case '(null)':
			return null;
	}

	return $value;
}

/**
 * Configuration Class
 *
 * Handles WordPress configuration management.
 * Based on Roots/Bedrock implementation.
 *
 * @package BuiltNorth\WPConfig
 */
class Config
{
	/** @var array<string, mixed> Configuration storage */
	protected static array $configMap = [];

	/**
	 * Define a configuration value if not already defined.
	 */
	public static function define(string $key, mixed $value): void
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		self::defined($key) or self::$configMap[$key] = $value;
	}

	/**
	 * Get a configuration value.
	 */
	public static function get(string $key, mixed $default = null): mixed
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		if (!array_key_exists($key, self::$configMap)) {
			if ($default !== null) {
				return $default;
			}
			throw new RuntimeException("'$key' has not been defined.");
		}

		return self::$configMap[$key];
	}

	/**
	 * Apply all configurations as WordPress constants
	 */
	public static function apply(): void
	{
		// First check for any conflicts
		foreach (self::$configMap as $key => $value) {
			try {
				self::defined($key);
			} catch (RuntimeException $e) {
				if (constant($key) !== $value) {
					throw $e;
				}
			}
		}

		// If all is well, apply the configMap
		foreach (self::$configMap as $key => $value) {
			defined($key) or define($key, $value);
		}
	}

	/**
	 * Check required environment variables
	 */
	public static function requireVars(array $vars): void
	{
		$missing = [];
		foreach ($vars as $var) {
			if (!env($var)) {
				$missing[] = $var;
			}
		}

		if (!empty($missing)) {
			throw new RuntimeException(
				sprintf('Required environment variables are missing: %s', implode(', ', $missing))
			);
		}
	}

	/**
	 * Remove a configuration value.
	 */
	public static function remove(string $key): void
	{
		unset(self::$configMap[$key]);
	}

	/**
	 * Check if a constant is already defined
	 */
	protected static function defined(string $key): bool
	{
		if (defined($key)) {
			throw new RuntimeException(
				"Aborted trying to redefine constant '$key'. `define('$key', ...)` has already occurred elsewhere."
			);
		}
		return false;
	}
}
