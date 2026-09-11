<?php
namespace Fixtures\Types;
use function PHPStan\Testing\assertType;
use PressGang\Traits\HandlesDynamicGetters;
trait NestedDynamic { use HandlesDynamicGetters; }
trait FieldGetter { /** @return list<string> */ protected function get_items(): array { return []; } }
class Model extends \Timber\CoreEntity {
 use NestedDynamic, FieldGetter;
 public int $real = 1;
 public function get_real(): string { return ''; }
 public function get_display_label(): string { return ''; }
 public function get_optional(): ?int { return null; }
 public function get_0(): int { return 1; }
}
class Child extends Model { public function get_display_label(): string { return ''; } }
class Ordinary extends \Timber\CoreEntity { public function get_display_label(): string { return ''; } }
class Overridden extends Model {
 public function meta($field_name = '', $args = []): mixed { return null; }
 public function __get($field): mixed { return null; }
}
function check(Model $model, Child $child, Ordinary $ordinary, Overridden $overridden, string $key): void {
 assertType('string', $model->display_label);
 assertType('string', $model->meta('display_label'));
 assertType('string', $model->meta('display_label', ['transform_value' => true]));
 assertType('string', $model->meta(args: [], field_name: 'display_label'));
 assertType('list<string>', $model->items);
 assertType('list<string>', $child->meta('items'));
 assertType('int|null', $model->optional);
 assertType('int|null', $child->meta('optional'));
 assertType('string', $child->display_label);
 assertType('int', $model->real);
 assertType('mixed', $model->meta('unknown'));
 assertType('mixed', $model->meta($key));
 assertType('mixed', $model->meta());
 assertType('mixed', $model->meta('0'));
 assertType('mixed', $ordinary->meta('display_label'));
 assertType('mixed', $overridden->meta('display_label'));
}

class CustomDispatch extends Model {
 protected function has_custom_getter(string $field): bool { return false; }
}
function custom_dispatch(CustomDispatch $model, Model $normal, bool $choice): void {
 assertType('mixed', $model->meta('display_label'));
 assertType('mixed', $normal->meta($choice ? 'display_label' : 'optional'));
 assertType('mixed', $normal->meta(args: []));
}
