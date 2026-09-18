<?php
/**
 * Analysed by ThemeConstantTest through the PHPStan binary: THEMENAME must
 * resolve as a string without a theme bootstrap.
 */

namespace Fixtures\Constants;

use function PHPStan\Testing\assertType;

assertType( 'string', THEMENAME );
