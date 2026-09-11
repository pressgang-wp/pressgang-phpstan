<?php
/**
 * RecursiveMetaGetterTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PressGang\PHPStan\Rules\RecursiveMetaGetterRule;
/**
 * Verifies rule diagnostics against source fixtures.
 *
 * @extends RuleTestCase<RecursiveMetaGetterRule>
 */
class RecursiveMetaGetterTest extends RuleTestCase {
	/** {@inheritDoc} */
	protected function getRule(): Rule {
		return self::getContainer()->getByType( RecursiveMetaGetterRule::class );
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
			array( __DIR__ . '/../../fixtures/recursion.php' ),
			array(
				array( 'Getter Fixtures\\Recursion\\Model::get_display_label() calls meta(\'display_label\'), which dispatches back to get_display_label(); rename the method without get_ or call parent::meta().', 7 ),
				array( 'Getter Fixtures\\Recursion\\Model::get_named() calls meta(\'named\'), which dispatches back to get_named(); rename the method without get_ or call parent::meta().', 12 ),
				array( 'Getter Fixtures\\Recursion\\ChildModel::get_child() calls meta(\'child\'), which dispatches back to get_child(); rename the method without get_ or call parent::meta().', 17 ),
				array( 'Getter Fixtures\\Recursion\\TraitModel::get_trait_field() calls meta(\'trait_field\'), which dispatches back to get_trait_field(); rename the method without get_ or call parent::meta().', 20 ),
			)
		);
	}
}
