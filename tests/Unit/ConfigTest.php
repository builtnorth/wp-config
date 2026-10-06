<?php
/**
 * Tests for the Config class
 *
 * @package BuiltNorth\WPConfig\Tests\Unit
 */

namespace BuiltNorth\WPConfig\Tests\Unit;

use BuiltNorth\WPConfig\Config;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;
use ReflectionClass;

/**
 * Config test case
 */
class ConfigTest extends TestCase {

	/**
	 * Set up before each test
	 */
	protected function setUp(): void {
		parent::setUp();
		// Clear the static config map before each test
		$this->clearConfigMap();
	}

	/**
	 * Tear down after each test
	 */
	protected function tearDown(): void {
		// Clear the static config map after each test
		$this->clearConfigMap();
		parent::tearDown();
	}

	/**
	 * Clear the static config map using reflection
	 */
	private function clearConfigMap(): void {
		$reflection = new ReflectionClass( Config::class );
		$property = $reflection->getProperty( 'configMap' );
		$property->setAccessible( true );
		$property->setValue( null, [] );
	}

	/**
	 * Test define method stores values
	 */
	public function test_define_stores_values() {
		Config::define( 'TEST_KEY', 'test_value' );
		$this->assertEquals( 'test_value', Config::get( 'TEST_KEY' ) );
	}

	/**
	 * Test define with different types
	 */
	public function test_define_with_different_types() {
		// String
		Config::define( 'STRING_KEY', 'string_value' );
		$this->assertEquals( 'string_value', Config::get( 'STRING_KEY' ) );

		// Integer
		Config::define( 'INT_KEY', 42 );
		$this->assertEquals( 42, Config::get( 'INT_KEY' ) );

		// Boolean
		Config::define( 'BOOL_KEY', true );
		$this->assertTrue( Config::get( 'BOOL_KEY' ) );

		// Array
		Config::define( 'ARRAY_KEY', [ 'foo', 'bar' ] );
		$this->assertEquals( [ 'foo', 'bar' ], Config::get( 'ARRAY_KEY' ) );

		// Null
		Config::define( 'NULL_KEY', null );
		$this->assertNull( Config::get( 'NULL_KEY' ) );
	}

	/**
	 * Test define throws exception for empty key
	 */
	public function test_define_throws_exception_for_empty_key() {
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Configuration key cannot be empty' );
		Config::define( '', 'value' );
	}

	/**
	 * Test define overwrites prior configMap entries when no PHP constant exists yet
	 * (Bedrock/Roots 1.x behavior — lets environment files override defaults).
	 */
	public function test_define_overwrites_existing_config_map_values() {
		Config::define( 'TEST_KEY', 'original_value' );
		Config::define( 'TEST_KEY', 'new_value' );
		$this->assertEquals( 'new_value', Config::get( 'TEST_KEY' ) );
	}

	/**
	 * Test get throws exception for undefined key
	 */
	public function test_get_throws_exception_for_undefined_key() {
		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "'UNDEFINED_KEY' has not been defined" );
		Config::get( 'UNDEFINED_KEY' );
	}

	/**
	 * Test remove deletes configuration
	 */
	public function test_remove_deletes_configuration() {
		Config::define( 'TEST_KEY', 'test_value' );
		$this->assertEquals( 'test_value', Config::get( 'TEST_KEY' ) );
		
		Config::remove( 'TEST_KEY' );
		
		$this->expectException( RuntimeException::class );
		Config::get( 'TEST_KEY' );
	}

	/**
	 * Test remove non-existent key does not throw
	 */
	public function test_remove_non_existent_key() {
		$this->expectNotToPerformAssertions();
		Config::remove( 'NON_EXISTENT_KEY' );
	}

	/**
	 * Test apply method with constants
	 */
	public function test_apply_defines_constants() {
		Config::define( 'TEST_CONSTANT', 'test_value' );
		Config::define( 'ANOTHER_CONSTANT', 42 );
		
		Config::apply();
		
		$this->assertTrue( defined( 'TEST_CONSTANT' ) );
		$this->assertEquals( 'test_value', TEST_CONSTANT );
		$this->assertTrue( defined( 'ANOTHER_CONSTANT' ) );
		$this->assertEquals( 42, ANOTHER_CONSTANT );
	}

	/**
	 * Test apply succeeds when a PHP constant already exists with the same value
	 */
	public function test_apply_allows_existing_constant_with_same_value() {
		Config::define( 'APPLY_SAME_VALUE_CONST', 'same_value' );
		define( 'APPLY_SAME_VALUE_CONST', 'same_value' );

		Config::apply();

		$this->assertTrue( defined( 'APPLY_SAME_VALUE_CONST' ) );
		$this->assertEquals( 'same_value', APPLY_SAME_VALUE_CONST );
	}

	/**
	 * Test apply throws when a PHP constant already exists with a different value
	 */
	public function test_apply_throws_for_existing_constant_with_different_value() {
		Config::define( 'APPLY_DIFF_VALUE_CONST', 'map_value' );
		define( 'APPLY_DIFF_VALUE_CONST', 'other_value' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Aborted trying to redefine constant 'APPLY_DIFF_VALUE_CONST'" );

		Config::apply();
	}

	/**
	 * Test define throws exception for already defined PHP constants
	 */
	public function test_define_throws_for_already_defined_constants() {
		define( 'PREDEFINED_CONSTANT_TEST', 'original_value' );
		
		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( "Aborted trying to redefine constant 'PREDEFINED_CONSTANT_TEST'" );
		
		Config::define( 'PREDEFINED_CONSTANT_TEST', 'new_value' );
	}

	/**
	 * Test apply with array values
	 */
	public function test_apply_with_array_values() {
		$array_value = [ 'foo' => 'bar', 'baz' => 123 ];
		Config::define( 'ARRAY_CONSTANT', $array_value );
		
		Config::apply();
		
		$this->assertTrue( defined( 'ARRAY_CONSTANT' ) );
		// Arrays are stored as arrays, not JSON strings
		$this->assertIsArray( ARRAY_CONSTANT );
		$this->assertEquals( $array_value, ARRAY_CONSTANT );
	}

	/**
	 * Test multiple configurations
	 */
	public function test_multiple_configurations() {
		$configs = [
			'TEST_DB_NAME' => 'wordpress',
			'TEST_DB_USER' => 'root',
			'TEST_DB_PASSWORD' => 'password',
			'TEST_DB_HOST' => 'localhost',
			'TEST_WP_OPTION' => true,
			'TEST_WP_ANOTHER' => true,
			'TEST_WP_DISPLAY' => false,
		];

		foreach ( $configs as $key => $value ) {
			Config::define( $key, $value );
		}

		foreach ( $configs as $key => $value ) {
			$this->assertEquals( $value, Config::get( $key ) );
		}
	}
}
