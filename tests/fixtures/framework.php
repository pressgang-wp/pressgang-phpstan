<?php
namespace PressGang\Controllers;
abstract class AbstractController {
 protected array $context_getters = [];
 protected function get_context(): array { return []; }
}
namespace Timber;
class CoreEntity {
 public function __get($field): mixed { return null; }
 public function meta($field_name = '', $args = []): mixed { return null; }
}

namespace PressGang\Blocks;
class Block {
 /**
  * @param array<string, mixed> $block
  * @return array<string, mixed>
  */
 protected static function get_context(mixed $block): array { return []; }
 /**
  * @param array<string, mixed> $block
  * @param array<string, mixed>|false $context
  */
 public static function render(array $block, string $content, bool $is_preview, int $post_id, mixed $wp_block, array|false $context): void {}
}
