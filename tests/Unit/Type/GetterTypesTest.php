<?php
/**
 * GetterTypesTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Type;

use PHPStan\Testing\TypeInferenceTestCase;
/** Verifies the PressGang contract against source fixtures. */
class GetterTypesTest extends TypeInferenceTestCase {
	/** {@inheritDoc} */
	public static function getAdditionalConfigFiles(): array {
		return array( __DIR__ . '/../../phpstan.neon' );
	}

	/**
	 * Collects type assertions from the fixture.
	 *
	 * @return array<string, mixed>
	 */
	public function data_types(): array {
		return array_merge(
			self::gatherAssertTypes( __DIR__ . '/../../fixtures/types.php' ),
			self::gatherAssertTypes( __DIR__ . '/../../fixtures/blocks.php' )
		);
	}

	/**
	 * Test types.
	 *
	 * @param string $assertType Test input.
	 * @param string $file Test input.
	 * @param mixed  ...$args Test input.
	 * @return void
	 * @dataProvider data_types
	 */
	public function test_types( string $assertType, string $file, mixed ...$args ): void {
		$this->assertFileAsserts( $assertType, $file, ...$args );
	}
}
