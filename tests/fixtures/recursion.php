<?php
namespace Fixtures\Recursion;
use PressGang\Traits\HandlesDynamicGetters;
class Model extends \Timber\CoreEntity {
 use HandlesDynamicGetters;
 public function get_display_label(): mixed {
  return $this->meta('display_label');
 }
 public function get_safe(): mixed { return parent::meta('safe'); }
 public function unknown_field(): mixed { return $this->meta('unknown_field'); }
 public function get_different(): mixed { return $this->meta('other'); }
 public function get_named(): mixed { return $this->meta(args: [], field_name: 'named'); }
 public function get_0(): mixed { return $this->meta('0'); }
 public function get_closure(): mixed { return function () { return $this->meta('closure'); }; }
}
class ChildModel extends Model {
 public function get_child(): mixed { return $this->meta('child'); }
}
trait GetterTrait {
 public function get_trait_field(): mixed { return $this->meta('trait_field'); }
}
class TraitModel extends Model { use GetterTrait; }
class OrdinaryModel extends \Timber\CoreEntity {
 public function get_plain(): mixed { return $this->meta('plain'); }
}
class OverriddenModel extends Model {
 public function meta($field_name = '', $args = []): mixed { return null; }
 public function get_override(): mixed { return $this->meta('override'); }
}
