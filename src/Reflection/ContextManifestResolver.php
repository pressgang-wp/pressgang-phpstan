<?php
/**
 * ContextManifestResolver for PressGang static analysis.
 */

namespace PressGang\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;

/**
 * Reads source-reflected defaults, never loading or constructing a controller.
 */
final class ContextManifestResolver {
	public const CONTROLLER = 'PressGang\\Controllers\\AbstractController';

	/**
	 * Reads the effective manifest default, or returns null when it cannot be resolved.
	 *
	 * @param ClassReflection $class_reflection Class being analysed.
	 * @return array<int|string, string>|null
	 */
	public function resolve( ClassReflection $class_reflection ): ?array {
		if ( ! $class_reflection->isSubclassOf( self::CONTROLLER ) || ! $class_reflection->hasNativeProperty( 'context_getters' ) ) {
			return null;
		}
		try {
			$value = $class_reflection->getNativeReflection()->getProperty( 'context_getters' )->getDefaultValue();
		} catch ( \Throwable ) {
			return null;
		}
		if ( ! is_array( $value ) ) {
			return null;
		}
		foreach ( $value as $entry ) {
			if ( ! is_string( $entry ) ) {
				return null;
			}
		}
		return $value;
	}

	/**
	 * Normalises manifest entries to case-insensitive PHP method names.
	 *
	 * @param array<int|string, string> $manifest Effective context manifest.
	 * @return list<string>
	 */
	public function methods( array $manifest ): array {
		$methods = array();
		foreach ( $manifest as $key => $getter ) {
			$methods[] = strtolower( is_int( $key ) ? 'get_' . $getter : $getter );
		}
		return $methods;
	}
}
