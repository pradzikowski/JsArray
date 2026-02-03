# JsArray - JavaScript Arrays in PHP

![Packagist Version](https://img.shields.io/packagist/v/jsarray/jsarray)
![PHP Version](https://img.shields.io/packagist/php-v/jsarray/jsarray)
![License](https://img.shields.io/github/license/omer73364/jsarray)
![Tests](https://img.shields.io/badge/tests-100%2B-brightgreen)

> **Write JavaScript Array code in PHP.** Familiar, powerful, and intuitive API for working with arrays.

If you know JavaScript arrays, you already know JsArray.

---

## What is JsArray?

JsArray brings the beloved JavaScript Array methods to PHP with:

- ✅ **Familiar API** - Same methods, same behavior as JavaScript
- ✅ **Full Type Safety** - PHP 8+ with complete type hints
- ✅ **Flexible** - Choose immutable (safe) or mutable (fast) mode
- ✅ **Pure PHP** - Zero dependencies, lightweight
- ✅ **Well Tested** - 100+ test cases, 95%+ coverage

---

## Installation

```bash
composer require jsarray/jsarray
```

**Requirements:** PHP 8.0+

---

## Quick Start

### The Basics

```php
use JsArray\JsArray;

$numbers = JsArray::from([1, 2, 3, 4, 5]);

// Transform data
$doubled = $numbers->map(fn($number) => $number * 2);

// Keep only matching items
$evenNumbers = $numbers->filter(fn($number) => $number % 2 === 0);

// Combine operations (method chaining)
$result = $numbers
    ->filter(fn($number) => $number > 2)
    ->map(fn($number) => $number * 10)
    ->toArray();

// Result: [30, 40, 50]
```

### Working with Objects

```php
$users = JsArray::from([
    ['name' => 'Alice', 'age' => 25],
    ['name' => 'Bob', 'age' => 17],
    ['name' => 'Charlie', 'age' => 30],
]);

// Get names of adults
$adultNames = $users
    ->filter(fn($user) => $user['age'] >= 18)
    ->map(fn($user) => $user['name'])
    ->toArray();

// Result: ['Alice', 'Charlie']
```

## Flexible Callback Signatures

Use only the parameters you need:

```php
// Just the item value
$numbers->map(fn($number) => $number * 2);

// Item and its index
$items->filter(fn($item, $index) => $index > 0);
$numbers->forEach(fn($number, $index) => echo "$index: $number");

// Full context (item, index, and array reference)
$array->map(fn($item, $index, $array) =>
    $item > ($array->length / 2) ? 'big' : 'small'
);
```

JsArray automatically detects which parameters your callback uses. No configuration needed!

---

## Two Modes: Immutable & Mutable

### Immutable Mode (Default)

Creates a new array for each operation. Original is never changed.

```php
$original = JsArray::from([1, 2, 3]);
$doubled = $original->map(fn ($n) => $n * 2);

$original->toArray(); // [1, 2, 3]
$doubled->toArray();  // [2, 4, 6]
```

**Use immutable when:**

- Processing user input
- Data safety matters
- Building complex logic
- Working with small to medium arrays (< 10,000 items)

### Mutable Mode (Fast)

Modifies the array in-place. Much faster for large datasets.

```php
$array = JsArray::mutable([1, 2, 3, 4, 5]);

$array
    ->map(fn ($n) => $n * 2)
    ->filter(fn ($n) => $n > 2);

$array->toArray(); // [4, 6, 8, 10]
```

**Use mutable when:**

- Processing large datasets (> 50,000 items)
- Performance is critical
- Building or accumulating data
- Bulk operations (imports, migrations)

### Converting Between Modes

Start with immutable, convert if needed

```php
$array = JsArray::from([1, 2, 3]);

if ($arraySize > 50000) {
    $array->toMutable();  // Switch to mutable mode
}
```

Create mutable copy without affecting original

```php
$copy = $array->getMutableCopy();
$copy->map(...)->filter(...);
echo $array->toArray();  // Original unchanged
```

Convert back to immutable

```php
$safeArray = $copy->toImmutable();
```

Check the mode:

```php
$array->isMutable;    // bool
$array->isImmutable;  // bool
```

---

## Testing

Run tests:

```bash
composer test
```

Tests include:

- 190+ test cases
- All methods covered
- Edge cases tested
- Immutable/mutable modes tested

---

## Contributing

We welcome contributions!

1. Fork the repository
2. Create a feature branch
3. Write tests for your changes
4. Ensure tests pass
5. Submit a pull request

See [CONTRIBUTING.md](./CONTRIBUTING.md) for details.

---

## Documentation Files

- **[PATTERNS.md](./docs/PATTERNS.md)** - Common patterns and recipes
- **[EXAMPLES.md](./docs/EXAMPLES.md)** - Real-world usage examples
- **[PERFORMANCE.md](./docs/PERFORMANCE.md)** - Performance analysis and tips
- **[MUTABILITY.md](./docs/MUTABILITY.md)** - Deep dive into mutable/immutable modes
- **[API.md](./docs/API.md)** - Complete API reference

---

## License

MIT License - See [LICENSE](./LICENSE) for details.

---

## Support

- 🐛 [Report Issues](https://github.com/omer73364/JsArray/issues)
- 💡 [Request Features](https://github.com/omer73364/JsArray/discussions)
- 📝 [View Discussions](https://github.com/omer73364/JsArray/discussions)

---

## Changelog

### v2.0.0

- ✨ Added mutable mode for performance
- ✨ 5 new methods (shift, unshift, indexOf, lastIndexOf, reverse)
- ✨ Flexible callback signatures (use only params you need!)
- ✨ Depth parameter for flat()
- 🐛 Fixed filter re-indexing for numeric arrays
- 📚 Comprehensive documentation files

### v1.0.0

- Initial release

---

<div align="center">

**JsArray** - _JavaScript Arrays in PHP_

</div>
