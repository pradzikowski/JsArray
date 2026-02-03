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

---

## Core Features

### 28+ Array Methods

**Transformation**

```php
$array->map(fn($item) => $item * 2);           // Transform each item
$array->filter(fn($item) => $item > 10);       // Keep matching items
$array->reduce(fn($total, $item) => $total + $item, 0);  // Combine to single value
$array->flat();                                 // Flatten nested arrays
$array->flatMap(fn($item) => [$item, $item * 2]); // Map then flatten
```

**Search**

```php
$array->find(fn($item) => $item > 100);        // Get first match
$array->findIndex(fn($item) => $item > 100);   // Get index of first match
$array->includes(50);                          // Check if value exists
$array->indexOf(50);                           // Get index of value
$array->some(fn($item) => $item > 100);        // Check if ANY match
$array->every(fn($item) => $item > 0);         // Check if ALL match
```

**Access**

```php
$array->first();                               // Get first item
$array->last();                                // Get last item
$array->at(0);                                 // Get item at index
$array->at(-1);                                // Get last item (negative index)
$array->length;                                // Get array length
```

**Manipulation**

```php
$array->push($item1, $item2);                  // Add items to end
$array->pop();                                 // Remove and return last item
$array->unshift($item1, $item2);               // Add items to start
$array->shift();                               // Remove and return first item
$array->slice(1, 3);                           // Extract portion
$array->reverse();                             // Reverse order
$array->sort();                                // Sort items
$array->concat($otherArray);                   // Combine arrays
$array->join(', ');                            // Join into string
```

**Other**

```php
$array->keys();                                // Get all keys
$array->values();                              // Get all values
$array->forEach(fn($item) => echo $item);     // Execute for each item
```

---

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

### Immutable Mode (Default - Safe)

Creates a new array for each operation. Original is never changed.

```php
$original = JsArray::from([1, 2, 3]);
$doubled = $original->map(fn($number) => $number * 2);

echo $original->toArray();  // [1, 2, 3] - unchanged
echo $doubled->toArray();   // [2, 4, 6] - new array
```

**Use immutable when:**

- Processing user input
- Data safety matters
- Building complex logic
- Working with small to medium arrays (< 10,000 items)

### Mutable Mode (Fast - Optional)

Modifies the array in-place. Much faster for large datasets.

```php
$array = JsArray::mutable([1, 2, 3, 4, 5]);
$array->map(fn($number) => $number * 2);
$array->filter(fn($number) => $number > 2);

echo $array->toArray();  // [4, 6, 8, 10] - modified in-place
```

**Use mutable when:**

- Processing large datasets (> 50,000 items)
- Performance is critical
- Building or accumulating data
- Bulk operations (imports, migrations)

### Converting Between Modes

```php
// Start with immutable, convert if needed
$array = JsArray::from([1, 2, 3]);

if ($arraySize > 50000) {
    $array->toMutable();  // Switch to mutable mode
}

// Create mutable copy without affecting original
$copy = $array->getMutableCopy();
$copy->map(...)->filter(...);
echo $array->toArray();  // Original unchanged

// Convert back to immutable
$safeArray = $copy->toImmutable();
```

Check the mode:

```php
$array->isMutable;    // bool
$array->isImmutable;  // bool
```

---

## Real-World Examples

See [EXAMPLES.md](./docs/EXAMPLES.md) for detailed real-world examples including:

- Processing form input safely
- Importing CSV files efficiently

---

## Common Patterns

See [PATTERNS.md](./docs/PATTERNS.md) for common patterns and recipes including:

- Filter and transform data
- Reduce to single values
- Group by properties
- Flatten nested arrays
- Chain multiple operations
- And more!

---

## API Reference

See [API.md](./docs/API.md) for complete API documentation including:

- **Creation Methods** - `from()`, `of()`, `mutable()`, `createMutable()`
- **Transformation Methods** - `map()`, `filter()`, `reduce()`, `flat()`, `flatMap()`, `slice()`, `splice()`, `reverse()`, `sort()`, `concat()`
- **Search Methods** - `find()`, `findIndex()`, `includes()`, `indexOf()`, `lastIndexOf()`, `some()`, `every()`
- **Access Methods** - `first()`, `last()`, `at()`, `keys()`, `values()`
- **Manipulation Methods** - `push()`, `pop()`, `unshift()`, `shift()`, `join()`, `forEach()`
- **Conversion Methods** - `toArray()`, `toMutable()`, `toImmutable()`, `getMutableCopy()`, `getImmutableCopy()`

---

## Performance Guide

**Recommended Array Sizes:**

| Size            | Mode        | Speed                                      |
| --------------- | ----------- | ------------------------------------------ |
| < 100 items     | Immutable   | Very fast, no issues                       |
| 100 - 10K items | Immutable   | Fast, acceptable for web apps              |
| 10K - 50K items | Either      | Consider mutable if doing heavy operations |
| > 50K items     | **Mutable** | Use mutable for 7x speed improvement       |
| > 1M items      | **Mutable** | Must use mutable to avoid timeout          |

**Example:**

```php
// Small dataset - use immutable (safe default)
$users = JsArray::from($request->input('users'));

// Large dataset - use mutable (performance)
$data = JsArray::mutable(range(1, 1000000));
```

See [PERFORMANCE.md](./docs/PERFORMANCE.md) for detailed performance analysis.

---

## Tips & Tricks

See [MUTABILITY.md](./docs/MUTABILITY.md) for comprehensive best practices.

### Use Only Parameters You Need

```php
// Don't pass parameters you won't use
$array->map(fn($item) => $item * 2);  // ✅ Clean
$array->map(fn($item, $i, $a) => $item * 2);  // ❌ Unnecessary
```

### Avoid Recreating Arrays in Loops

```php
// ❌ Slow - recreates array each iteration
foreach ($batch as $item) {
    $result = JsArray::from($data)->filter(...);
}

// ✅ Fast - create once, use many times
$array = JsArray::from($data);
foreach ($batch as $item) {
    $result = $array->filter(...);
}
```

### Use Mutable for Building Data

```php
// Building array from multiple operations
$builder = JsArray::mutable([]);
while ($row = getNextRow()) {
    $builder->push($row);
}
// Much faster than immutable mode!
```

### Convert to Immutable When Done

```php
// Do heavy processing with mutable
$data = JsArray::mutable($large_dataset)
    ->filter(...)
    ->map(...);

// Convert to immutable before storing/returning
return $data->toImmutable();
```

---

## Common Questions

### How is this different from Laravel Collections?

**Collections** are Laravel-specific with 100+ methods for general data manipulation.
**JsArray** focuses on JavaScript Array API with familiar syntax.

Use JsArray for:

- JavaScript developers learning PHP
- Specific JavaScript array methods
- Lightweight alternative to Collections

### Does it work with database results?

Yes! Convert your results to array first:

```php
$users = JsArray::from(User::all()->toArray());
$admins = $users->filter(fn($user) => $user['role'] === 'admin');
```

### Can I use it in Laravel?

Absolutely! Works great in any PHP project.

```php
class UserController {
    public function index() {
        $users = JsArray::from(User::all()->toArray());
        $filtered = $users->filter(...)->map(...);
        return response()->json($filtered->toArray());
    }
}
```

### What about object immutability?

JsArray maintains array structure immutability (new instances for operations). Objects within can still be modified. This is the same behavior as JavaScript.

---

## Requirements

- **PHP:** 8.0 or higher
- **Composer:** For easy installation
- **Dependencies:** None! (Pure PHP)

---

## Testing

Run tests:

```bash
composer test
```

Tests include:

- 100+ test cases
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
- 📚 Comprehensive documentation

### v1.0.0

- Initial release

---

<div align="center">

**JsArray** - _JavaScript Arrays in PHP_

[⭐ Star us on GitHub](https://github.com/omer73364/JsArray)

</div>
