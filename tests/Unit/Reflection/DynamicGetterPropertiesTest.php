<?php
/**
 * DynamicGetterPropertiesTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Reflection;

use PHPStan\Testing\PHPStanTestCase;
use PressGang\PHPStan\Reflection\DynamicGetterPropertiesExtension;
/** Verifies the PressGang contract against source fixtures. */
class DynamicGetterPropertiesTest extends PHPStanTestCase {
	/** {@inheritDoc} */
	public static function getAdditionalConfigFiles(): array {
		return array( __DIR__ . '/../../phpstan.neon' );
	}

	/**
	 * Only actual getters on active trait users are readable.
	 *
	 * @return void
	 * @test
	 */
	public function only_actual_getters_on_active_trait_users_are_readable(): void {
		$provider  = self::createReflectionProvider();
		$extension = self::getContainer()->getByType( DynamicGetterPropertiesExtension::class );
		$model     = $provider->getClass( 'Fixtures\\Types\\Model' );
		self::assertTrue( $extension->hasProperty( $model, 'display_label' ) );
		self::assertFalse( $extension->hasProperty( $model, 'missing' ) );
		self::assertFalse( $extension->hasProperty( $model, 'real' ) );
		self::assertFalse( $extension->hasProperty( $provider->getClass( 'Fixtures\\Types\\Ordinary' ), 'display_label' ) );
		self::assertFalse( $extension->hasProperty( $provider->getClass( 'Fixtures\\Types\\Overridden' ), 'display_label' ) );
		self::assertFalse( $extension->hasProperty( $provider->getClass( 'Fixtures\\Types\\CustomDispatch' ), 'display_label' ) );
		$property = $extension->getProperty( $model, 'display_label' );
		self::assertTrue( $property->isReadable() );
		self::assertFalse( $property->isWritable() );
	}
}
