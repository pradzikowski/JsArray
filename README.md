# JsArray - JavaScript-accurate Array for PHP

![Packagist Version](https://img.shields.io/packagist/v/jsarray/jsarray)
![PHP Version](https://img.shields.io/packagist/php-v/jsarray/jsarray)
![License](https://img.shields.io/github/license/omer73364/jsarray)

A PHP implementation of JavaScript Array methods with immutable operations and identical API behavior.

Bring **JavaScript Array methods** to PHP with **immutable operations** and **identical API behavior**. Chain methods, work with numeric or associative arrays, and write cleaner PHP code with a familiar JS-like style.

## Installation

```bash
composer require jsarray/jsarray
```

## Features

- ✅ **JavaScript-accurate API** - All methods behave exactly like their JavaScript counterparts
- ✅ **Immutable operations** - Every method returns a new JsArray instance
- ✅ **Supports both numeric and associative arrays** - Handles PHP arrays naturally
- ✅ **Performance optimized** - Uses foreach loops, avoids unnecessary array operations
- ✅ **PHP 8+ compatible** - Modern PHP with type hints
- ✅ **Zero dependencies** - Lightweight, pure PHP implementation

## Quick Start

```php
use JsArray\JsArray;

// Create a new JsArray
$numbers = JsArray::from([1, 2, 3, 4, 5]);

// Chain methods like in JavaScript
$result = $numbers
    ->filter(fn($n) => $n % 2 === 0)  // Keep even numbers
    ->map(fn($n) => $n * 2)           // Double them
    ->toArray();                       // Convert back to PHP array

print_r($result); // [4, 8]
```

### Working with Objects

```php
$users = JsArray::from([
    ['name' => 'John', 'age' => 25],
    ['name' => 'Jane', 'age' => 30],
    ['name' => 'Doe', 'age' => 22]
]);

// Get names of users over 23
$names = $users
    ->filter(fn($user) => $user['age'] > 23)
    ->map(fn($user) => $user['name'])
    ->toArray();

print_r($names); // ['John', 'Jane']
```

### More Examples

#### Finding Elements
```php
$users = JsArray::from([
    ['id' => 1, 'name' => 'John'],
    ['id' => 2, 'name' => 'Jane']
]);

// Find user by ID
$user = $users->find(fn($u) => $u['id'] === 2);
// ['id' => 2, 'name' => 'Jane']

// Check if any user is an admin
$hasAdmin = $users->some(fn($u) => $u['is_admin'] ?? false);
// false
```

#### Array Manipulation
```php
$numbers = JsArray::from([1, 2, 3]);

// Add elements
$newNumbers = $numbers->push(4, 5);
// [1, 2, 3, 4, 5]

// Get first and last elements
$first = $numbers->first(); // 1
$last = $numbers->last();   // 3

// Slice array
$middle = $numbers->slice(1, 2); // [2, 3]
```

#### Working with Keys
```php
$data = JsArray::from([
    'a' => 1,
    'b' => 2,
    'c' => 3
]);

// Get all keys
$keys = $data->keys()->toArray(); // ['a', 'b', 'c']

// Get all values
$values = $data->values()->toArray(); // [1, 2, 3]
```


## API Reference

### Properties

#### `length`
The `length` property returns the number of elements in the array.

```php
$array = JsArray::from([1, 2, 3]);
echo $array->length; // 3
```

### Creation Methods

```php
use JsArray\JsArray;

// Static methods
$jsArray = JsArray::from([1, 2, 3]);
$jsArray = JsArray::of(1, 2, 3);
```

### Iteration & Transformation

```php
// Execute a function for each element
$jsArray->forEach(function($value, $index, $array) {
    // Your code here
});

$jsArray->map(fn($value, $index, $array) => $value * 2)
$jsArray->filter(fn($value, $index, $array) => $value > 10)
$jsArray->reduce(fn($accumulator, $value, $index, $array) => $accumulator + $value, 0)
$jsArray->flat()
$jsArray->flatMap(fn($value, $index, $array) => [$value, $value * 2])
$jsArray->slice(1, 3)
$jsArray->concat(JsArray::from([4, 5]), JsArray::from([6]))
```

### Search & Validation

```php
$jsArray->find(fn($value, $index, $array) => $value > 10)
$jsArray->findIndex(fn($value, $index, $array) => $value > 10)  // Returns -1 for numeric, null for associative
$jsArray->includes(5)
$jsArray->some(fn($value, $index, $array) => $value > 10)
$jsArray->every(fn($value, $index, $array) => $value > 0)
```

### Stack-like Operations

```php
$jsArray->push(4, 5)
$result = $jsArray->pop()
// Returns ['array' => new JsArray([...]), 'value' => last_value]
```

### Utility Methods

```php
$jsArray->keys()      // Returns JsArray of keys
$jsArray->values()    // Returns JsArray of values
$jsArray->first()     // Returns first element or null
$jsArray->last()      // Returns last element or null
$jsArray->toArray()   // Returns native PHP array
```

## Callback Signature

All callback-based methods use the JavaScript signature:

```php
callback($value, $indexOrKey, $array)
```

- `$value` - The current value (always first, like JS)
- `$indexOrKey` - Numeric index for arrays, string key for associative
- `$array` - The original JsArray instance (immutable reference)

## Behavioral Notes

### Numeric vs Associative Arrays

- **Numeric arrays**: Automatically reindexed when JavaScript behavior dictates (like `filter()`)
- **Associative arrays**: Keys preserved unless explicitly modified

### findIndex() Returns

```php
// Numeric array: returns -1 when not found
JsArray::from([1, 2, 3])->findIndex(fn($value, $index, $array) => $value > 10);  // -1

// Associative array: returns null when not found
JsArray::from(['a' => 1, 'b' => 2])->findIndex(fn($value, $key, $array) => $value > 10);  // null
```

### Immutability

Every operation returns a new JsArray instance:

```php
$original = JsArray::from([1, 2, 3]);
$modified = $original->push(4);

echo $original->toArray();  // [1, 2, 3] (unchanged)
echo $modified->toArray();  // [1, 2, 3, 4] (new instance)
```

## Requirements

- PHP 7.4+
- Composer

## License

MIT License - see LICENSE file for details.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Why JsArray?

- **Familiar API** - If you know JavaScript Arrays, you know JsArray
- **Type Safety** - Full PHP 8+ type hints
- **Performance** - Optimized for PHP's strengths
- **Predictable** - Immutable operations prevent side effects
- **Flexible** - Works with both numeric and associative arrays naturally
