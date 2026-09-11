<?php
/**
 * RecursiveMetaGetterRule for PressGang static analysis.
 */

namespace PressGang\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PressGang\PHPStan\Reflection\DynamicGetterResolver;

/**
 * Detects the direct self-recursion introduced by getter-backed meta access.
 *
 * @implements Rule<MethodCall>
 */
final class RecursiveMetaGetterRule implements Rule {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param DynamicGetterResolver $resolver Shared contract resolver.
	 */
	public function __construct( private DynamicGetterResolver $resolver ) {}

	/** {@inheritDoc} */
	public function getNodeType(): string {
		return MethodCall::class;
	}

	/** {@inheritDoc} */
	public function processNode( Node $node, Scope $scope ): array {
		$class_reflection = $scope->getClassReflection();
		$method           = $scope->getFunctionName();
		if ( null === $class_reflection || null === $method || $scope->isInAnonymousFunction()
			|| ! $this->resolver->dispatches( $class_reflection, 'meta' )
			|| ! str_starts_with( strtolower( $method ), 'get_' )
			|| ! $node->var instanceof Variable || 'this' !== $node->var->name
			|| ! $node->name instanceof Identifier || 'meta' !== strtolower( $node->name->toString() ) ) {
			return array();
		}
		foreach ( $node->getArgs() as $index => $arg ) {
			if ( $arg->unpack ) {
				return array();
			}
			if ( $arg->name?->toString() !== 'field_name' && ( 0 !== $index || null !== $arg->name ) ) {
				continue;
			}
			$type = $scope->getType( $arg->value );
			if ( ! $type->isConstantScalarValue()->yes() || count( $type->getConstantStrings() ) !== 1 ) {
				return array();
			}
			$field = $type->getConstantStrings()[0]->getValue();
			if ( '' === $field || '0' === $field || strtolower( 'get_' . $field ) !== strtolower( $method ) ) {
				return array();
			}
			return array(
				RuleErrorBuilder::message( sprintf( "Getter %s::%s() calls meta('%s'), which dispatches back to %s(); rename the method without get_ or call parent::meta().", $class_reflection->getName(), $method, $field, $method ) )
									->identifier( 'pressgang.recursiveMetaGetter' )->build(),
			);
		}
		return array();
	}
}
