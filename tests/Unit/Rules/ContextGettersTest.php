<?php
/**
 * ContextGettersTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PressGang\PHPStan\Rules\ContextGettersRule;
/**
 * Verifies rule diagnostics against source fixtures.
 *
 * @extends RuleTestCase<ContextGettersRule>
 */
class ContextGettersTest extends RuleTestCase {
	/** {@inheritDoc} */
	protected function getRule(): Rule {
		return self::getContainer()->getByType( ContextGettersRule::class );
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
				array( 'Context manifest in Fixtures\\Manifests\\MissingController calls missing method get_missing().', 16 ),
				array( 'Context manifest in Fixtures\\Manifests\\MissingController calls missing method fetch_missing().', 16 ),
			)
		);
	}
}
