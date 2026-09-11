<?php
/**
 * GetterPropertyReflection for PressGang static analysis.
 */

namespace PressGang\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\NeverType;
use PHPStan\Type\Type;

/**
 * A virtual read-only getter; writes are not handled by HandlesDynamicGetters.
 */
final class GetterPropertyReflection implements PropertyReflection {
	/**
	 * Connects the source-reflection dependencies.
	 *
	 * @param ClassReflection $class_reflection Class being analysed.
	 * @param Type            $type Getter return type.
	 */
	public function __construct( private ClassReflection $class_reflection, private Type $type ) {}

	/** {@inheritDoc} */
	public function getDeclaringClass(): ClassReflection {
		return $this->class_reflection;
	}

	/** {@inheritDoc} */
	public function getReadableType(): Type {
		return $this->type;
	}

	/** {@inheritDoc} */
	public function getWritableType(): Type {
		return new NeverType();
	}

	/** {@inheritDoc} */
	public function canChangeTypeAfterAssignment(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function isReadable(): bool {
		return true;
	}

	/** {@inheritDoc} */
	public function isWritable(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function isStatic(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function isPrivate(): bool {
		return false;
	}

	/** {@inheritDoc} */
	public function isPublic(): bool {
		return true;
	}

	/** {@inheritDoc} */
	public function isDeprecated(): TrinaryLogic {
		return TrinaryLogic::createNo();
	}

	/** {@inheritDoc} */
	public function getDeprecatedDescription(): ?string {
		return null;
	}

	/** {@inheritDoc} */
	public function isInternal(): TrinaryLogic {
		return TrinaryLogic::createNo();
	}

	/** {@inheritDoc} */
	public function getDocComment(): ?string {
		return null;
	}
}
