<?php

namespace BuiltNorth\WPConfig;

use InvalidArgumentException;
use RuntimeException;

/**
 * Configuration Class
 *
 * Handles WordPress configuration management through a singleton pattern.
 * Inspired by Roots/Bedrock but with our own implementation.
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

	/** @var array Required environment variables */
	private array $required = ['WP_HOME'];

	/**
	 * Get singleton instance of Config.
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Static interface for define
	 */
	public static function define(string $key, mixed $value): void
	{
		self::getInstance()->set($key, $value);
	}

	/**
	 * Static interface for get
	 */
	public static function get(string $key, mixed $default = null): mixed
	{
		return self::getInstance()->getValue($key, $default);
	}

	/**
	 * Apply all configurations as WordPress constants
	 */
	public static function apply(): void
	{
		$instance = self::getInstance();
		foreach ($instance->all() as $key => $value) {
			$instance->defineConstant($key, $value);
		}
	}

	/**
	 * Check required environment variables
	 *
	 * @throws RuntimeException if required variables are missing
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
	 * Set a configuration value.
	 */
	private function set(string $key, mixed $value): void
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		$this->config[$key] = $value;
	}

	/**
	 * Get a configuration value.
	 */
	private function getValue(string $key, mixed $default = null): mixed
	{
		if (empty($key)) {
			throw new InvalidArgumentException('Configuration key cannot be empty');
		}

		return $this->config[$key] ?? $default;
	}

	/**
	 * Define a WordPress constant if it doesn't exist.
	 */
	private function defineConstant(string $key, mixed $value): void
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
	 * Get all configuration values.
	 */
	private function all(): array
	{
		return $this->config;
	}
}
