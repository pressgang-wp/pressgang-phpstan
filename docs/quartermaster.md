# Quartermaster types

The extension recognises a direct `idsOnly()->get()` call on
`PressGang\Quartermaster\Quartermaster` as `array<int, int>`. It does not require
Quartermaster as a dependency or load WordPress.

Place other fluent calls before `idsOnly()` when using this inference:

```php
$ids = Quartermaster::posts('product')->limit(5)->idsOnly()->get();
```

Stored builders and calls between `idsOnly()` and `get()` retain
the declared return type. The extension does not track mutable query state or
assume callbacks preserve `fields`. It does not enforce query style.
