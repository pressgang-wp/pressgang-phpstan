<?php
/**
 * ManifestMethodsExtension for PressGang static analysis.
 */

namespace PressGang\PHPStan\Reflection;

use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Rules\Methods\AlwaysUsedMethodExtension;

/**
 * Makes manifest references visible to PHPStan's unused-method check.
 */
final class ManifestMethodsExtension implements AlwaysUsedMethodExtension {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param ContextManifestResolver $resolver Shared contract resolver.
	 */
	public function __construct( private ContextManifestResolver $resolver ) {}

	/** {@inheritDoc} */
	public function isAlwaysUsed( ExtendedMethodReflection $methodReflection ): bool {
		$manifest = $this->resolver->resolve( $methodReflection->getDeclaringClass() );
		return null !== $manifest && in_array( strtolower( $methodReflection->getName() ), $this->resolver->methods( $manifest ), true );
	}
}
