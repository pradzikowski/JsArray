# Common Patterns

This document provides common patterns and recipes for working with JsArray.

## Pattern 1: Filter and Transform

Filter items based on a condition and transform them.

```php
$users = JsArray::from([
    ['name' => 'Alice', 'role' => 'admin'],
    ['name' => 'Bob', 'role' => 'user'],
    ['name' => 'Charlie', 'role' => 'admin'],
]);

$admins = $users
    ->filter(fn($user) => $user['role'] === 'admin')
    ->map(fn($user) => $user['name'])
    ->toArray();

// Result: ['Alice', 'Charlie']
```

## Pattern 2: Reduce to Single Value

Accumulate items into a single result using `reduce()`.

```php
$transactions = JsArray::from([
    ['type' => 'income', 'amount' => 1000],
    ['type' => 'expense', 'amount' => 300],
    ['type' => 'income', 'amount' => 500],
]);

$total = $transactions->reduce(function($balance, $transaction) {
    $change = $transaction['type'] === 'income' ? $transaction['amount'] : -$transaction['amount'];
    return $balance + $change;
}, 0);

// Result: 1200
```

## Pattern 3: Group By Property

Group items by a specific property using `reduce()`.

```php
$items = JsArray::from([
    ['id' => 1, 'category' => 'food'],
    ['id' => 2, 'category' => 'books'],
    ['id' => 3, 'category' => 'food'],
]);

$grouped = $items->reduce(function($groups, $item) {
    $category = $item['category'];
    if (!isset($groups[$category])) {
        $groups[$category] = [];
    }
    $groups[$category][] = $item;
    return $groups;
}, []);

// Result:
// [
//     'food' => [['id' => 1, ...], ['id' => 3, ...]],
//     'books' => [['id' => 2, ...]]
// ]
```

## Pattern 4: Flatten Nested Arrays

Flatten multi-dimensional arrays using `flat()`.

```php
$nested = JsArray::from([
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
]);

$flat = $nested->flat();
// Result: [1, 2, 3, 4, 5, 6, 7, 8, 9]

// Control depth
$deepNested = JsArray::from([1, [2, [3, [4]]]]);
$flat1 = $deepNested->flat(1);    // [1, 2, [3, [4]]]
$flat2 = $deepNested->flat(2);    // [1, 2, 3, [4]]
$flatAll = $deepNested->flat(999); // [1, 2, 3, 4]
```

## Pattern 5: Chaining Multiple Operations

Chain multiple array operations together.

```php
$result = JsArray::from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10])
    ->filter(fn($number) => $number % 2 === 0)  // Keep even: [2, 4, 6, 8, 10]
    ->map(fn($number) => $number * 10)          // Multiply: [20, 40, 60, 80, 100]
    ->filter(fn($number) => $number < 90)       // Keep < 90: [20, 40, 60, 80]
    ->reverse()                                  // Reverse: [80, 60, 40, 20]
    ->toArray();

// Result: [80, 60, 40, 20]
```

## Pattern 6: Find First Matching Item

Use `find()` to get the first item matching a condition.

```php
$products = JsArray::from([
    ['name' => 'Laptop', 'price' => 999],
    ['name' => 'Mouse', 'price' => 29],
    ['name' => 'Keyboard', 'price' => 79],
]);

$expensiveItem = $products->find(fn($product) => $product['price'] > 500);
// Result: ['name' => 'Laptop', 'price' => 999]
```

## Pattern 7: Check Conditions with some() and every()

Use `some()` for "any" checks and `every()` for "all" checks.

```php
$numbers = JsArray::from([2, 4, 6, 8, 10]);

// Check if ANY number is odd
$hasOdd = $numbers->some(fn($n) => $n % 2 === 1);
// Result: false

// Check if ALL numbers are even
$allEven = $numbers->every(fn($n) => $n % 2 === 0);
// Result: true
```

## Pattern 8: Sort with Custom Comparator

Sort arrays using custom comparison logic.

```php
$users = JsArray::from([
    ['name' => 'Alice', 'age' => 25],
    ['name' => 'Bob', 'age' => 30],
    ['name' => 'Charlie', 'age' => 20],
]);

// Sort by age ascending
$sortedByAge = $users->sort(fn($a, $b) => $a['age'] <=> $b['age']);
// Result: [Charlie(20), Alice(25), Bob(30)]

// Sort by name alphabetically
$sortedByName = $users->sort(fn($a, $b) => $a['name'] <=> $b['name']);
// Result: [Alice, Bob, Charlie]
```

## Pattern 9: Remove and Extract Items

Use `splice()` to remove and/or replace items.

```php
$numbers = JsArray::from([1, 2, 3, 4, 5]);

// Remove 2 items starting at index 2
$result = $numbers->splice(2, 2);
$removed = $result['deleted']->toArray();   // [3, 4]
$remaining = $result['array']->toArray();   // [1, 2, 5]

// Remove and replace
$numbers2 = JsArray::from([1, 2, 3, 4, 5]);
$result2 = $numbers2->splice(2, 1, [10, 20]);
$removed2 = $result2['deleted']->toArray();    // [3]
$modified = $result2['array']->toArray();      // [1, 2, 10, 20, 4, 5]
```

## Pattern 10: Working with String Arrays

Join, split, and manipulate string arrays.

```php
$words = JsArray::from(['Hello', 'World', 'from', 'JsArray']);

// Join into string
$sentence = $words->join(' ');
// Result: "Hello World from JsArray"

// Create comma-separated list
$csv = $words->join(', ');
// Result: "Hello, World, from, JsArray"
```

## Pattern 11: Immutable Data Pipeline

Build a processing pipeline that preserves original data.

```php
$originalData = JsArray::from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$pipeline = function($data) {
    return $data
        ->filter(fn($n) => $n % 2 === 0)
        ->map(fn($n) => $n * $n)
        ->reduce(fn($acc, $n) => $acc + $n, 0);
};

$result = $pipeline($originalData);
// Result: 220 (4 + 16 + 36 + 64 + 100)

// Original data is unchanged
$originalData->toArray(); // [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
```

## Pattern 12: Mutable Mode for Large Datasets

Use mutable mode for efficient bulk operations.

```php
// Build a large dataset efficiently
$largeArray = JsArray::mutable([]);

// Simulate adding 100,000 items
for ($i = 0; $i < 100000; $i++) {
    $largeArray->push([
        'id' => $i,
        'value' => $i * 2,
        'processed' => false,
    ]);
}

// Process in-place
$largeArray
    ->filter(fn($item) => $item['value'] > 1000)
    ->map(fn($item) => array_merge($item, ['processed' => true]));

// Convert to immutable for safe return
return $largeArray->toImmutable()->toArray();
```

## Pattern 13: Find Index and Use It

Find the index of an item and use it for further operations.

```php
$items = JsArray::from(['apple', 'banana', 'cherry', 'date']);

// Find index of 'cherry'
$index = $items->findIndex(fn($item) => $item === 'cherry');
// Result: 2

// Use index to get surrounding items
$previous = $items->at($index - 1);  // 'banana'
$next = $items->at($index + 1);      // 'date'
```

## Pattern 14: Distinct/Unique Values

Get unique values from an array.

```php
$numbers = JsArray::from([1, 2, 2, 3, 3, 3, 4, 4, 4, 4]);

// Using reduce to get distinct values
$distinct = $numbers->reduce(function($acc, $n) {
    if (!$acc->includes($n)) {
        $acc->push($n);
    }
    return $acc;
}, JsArray::from([]))->toArray();

// Result: [1, 2, 3, 4]
```

## Pattern 15: Partition Array

Split an array into two parts based on a condition.

```php
$numbers = JsArray::from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$partition = $numbers->reduce(function($acc, $n) {
    if ($n % 2 === 0) {
        $acc['even']->push($n);
    } else {
        $acc['odd']->push($n);
    }
    return $acc;
}, JsArray::from(['even' => JsArray::mutable([]), 'odd' => JsArray::mutable([])]));

// Result:
// ['even' => [2, 4, 6, 8, 10], 'odd' => [1, 3, 5, 7, 9]]
```

---

## Related Documentation

- [EXAMPLES.md](./EXAMPLES.md) - Real-world usage examples
- [PERFORMANCE.md](./PERFORMANCE.md) - Performance analysis and tips
- [MUTABILITY.md](./MUTABILITY.md) - Deep dive into mutable/immutable modes
- [API.md](./API.md) - Complete API reference
