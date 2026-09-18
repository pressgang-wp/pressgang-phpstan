# PressGang PHPStan 🔍

**Static analysis for [PressGang](https://pressgang.dev) WordPress themes built with Timber and Twig.** Teaches
[PHPStan](https://phpstan.org) about controller context manifests and dynamic model
getters, so CI catches a missing `get_featured_posts()` or a self-recursive `meta()`
before the page renders. No WordPress boot, no database. ⚡

PHP 8.2+ · PHPStan 2.2.9+ · MIT

> **New to PressGang?** A modern WordPress parent theme framework built on Timber and
> Twig — controllers and config instead of spaghetti templates.
> [pressgang.dev](https://pressgang.dev) · [Docs](https://docs.pressgang.dev) ·
> [Controllers](https://docs.pressgang.dev/controllers)

## What it catches ✅

A controller declares context getters; a model exposes typed values through getters:

```php
use PressGang\Controllers\AbstractController;
use PressGang\Traits\HandlesDynamicGetters;

// Controller — every manifest entry must be backed by a getter
class PageController extends AbstractController {
    protected array $context_getters = ['featured_posts'];

    /** @return list<\Timber\Post> */
    protected function get_featured_posts(): array {
        return []; // Replace with your content query.
    }
}

// Model — getters back dynamic properties and meta()
class Page extends \Timber\Post {
    use HandlesDynamicGetters;

    protected function get_subtitle(): string {
        return 'Example subtitle';
    }
}
```

| You write | PHPStan now knows | Identifier |
| --- | --- | --- |
| `'featured_posts'` added to the manifest | `get_featured_posts()` must exist — and aliases like `'featured_posts' => 'fetch_featured_posts'`, across inherited and trait methods | `pressgang.contextGetterMissing` |
| `$page->subtitle` | it's the `string` from `get_subtitle()` — generics and nullables included | type extension |
| `$page->meta('subtitle')` | the same `string`, not `mixed`. Named arguments supported | type extension |
| `get_subtitle()` calling `$this->meta('subtitle')` | infinite recursion 💥 — rename it, or call `parent::meta()` | `pressgang.recursiveMetaGetter` |

Using blocks? Covered too — `get_context()` and `render()` are checked directly, with
no extra configuration. Details below. 🧱

## Install 📦

Currently local and unpublished, so point Composer at your checkout:

```sh
composer config repositories.pressgang-phpstan path /path/to/pressgang-phpstan
composer config allow-plugins.phpstan/extension-installer true
composer require --dev pressgang-wp/phpstan:@dev phpstan/extension-installer:^1.4 \
  szepeviktor/phpstan-wordpress:^2.0 php-stubs/acf-pro-stubs:^6.8
```

The ACF stubs and `scanFiles` entry below are only needed for themes using ACF.
Your installed framework may require a newer PHP version than this extension.

Add a theme `phpstan.neon`:

```neon
parameters:
    level: 5
    paths:
        - src
    scanFiles:
        - vendor/php-stubs/acf-pro-stubs/acf-pro-stubs.php
```

Then run `vendor/bin/phpstan analyse --memory-limit=1G`. 🚀 Your theme and framework
just need to be discoverable through Composer.

The extension declares PressGang's `THEMENAME` constant, which child themes define in
`functions.php`, so analysis needs no theme bootstrap file.

`extension.neon` is included automatically — don't also add it by hand. Without
`phpstan/extension-installer`, include `vendor/pressgang-wp/phpstan/extension.neon`
yourself.

<details>
<summary><strong>Behaviour details</strong> — what's typed, what's left alone</summary>

Dynamic property typing is read-only; the trait has no setter contract. Real
properties keep their native types, and only native getters count, not `@method`
declarations. Trait use is followed through parent classes and nested traits.
Overrides of `__get`, `meta`, `has_custom_getter` or `call_custom_getter` are left to
their declared behaviour, and the getter argument resolution hook may still be
overridden, as PressGang supports.

`meta()` needs a single known, nonempty constant string. Dynamic keys, unknown
getters, uncertain unions, argument unpacking and the falsey keys `''` and `'0'`
retain the declared return type. Getter dispatch happens **before** Timber's parent
meta processing, so `transform_value` options don't change a custom getter's return
type. Ordinary ACF fields stay as declared, usually `mixed` — this package won't
invent field shapes from runtime configuration.

Manifest references are registered with PHPStan's unused-method extension point. That
describes declared manifest usage; it doesn't make all controller methods used, or
replace third-party dead-code tools' own call graphs.

**Non-Timber base class?** Extensions register against classes, not traits, so the
default is `Timber\CoreEntity`. For your own base class, register another instance:

```neon
services:
    -
        class: PressGang\PHPStan\Type\MetaReturnTypeExtension
        arguments:
            baseClass: Your\ModelBase
        tags:
            - phpstan.broker.dynamicMethodReturnTypeExtension
```

The property and recursion extensions already apply to every trait user.
`AbstractController` doesn't currently use `HandlesDynamicGetters` itself.

</details>

<details>
<summary><strong>Optional orphan advice</strong> — flag getters absent from the manifest</summary>

```neon
includes:
    - vendor/pressgang-wp/phpstan/rules.neon
```

`pressgang.contextGetterOrphan` reports `get_*()` methods absent from a concrete
controller's effective manifest, including inherited and trait methods.
`get_context()` and `get_template_candidates()` are excluded, and abstract controllers
aren't assessed — they can supply getters for child manifests.

It's advisory: direct calls, hooks and framework lifecycle calls can all use a getter
without a manifest entry. Mark intentional helpers on the method:

```php
/**
 * Called by get_featured_posts(), so it never appears in the manifest.
 *
 * @pressgang-context-helper
 */
protected function get_excerpt_length(): int {
    return 40;
}
```

PHPStan has no separate warning severity, so opting in makes these diagnostics count
toward its exit status. The annotation suppresses only this advisory — missing-method
and recursion diagnostics are unaffected. Don't remove methods on this advice alone;
absence from a manifest doesn't itself execute a query.

</details>

<details>
<summary><strong>Blocks</strong> — they just work, with one gotcha</summary>

Nothing to configure: analysing `src` already covers `src/Blocks`. Block subclasses
use ordinary static `get_context()` and `render()` methods, so PHPStan checks those
calls and overrides directly.

**The gotcha:** blocks don't use controller manifests, so block `get_*()` helpers
never need `@pressgang-context-helper` — the orphan rule leaves them alone.

Model getters behave exactly as they do anywhere else, including inside a block:

```php
abstract class BaseBlock extends \PressGang\Blocks\Block {
    /**
     * @param array<string, mixed> $block
     * @return array<string, mixed>
     */
    protected static function get_context(mixed $block): array {
        $context = parent::get_context($block);        // array<string, mixed>
        $context['subtitle'] = (new Page())->subtitle; // still a string
        return $context;
    }
}
```

Document a custom `get_context()` as `array<string, mixed>` in and out, keeping the
framework's native `mixed` parameter. Inherited implementations, trait helpers and
deliberately replaced contexts are all supported — calling `parent::get_context()`
isn't mandatory.

The extension doesn't promise a fixed block-context shape: ACF fields can replace
context keys, and their values depend on runtime field configuration. Block
registration, callback wiring and template discovery still need runtime checks.

</details>

<details>
<summary><strong>Boundaries</strong> — what it deliberately doesn't see</summary>

The manifest resolver reads source-reflected property defaults and treats a child
declaration as a replacement, not a merge; unresolvable or non-string defaults are
skipped. It doesn't model constructor mutations, runtime filters, dynamic manifest
assignment, indirect call graphs, recursive cycles across multiple getters, or
closures passed elsewhere. The recursion rule detects direct `$this->meta()` calls in
the current getter, including constant expressions and named arguments, and skips
nested closure bodies and overridden dispatch.

Config, snippets, bootstrap wiring and installed namespaces belong to
[`wp capstan doctor`](https://docs.pressgang.dev/ecosystem/capstan). Config shape maps
and Twig/template validation are out of scope. This package loads neither PressGang
nor Timber at runtime — consumers provide their installed framework for reflection.

</details>

<details>
<summary><strong>Development</strong> — tests and the integration fixture</summary>

```sh
composer install
composer update phpstan/phpstan:2.2.9  # validate the supported minimum
composer check                         # PHPUnit + level-8 PHPStan + WordPress PHPCS
```

Tests mirror `tests/Unit` in PressGang and use PHPStan's `RuleTestCase` and
`TypeInferenceTestCase` rather than booting WordPress — see the
[testing guide](https://docs.pressgang.dev/testing). The isolated trait fixture is
copied from PressGang's current `HandlesDynamicGetters`; keep it in sync when that
contract changes. Fixtures cover aliases, numeric keys, parent and trait getters,
inherited and replaced manifests, orphan opt-outs, recursion, native properties,
overridden dispatch, nullable and generic types, and fallback meta calls.

The optional fixture at `tests/integration/timber.php` runs generic model and
controller classes against real Timber and PressGang dependencies. It intentionally
produces five diagnostics: an array subtype overclaim, an undefined Term property, a
collection return mismatch, a missing manifest getter and a recursive meta getter.
Getter-backed property and meta type assertions must pass. Run it from a consuming
theme, using a config without a baseline or theme-specific ignores:

```sh
vendor/bin/phpstan analyse -c /path/to/integration.neon --memory-limit=1G \
  /path/to/pressgang-phpstan/tests/integration/timber.php
```

</details>

## The rest of the fleet ⚓

| Package | What it does |
| --- | --- |
| [PressGang](https://github.com/pressgang-wp/pressgang) | The Timber/Twig parent theme framework |
| [Capstan](https://docs.pressgang.dev/ecosystem/capstan) | WP-CLI scaffolding — `wp capstan new` |
| [Bosun](https://docs.pressgang.dev/ecosystem/bosun) | AI agent guidelines — `wp bosun install` |
| [Quartermaster](https://docs.pressgang.dev/ecosystem/quartermaster) | Fluent query builder for `WP_Query` args |
| [Muster](https://docs.pressgang.dev/ecosystem/muster) | Content seeding and dev fixtures |

Built by [Benedict Wallis](https://benedict-wallis.com) · [MIT](LICENSE)
