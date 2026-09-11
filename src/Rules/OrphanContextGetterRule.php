<?php
/**
 * OrphanContextGetterRule for PressGang static analysis.
 */

namespace PressGang\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PressGang\PHPStan\Reflection\ContextManifestResolver;

/**
 * Advisory: absence from a manifest is not proof of dead code.
 *
 * @implements Rule<InClassNode>
 */
final class OrphanContextGetterRule implements Rule {
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
		if ( null === $manifest || $class_reflection->isAbstract() ) {
			return array();
		}
		$reachable = $this->resolver->methods( $manifest );
		$errors    = array();
		foreach ( $class_reflection->getNativeReflection()->getMethods() as $method ) {
			$name = strtolower( $method->getName() );
			// Framework lifecycle APIs are not context manifest entries.
			if ( ! str_starts_with( $name, 'get_' ) || 'get_context' === $name || 'get_template_candidates' === $name
				|| in_array( $name, $reachable, true )
				|| str_contains( false !== $method->getDocComment() ? $method->getDocComment() : '', '@pressgang-context-helper' ) ) {
				continue;
			}
			$errors[] = RuleErrorBuilder::message( sprintf( 'Getter %s::%s() is absent from context_getters; add it or annotate intentional direct use with @pressgang-context-helper.', $class_reflection->getName(), $method->getName() ) )
				->identifier( 'pressgang.contextGetterOrphan' )->build();
		}
		return $errors;
	}
}
