<?php
/**
 * OrphanContextGetterTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PressGang\PHPStan\Rules\OrphanContextGetterRule;
/**
 * Verifies rule diagnostics against source fixtures.
 *
 * @extends RuleTestCase<OrphanContextGetterRule>
 */
class OrphanContextGetterTest extends RuleTestCase {
	/** {@inheritDoc} */
	protected function getRule(): Rule {
		return self::getContainer()->getByType( OrphanContextGetterRule::class );
	}

	/** {@inheritDoc} */
	public static function getAdditionalConfigFiles(): array {
		return array( __DIR__ . '/../../phpstan.neon', __DIR__ . '/../../../rules.neon' );
	}

	/**
	 * Checks contract.
	 *
	 * @return void
	 * @test
	 */
	public function checks_contract(): void {
		$this->analyse(
			array( __DIR__ . '/../../fixtures/manifests.php' ),
			array(
				array( 'Getter Fixtures\\Manifests\\OrphanController::get_unused() is absent from context_getters; add it or annotate intentional direct use with @pressgang-context-helper.', 19 ),
			)
		);
	}
}
