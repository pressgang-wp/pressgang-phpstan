<?php
/**
 * DynamicGetterResolver for PressGang static analysis.
 */

namespace PressGang\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\Type;

/**
 * Resolves only native getters exposed by PressGang's active trait implementation.
 */
final class DynamicGetterResolver {
	public const TRAIT_NAME = 'PressGang\\Traits\\HandlesDynamicGetters';

	/**
	 * Checks inherited and nested trait use.
	 *
	 * @param ClassReflection $class_reflection Class being analysed.
	 * @return bool
	 */
	public function uses_trait( ClassReflection $class_reflection ): bool {
		return isset( $class_reflection->getTraits( true )[ self::TRAIT_NAME ] );
	}

	/**
	 * Leaves overridden dispatch to its declared behaviour.
	 *
	 * @param ClassReflection $class_reflection Class being analysed.
	 * @param string          $method Dispatch method name.
	 * @return bool
	 */
	public function dispatches( ClassReflection $class_reflection, string $method ): bool {
		if ( ! $this->uses_trait( $class_reflection ) || ! $class_reflection->hasNativeMethod( $method ) ) {
			return false;
		}
		$trait = $class_reflection->getTraits( true )[ self::TRAIT_NAME ];
		foreach ( array( $method, 'has_custom_getter', 'call_custom_getter' ) as $dispatch_method ) {
			if ( ! $class_reflection->hasNativeMethod( $dispatch_method ) ) {
				return false;
			}
			$native = $class_reflection->getNativeReflection()->getMethod( $dispatch_method );
			if ( $native->getFileName() !== $trait->getFileName()
				|| $native->getStartLine() !== $trait->getNativeReflection()->getMethod( $dispatch_method )->getStartLine() ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Reads the native getter return contract, including PHPDoc.
	 *
	 * @param ClassReflection $class_reflection Class being analysed.
	 * @param string          $field Requested field name.
	 * @return Type|null
	 */
	public function return_type( ClassReflection $class_reflection, string $field ): ?Type {
		$method = 'get_' . $field;
		if ( ! $this->uses_trait( $class_reflection ) || ! $class_reflection->hasNativeMethod( $method ) ) {
			return null;
		}
		$types = array();
		foreach ( $class_reflection->getNativeMethod( $method )->getVariants() as $variant ) {
			$types[] = $variant->getReturnType();
		}
		return TypeCombinator::union( ...$types );
	}
}
