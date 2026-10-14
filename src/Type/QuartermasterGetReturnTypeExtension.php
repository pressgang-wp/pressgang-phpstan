<?php
/**
 * Types the direct Quartermaster ID terminal without tracking mutable builders.
 */

namespace PressGang\PHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;

/** Infers only a direct idsOnly()->get() chain on the concrete builder. */
final class QuartermasterGetReturnTypeExtension implements DynamicMethodReturnTypeExtension {
	/** {@inheritDoc} */
	public function getClass(): string {
		return 'PressGang\\Quartermaster\\Quartermaster';
	}

	/** {@inheritDoc} */
	public function isMethodSupported( MethodReflection $methodReflection ): bool {
		return strtolower( $methodReflection->getName() ) === 'get';
	}

	/** {@inheritDoc} */
	public function getTypeFromMethodCall( MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope ): ?Type {
		$receiver = $methodCall->var;
		if ( ! $receiver instanceof MethodCall || ! $receiver->name instanceof Identifier
			|| strtolower( $receiver->name->toString() ) !== 'idsonly'
			|| array() !== $receiver->getArgs()
			|| $scope->getType( $receiver->var )->getObjectClassNames() !== array( $this->getClass() ) ) {
			return null;
		}

		return new ArrayType( new IntegerType(), new IntegerType() );
	}
}
