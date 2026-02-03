# API Reference

This document provides a complete reference for all JsArray methods and properties.

## Table of Contents

- [Creation Methods](#creation-methods)
- [Properties](#properties)
- [Transformation Methods](#transformation-methods)
- [Search Methods](#search-methods)
- [Access Methods](#access-methods)
- [Manipulation Methods](#manipulation-methods)
- [Conversion Methods](#conversion-methods)
- [Mode Conversion Methods](#mode-conversion-methods)

---

## Creation Methods

### from()

Create an immutable JsArray from a PHP array.

```php
public static function from(array $items): self
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
// Returns immutable JsArray
```

### of()

Create an immutable JsArray from individual arguments.

```php
public static function of(mixed ...$items): self
```

**Example:**
```php
$array = JsArray::of(1, 2, 3);
// Returns [1, 2, 3]
```

### mutable()

Create a mutable JsArray for high-performance operations.

```php
public static function mutable(array $items): self
```

**Example:**
```php
$array = JsArray::mutable([1, 2, 3]);
// Operations modify in place
```

### createMutable()

Alias for `mutable()`.

```php
public static function createMutable(array $items): self
```

---

## Properties

### length

Get the number of items in the array (read-only).

```php
public readonly int $length
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
echo $array->length;  // 3
```

### isMutable

Check if the array is in mutable mode.

```php
public readonly bool $isMutable
```

**Example:**
```php
$mutable = JsArray::mutable([1, 2, 3]);
echo $mutable->isMutable;  // true
```

### isImmutable

Check if the array is in immutable mode.

```php
public readonly bool $isImmutable
```

**Example:**
```php
$immutable = JsArray::from([1, 2, 3]);
echo $immutable->isImmutable;  // true
```

---

## Transformation Methods

### map()

Create a new array with results of calling callback on every element.

```php
public function map(callable $callback): self
```

**Parameters:**
- `$callback`: Function with signature `fn($value)`, `fn($value, $index)`, or `fn($value, $index, $array)`

**Returns:** New JsArray (or same instance in mutable mode)

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->map(fn($n) => $n * 2);
// Returns [2, 4, 6]
```

### filter()

Create a new array with elements that pass the test.

```php
public function filter(callable $callback): self
```

**Parameters:**
- `$callback`: Function that returns true to keep element

**Returns:** New JsArray with re-indexed numeric keys

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$result = $array->filter(fn($n) => $n % 2 === 0);
// Returns [2, 4]
```

### reduce()

Reduce the array to a single value.

```php
public function reduce(callable $callback, mixed $initial = null): mixed
```

**Parameters:**
- `$callback`: Function with signature `fn($accumulator, $value)`, `fn($accumulator, $value, $index)`, or `fn($accumulator, $value, $index, $array)`
- `$initial`: Initial accumulator value (optional)

**Returns:** The reduced value

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4]);
$sum = $array->reduce(fn($acc, $n) => $acc + $n, 0);
// Returns 10
```

### flat()

Flatten the array by one or more levels.

```php
public function flat(int $depth = 1): self
```

**Parameters:**
- `$depth`: How many levels to flatten (default: 1)

**Returns:** New JsArray with flattened structure

**Example:**
```php
$array = JsArray::from([1, [2, [3, 4]], 5]);
$flat = $array->flat();
// Returns [1, 2, [3, 4], 5]

$flat2 = $array->flat(2);
// Returns [1, 2, 3, 4, 5]
```

### flatMap()

Map each element and flatten by one level.

```php
public function flatMap(callable $callback): self
```

**Equivalent to:** `$array->map($callback)->flat(1)`

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->flatMap(fn($n) => [$n, $n * 2]);
// Returns [1, 2, 2, 4, 3, 6]
```

### slice()

Extract a portion of the array.

```php
public function slice(int $start, ?int $end = null): self
```

**Parameters:**
- `$start`: Start index (negative counts from end)
- `$end`: End index (optional, negative counts from end)

**Returns:** New JsArray with re-indexed numeric keys

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$result = $array->slice(1, 3);
// Returns [2, 3]

$result2 = $array->slice(-2);
// Returns [4, 5]
```

### splice()

Change contents by removing/replacing elements.

```php
public function splice(int $start, ?int $deleteCount = null, array $items = []): array
```

**Parameters:**
- `$start`: Start position
- `$deleteCount`: Number of items to delete (optional)
- `$items`: Items to insert (optional)

**Returns:** `['deleted' => JsArray, 'array' => JsArray]`

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$result = $array->splice(2, 2, [10, 20]);
// $result['deleted'] = [3, 4]
// $result['array'] = [1, 2, 10, 20, 5]
```

### reverse()

Reverse the array order.

```php
public function reverse(): self
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->reverse();
// Returns [3, 2, 1]
```

### sort()

Sort the array.

```php
public function sort(?callable $callback = null): self
```

**Parameters:**
- `$callback`: Optional comparator function `fn($a, $b) => int`

**Example:**
```php
$array = JsArray::from([3, 1, 2]);
$result = $array->sort();
// Returns [1, 2, 3]

// With comparator
$result = $array->sort(fn($a, $b) => $b <=> $a);
// Returns [3, 2, 1]
```

### concat()

Concatenate multiple arrays.

```php
public function concat(self ...$arrays): self
```

**Example:**
```php
$array1 = JsArray::from([1, 2]);
$array2 = JsArray::from([3, 4]);
$result = $array1->concat($array2);
// Returns [1, 2, 3, 4]
```

---

## Search Methods

### find()

Return first element matching the test.

```php
public function find(callable $callback): mixed
```

**Returns:** The matching element or `null`

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$result = $array->find(fn($n) => $n > 3);
// Returns 4
```

### findIndex()

Return index of first matching element.

```php
public function findIndex(callable $callback): int|string|null
```

**Returns:** Index (numeric arrays), key (associative), or -1/null if not found

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$index = $array->findIndex(fn($n) => $n === 3);
// Returns 2
```

### includes()

Check if array contains a value (strict comparison).

```php
public function includes(mixed $value): bool
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->includes(2);  // true
$result = $array->includes(4);  // false
```

### indexOf()

Find first index of a value.

```php
public function indexOf(mixed $searchElement, int $fromIndex = 0): int
```

**Parameters:**
- `$searchElement`: Value to find
- `$fromIndex`: Start searching from index

**Returns:** Index or -1 if not found

**Example:**
```php
$array = JsArray::from([1, 2, 3, 2, 1]);
$index = $array->indexOf(2);  // Returns 1
$index = $array->indexOf(2, 2);  // Returns 3
```

### lastIndexOf()

Find last index of a value.

```php
public function lastIndexOf(mixed $searchElement, ?int $fromIndex = null): int
```

**Parameters:**
- `$searchElement`: Value to find
- `$fromIndex`: Start searching from index (searches backwards)

**Returns:** Index or -1 if not found

**Example:**
```php
$array = JsArray::from([1, 2, 3, 2, 1]);
$index = $array->lastIndexOf(2);  // Returns 3
```

### some()

Test if any element passes the test.

```php
public function some(callable $callback): bool
```

**Example:**
```php
$array = JsArray::from([1, 2, 3, 4, 5]);
$result = $array->some(fn($n) => $n > 4);  // true
$result = $array->some(fn($n) => $n > 10); // false
```

### every()

Test if all elements pass the test.

```php
public function every(callable $callback): bool
```

**Example:**
```php
$array = JsArray::from([2, 4, 6, 8]);
$result = $array->every(fn($n) => $n % 2 === 0);  // true
$result = $array->every(fn($n) => $n > 5);        // false
```

---

## Access Methods

### first()

Get the first element.

```php
public function first(): mixed
```

**Returns:** First element or `null` if empty

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->first();  // Returns 1
```

### last()

Get the last element.

```php
public function last(): mixed
```

**Returns:** Last element or `null` if empty

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->last();  // Returns 3
```

### at()

Get element at index (supports negative indexing).

```php
public function at(int $index): mixed
```

**Parameters:**
- `$index`: Positive or negative index

**Returns:** Element at index or `null` if out of bounds

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->at(0);   // Returns 1
$result = $array->at(-1);  // Returns 3
$result = $array->at(5);   // Returns null
```

### keys()

Get all array keys.

```php
public function keys(): self
```

**Returns:** JsArray of keys (always immutable)

**Example:**
```php
$array = JsArray::from(['a' => 1, 'b' => 2]);
$keys = $array->keys();  // Returns ['a', 'b']
```

### values()

Get all array values (re-indexed).

```php
public function values(): self
```

**Returns:** JsArray of values (always immutable)

**Example:**
```php
$array = JsArray::from(['a' => 1, 'b' => 2]);
$values = $array->values();  // Returns [1, 2]
```

---

## Manipulation Methods

### push()

Add elements to the end.

```php
public function push(mixed ...$values): self
```

**Example:**
```php
$array = JsArray::from([1, 2]);
$result = $array->push(3, 4);
// Returns [1, 2, 3, 4]
```

### pop()

Remove and return the last element.

```php
public function pop(): array
```

**Returns:** `['array' => JsArray, 'value' => mixed]`

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->pop();
// $result['value'] = 3
// $result['array'] = [1, 2]
```

### unshift()

Add elements to the beginning.

```php
public function unshift(mixed ...$values): self
```

**Example:**
```php
$array = JsArray::from([3, 4]);
$result = $array->unshift(1, 2);
// Returns [1, 2, 3, 4]
```

### shift()

Remove and return the first element.

```php
public function shift(): array
```

**Returns:** `['array' => JsArray, 'value' => mixed]`

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->shift();
// $result['value'] = 1
// $result['array'] = [2, 3]
```

### join()

Join elements into a string.

```php
public function join(string $separator = ','): string
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->join(', ');  // Returns "1, 2, 3"
```

### forEach()

Execute a function for each element.

```php
public function forEach(callable $callback): void
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$array->forEach(fn($value) => echo $value);
// Outputs: 123
```

---

## Conversion Methods

### toArray()

Convert to native PHP array.

```php
public function toArray(): array
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$result = $array->toArray();  // Returns [1, 2, 3]
```

---

## Mode Conversion Methods

### toImmutable()

Convert to immutable mode (creates copy).

```php
public function toImmutable(): self
```

**Example:**
```php
$array = JsArray::mutable([1, 2, 3]);
$immutable = $array->toImmutable();
```

### toMutable()

Convert to mutable mode (modifies in place).

```php
public function toMutable(): self
```

**Example:**
```php
$array = JsArray::from([1, 2, 3]);
$array->toMutable();
// Now $array is mutable
```

### getMutableCopy()

Create a mutable copy.

```php
public function getMutableCopy(): self
```

**Example:**
```php
$original = JsArray::from([1, 2, 3]);
$mutable = $original->getMutableCopy();
// $mutable is mutable, $original is unchanged
```

### getImmutableCopy()

Create an immutable copy.

```php
public function getImmutableCopy(): self
```

**Example:**
```php
$original = JsArray::mutable([1, 2, 3]);
$immutable = $original->getImmutableCopy();
// $immutable is immutable, $original is unchanged
```

---

## Related Documentation

- [PATTERNS.md](./PATTERNS.md) - Common patterns and recipes
- [EXAMPLES.md](./EXAMPLES.md) - Real-world usage examples
- [PERFORMANCE.md](./PERFORMANCE.md) - Performance analysis and tips
- [MUTABILITY.md](./MUTABILITY.md) - Deep dive into mutable/immutable modes
