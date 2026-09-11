<?php
namespace Fixtures\Blocks;
use function PHPStan\Testing\assertType;

class BlockModel extends \Timber\CoreEntity {
 use \PressGang\Traits\HandlesDynamicGetters;
 public function get_label(): string { return 'Example'; }
}
trait BlockHelpers {
 protected static function get_label(): string { return 'Heading'; }
}
abstract class BaseBlock extends \PressGang\Blocks\Block {
 use BlockHelpers;
 /**
  * @param array<string, mixed> $block
  * @return array<string, mixed>
  */
 protected static function get_context(mixed $block): array {
  $context = parent::get_context($block);
  assertType('array<string, mixed>', $context);
  $context['heading'] = static::get_label();
  $model = new BlockModel();
  assertType('string', $model->label);
  assertType('string', $model->meta('label'));
  $context['label'] = $model->label;
  return $context;
 }
}
class InheritedBlock extends BaseBlock {}
class ReplacementBlock extends BaseBlock {
 /**
  * @param array<string, mixed> $block
  * @return array<string, mixed>
  */
 protected static function get_context(mixed $block): array {
  return ['heading' => static::get_label()];
 }
 /**
  * @param array<string, mixed> $block
  * @param array<string, mixed>|false $context
  */
 public static function render(array $block, string $content, bool $is_preview, int $post_id, mixed $wp_block, array|false $context): void {
  assertType('array<string, mixed>', static::get_context($block));
 }
}
