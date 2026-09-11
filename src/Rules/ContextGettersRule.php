<?php
/**
 * ContextGettersRule for PressGang static analysis.
 */

namespace PressGang\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PressGang\PHPStan\Reflection\ContextManifestResolver;

/**
 * Checks dynamic manifest calls against inherited and trait methods.
 *
 * @implements Rule<InClassNode>
 */
final class ContextGettersRule implements Rule {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param ContextManifestResolver $resolver Shared contract resolver.
	 */
	public function __construct( private ContextManifestResolver $resolver ) {}

	/** {@inheritDoc} */
	public function getNodeType(): string {
		return InClassNode::class;
	}

	/** {@inheritDoc} */
	public function processNode( Node $node, Scope $scope ): array {
		$class_reflection = $node->getClassReflection();
		$manifest         = $this->resolver->resolve( $class_reflection );
		if ( null === $manifest ) {
			return array();
		}
		$errors = array();
		foreach ( $this->resolver->methods( $manifest ) as $method ) {
			if ( ! $class_reflection->hasNativeMethod( $method ) ) {
				$errors[] = RuleErrorBuilder::message( sprintf( 'Context manifest in %s calls missing method %s().', $class_reflection->getName(), $method ) )
					->identifier( 'pressgang.contextGetterMissing' )->build();
			}
		}
		return $errors;
	}
}
