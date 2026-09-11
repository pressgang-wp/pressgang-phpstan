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
