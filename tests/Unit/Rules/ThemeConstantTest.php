<?php
/**
 * ThemeConstantTest for PressGang static analysis.
 */

namespace PressGang\PHPStan\Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
/**
 * Verifies THEMENAME resolves without a theme bootstrap.
 *
 * The constant is declared by a scanned file, which PHPStan's in-process test
 * harness does not load, so this runs the PHPStan binary against a fixture.
 */
class ThemeConstantTest extends TestCase {
	/**
	 * THEMENAME is a string, not undefined and not the stub's literal value.
	 *
	 * @return void
	 * @test
	 */
	public function themename_resolves_as_a_string(): void {
		$root    = dirname( __DIR__, 3 );
		$command = sprintf(
			'%s %s analyse --no-progress --error-format=json --level=5 -c %s %s 2>&1',
			escapeshellarg( PHP_BINARY ),
			escapeshellarg( $root . '/vendor/bin/phpstan' ),
			escapeshellarg( $root . '/tests/phpstan.neon' ),
			escapeshellarg( $root . '/tests/constants/themename.php' )
		);

		exec( $command, $output, $status ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec -- Runs the PHPStan binary; the in-process harness ignores scanned files.
		$result = json_decode( (string) end( $output ), true );

		$this->assertSame( 0, $result['totals']['file_errors'] ?? null, implode( "\n", $output ) );
		$this->assertSame( 0, $status );
	}
}
