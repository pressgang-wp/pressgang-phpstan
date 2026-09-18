<?php
/**
 * PressGang framework constants, declared for static analysis.
 *
 * Child themes define THEMENAME in functions.php, and PressGang's own source
 * uses it as the translation text domain. PHPStan loads neither, so this file is
 * scanned (never executed) to declare the constant. The value is a placeholder:
 * extension.neon lists THEMENAME in dynamicConstantNames, so it is typed as a
 * string rather than this literal.
 */

if ( ! defined( 'THEMENAME' ) ) {
	define( 'THEMENAME', 'pressgang' );
}
