<?php
/**
 * MetaReturnTypeExtension for PressGang static analysis.
 */

namespace PressGang\PHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PressGang\PHPStan\Reflection\DynamicGetterResolver;

/**
 * Getter dispatch precedes Timber/ACF processing, including transform_value options.
 */
final class MetaReturnTypeExtension implements DynamicMethodReturnTypeExtension {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param DynamicGetterResolver $resolver Shared contract resolver.
	 * @param class-string          $baseClass Base class used for PHPStan registration.
	 */
	public function __construct( private DynamicGetterResolver $resolver, private string $baseClass = 'Timber\\CoreEntity' ) {}

	/** {@inheritDoc} */
	public function getClass(): string {
		return $this->baseClass;
	}

	/** {@inheritDoc} */
	public function isMethodSupported( MethodReflection $methodReflection ): bool {
		return strtolower( $methodReflection->getName() ) === 'meta';
	}

	/** {@inheritDoc} */
	public function getTypeFromMethodCall( MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope ): ?Type {
		$args     = $methodCall->getArgs();
		$argument = null;
		foreach ( $args as $index => $arg ) {
			if ( $arg->unpack ) {
				return null;
			}
			if ( $arg->name?->toString() === 'field_name' || ( 0 === $index && null === $arg->name ) ) {
				$argument = $arg->value;
			}
		}
		if ( null === $argument ) {
			return null;
		}
		$field = $scope->getType( $argument );
		if ( ! $field->isConstantScalarValue()->yes() || count( $field->getConstantStrings() ) !== 1 ) {
			return null;
		}
		$name = $field->getConstantStrings()[0]->getValue();
		if ( '' === $name || '0' === $name ) {
			return null;
		}
		$classes = $scope->getType( $methodCall->var )->getObjectClassReflections();
		$types   = array();
		foreach ( $classes as $class_reflection ) {
			if ( ! $this->resolver->dispatches( $class_reflection, 'meta' ) ) {
				return null;
			}
			$type = $this->resolver->return_type( $class_reflection, $name );
			if ( null === $type ) {
				return null;
			}
			$types[] = $type;
		}
		return array() === $types ? null : TypeCombinator::union( ...$types );
	}
}
