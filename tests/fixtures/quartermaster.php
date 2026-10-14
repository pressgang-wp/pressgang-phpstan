<?php

namespace PressGang\Quartermaster;

final class Quartermaster {
	public static function posts( string $type ): self { return new self(); }
	public function idsOnly(): self { return $this; }
	public function limit( int $limit ): self { return $this; }
	public function tapArgs( callable $callback ): self { return $this; }
	/** @return array<int, \WP_Post|int|\stdClass> */
	public function get(): array { return array(); }
}

namespace QuartermasterTypeAssertions;

use PressGang\Quartermaster\Quartermaster;
use function PHPStan\Testing\assertType;

assertType('array<int, int>', Quartermaster::posts('product')->idsOnly()->get());
assertType('array<int, int>', Quartermaster::posts('product')->limit(5)->idsOnly()->get());
assertType('array<int, int|stdClass|WP_Post>', Quartermaster::posts('product')->get());
assertType('array<int, int|stdClass|WP_Post>', Quartermaster::posts('product')->idsOnly()->tapArgs(fn($args) => array('fields' => 'all'))->get());
$query = Quartermaster::posts('product')->idsOnly();
assertType('array<int, int|stdClass|WP_Post>', $query->get());
assertType('array<int, int|stdClass|WP_Post>', Quartermaster::posts('product')->idsOnly()->limit(5)->get());
