<?php

declare(strict_types=1);

namespace BuiltNorth\WPConfig;

use InvalidArgumentException;
use RuntimeException;

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
	public static function get(string $key): mixed
	{
		if (!array_key_exists($key, self::$configMap)) {
			$class = self::class;
			throw new RuntimeException("'$key' has not been defined. Use `$class::define('$key', ...)`.");
		}

		return self::$configMap[$key];
	}

	/**
	 * Remove a configuration value.
	 */
	public static function remove(string $key): void
	{
		unset(self::$configMap[$key]);
	}

	/**
	 * Apply all configurations as WordPress constants
	 *
	 * We throw an exception if attempting to redefine a constant because a silent
	 * rejection of a configuration value is unacceptable. This method fails fast
	 * before undefined behavior can occur due to unexpected configurations.
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
