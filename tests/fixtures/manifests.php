<?php
namespace Fixtures\Manifests;
use PressGang\Controllers\AbstractController;
trait SharedGetter { protected function get_shared(): string { return ''; } }
abstract class ParentController extends AbstractController {
 protected function get_parent_value(): string { return ''; }
}
class ValidController extends ParentController {
 use SharedGetter;
 protected array $context_getters = ['shared', 'parent_value', 'alias' => 'fetch_posts', 'local'];
 protected function fetch_posts(): array { return []; }
 protected function get_local(): string { return ''; }
 /** @pressgang-context-helper */
 protected function get_helper(): string { return ''; }
}
class MissingController extends AbstractController {
 protected array $context_getters = ['missing', 'renamed' => 'fetch_missing'];
}
class OrphanController extends AbstractController {
 protected array $context_getters = [];
 protected function get_unused(): string { return ''; }
}
class InheritedManifest extends ValidController {}
class OverrideManifest extends ValidController {
 protected array $context_getters = ['shared', 'parent_value', 'local', 'alias' => 'fetch_posts'];
}
class OrdinaryClass {
 protected array $context_getters = ['missing'];
 protected function get_unused(): string { return ''; }
}
