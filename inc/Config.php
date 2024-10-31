<?php

namespace BuiltNorth\WPConfig;

use InvalidArgumentException;
use RuntimeException;

/**
 * Configuration Class
 *
 * Handles WordPress configuration management through a singleton pattern.
 * Provides methods for setting, getting, and defining configuration values.
 *
 * @package BuiltNorth\WPConfig
 * @since 1.0.0
 */
class Config
{
	/** @var Config|null Singleton instance */
	private static ?Config $instance = null;

	/** @var array Configuration storage */
	private array $config = [];

	/**
	 * Get singleton instance of Config.
	 *
	 * @return self
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Set a configuration value.
	 *
	 * @param string $key Configuration key
	 * @param mixed $value Configuration value
	 * @throws InvalidArgumentException If key is empty
	 * @return void
	 */
	public function set(string $key, mixed $value): void
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		$this->config[$key] = $value;
	}

	/**
	 * Get a configuration value.
	 *
	 * @param string $key Configuration key
	 * @param mixed|null $default Default value if key doesn't exist
	 * @throws InvalidArgumentException If key is empty
	 * @return mixed
	 */
	public function get(string $key, mixed $default = null): mixed
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		return $this->config[$key] ?? $default;
	}

	/**
	 * Define a WordPress constant if it doesn't exist.
	 *
	 * @param string $key Constant name
	 * @param mixed $value Constant value
	 * @throws InvalidArgumentException If key is empty
	 * @throws RuntimeException If constant definition fails
	 * @return void
	 */
	public function define(string $key, mixed $value): void
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Constant name cannot be empty');
		}

		if (!defined($key)) {
			if (@define($key, $value) === false) {
				throw new RuntimeException("Failed to define constant: {$key}");
			}
		}
	}

	/**
	 * Check if a configuration key exists.
	 *
	 * @param string $key Configuration key
	 * @throws InvalidArgumentException If key is empty
	 * @return bool
	 */
	public function has(string $key): bool
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		return isset($this->config[$key]);
	}

	/**
	 * Get all configuration values.
	 *
	 * @return array
	 */
	public function all(): array
	{
		return $this->config;
	}
}
