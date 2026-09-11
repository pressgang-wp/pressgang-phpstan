<?php
/**
 * ManifestMethodsTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Reflection;

use PHPStan\Testing\PHPStanTestCase;
use PressGang\PHPStan\Reflection\ManifestMethodsExtension;
/** Verifies the PressGang contract against source fixtures. */
class ManifestMethodsTest extends PHPStanTestCase {
	/** {@inheritDoc} */
	public static function getAdditionalConfigFiles(): array {
		return array( __DIR__ . '/../../phpstan.neon' );
	}

	/**
	 * Only manifest methods are marked used.
	 *
	 * @return void
	 * @test
	 */
	public function only_manifest_methods_are_marked_used(): void {
		$class_reflection = self::createReflectionProvider()->getClass( 'Fixtures\\Manifests\\ValidController' );
		$extension        = self::getContainer()->getByType( ManifestMethodsExtension::class );
		self::assertTrue( $extension->isAlwaysUsed( $class_reflection->getNativeMethod( 'get_local' ) ) );
		self::assertTrue( $extension->isAlwaysUsed( $class_reflection->getNativeMethod( 'fetch_posts' ) ) );
		self::assertFalse( $extension->isAlwaysUsed( $class_reflection->getNativeMethod( 'get_helper' ) ) );
	}
}
