<?php
/**
 * DynamicGetterPropertiesExtension for PressGang static analysis.
 */

namespace PressGang\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\ShouldNotHappenException;

/**
 * Publishes the read side of get_field() without inventing writable model fields.
 */
final class DynamicGetterPropertiesExtension implements PropertiesClassReflectionExtension {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param DynamicGetterResolver $resolver Shared contract resolver.
	 */
	public function __construct( private DynamicGetterResolver $resolver ) {}

	/** {@inheritDoc} */
	public function hasProperty( ClassReflection $classReflection, string $propertyName ): bool {
		return ! $classReflection->hasNativeProperty( $propertyName )
			&& $this->resolver->dispatches( $classReflection, '__get' )
			&& $this->resolver->return_type( $classReflection, $propertyName ) !== null;
	}

	/**
	 * Reflects the getter as a readable property after hasProperty() succeeds.
	 *
	 * @param ClassReflection $classReflection Class being analysed.
	 * @param string          $propertyName Requested property.
	 * @return PropertyReflection
	 * @throws ShouldNotHappenException If the caller skipped the existence check.
	 */
	public function getProperty( ClassReflection $classReflection, string $propertyName ): PropertyReflection {
		$type = $this->resolver->return_type( $classReflection, $propertyName );
		if ( null === $type ) {
			throw new ShouldNotHappenException();
		}
		return new GetterPropertyReflection( $classReflection, $type );
	}
}
