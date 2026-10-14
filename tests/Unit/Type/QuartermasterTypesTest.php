<?php
/**
 * Quartermaster terminal inference boundaries.
 */

namespace PressGang\PHPStan\Tests\Unit\Type;

use PHPStan\Testing\TypeInferenceTestCase;

/** Verifies direct ID chains and conservative fallbacks. */
class QuartermasterTypesTest extends TypeInferenceTestCase {
	/** {@inheritDoc} */
	public static function getAdditionalConfigFiles(): array {
		return array( __DIR__ . '/../../phpstan.neon' );
	}

	/**
	 * Collects the fixture assertions.
	 *
	 * @return array<string, mixed>
	 */
	public function data_types(): array {
		return self::gatherAssertTypes( __DIR__ . '/../../fixtures/quartermaster.php' );
	}

	/**
	 * Checks inferred types.
	 *
	 * @param string $assertType Assertion kind.
	 * @param string $file Fixture path.
	 * @param mixed  ...$args Assertion arguments.
	 * @dataProvider data_types
	 */
	public function test_types( string $assertType, string $file, mixed ...$args ): void {
		$this->assertFileAsserts( $assertType, $file, ...$args );
	}
}
