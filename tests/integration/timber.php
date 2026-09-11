<?php

/** Analyse with a consuming theme autoloader to check real Timber/PressGang contracts. */
namespace PressGang\PHPStan\Integration;

use function PHPStan\Testing\assertType;

class ExamplePost extends \Timber\Post {
 use \PressGang\Traits\HandlesDynamicGetters;
 public function get_display_label(): string { return ''; }
}

function getter_types(ExamplePost $example_post): void {
 assertType('string', $example_post->display_label);
 assertType('string', $example_post->meta('display_label'));
 assertType('mixed', $example_post->meta('unknown_field'));
}

/** These ordinary type errors must remain visible with the extension enabled. */
class ReturnContracts {
 /** @return array<int, ExamplePost> */
 public function mapped_posts(mixed $value): array {
  return \PressGang\ACF\TimberMapper::to_timber_posts($value);
 }
 public function term_link(\Timber\Term $term): string {
  return $term->link;
 }
 public function post_collection(): \Timber\PostQuery {
  return \Timber\Timber::get_posts();
 }
}

class MissingManifest extends \PressGang\Controllers\AbstractController {
 protected array $context_getters = ['missing'];
}
class Recursion extends \Timber\Post {
 use \PressGang\Traits\HandlesDynamicGetters;
 public function get_collision(): mixed { return $this->meta('collision'); }
}
