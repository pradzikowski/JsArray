# Mutability Guide

This document provides a deep dive into mutable and immutable modes in JsArray.

## Understanding Mutability

### What is Immutability?

Immutability means that once an object is created, it cannot be changed. Every operation that appears to modify an immutable object actually returns a new object.

```php
$original = JsArray::from([1, 2, 3]);

// This creates a NEW array, original is unchanged
$modified = $original->map(fn($n) => $n * 2);

echo $original->toArray();  // [1, 2, 3]
echo $modified->toArray();  // [2, 4, 6]
```

### What is Mutability?

Mutability means the object can be changed after creation. Operations modify the object in place.

```php
$array = JsArray::mutable([1, 2, 3]);

// This modifies the array in place
$array->map(fn($n) => $n * 2);

echo $array->toArray();  // [2, 4, 6]
```

## Creating Arrays

### Immutable Mode (Default)

```php
// Create immutable array
$array = JsArray::from([1, 2, 3]);

// All operations return new arrays
$doubled = $array->map(fn($n) => $n * 2);
$filtered = $array->filter(fn($n) => $n > 1);

// Original remains unchanged
assert($array->toArray() === [1, 2, 3]);
```

### Mutable Mode

```php
// Create mutable array
$array = JsArray::mutable([1, 2, 3]);

// Operations modify in place
$array->map(fn($n) => $n * 2);
$array->filter(fn($n) => $n > 1);

// Array is modified
assert($array->toArray() === [4, 6]);  // [2, 4, 6] filtered to [4, 6]
```

### Using Constructor Directly

```php
// Immutable (same as JsArray::from())
$immutable = new JsArray([1, 2, 3], false);

// Mutable (same as JsArray::mutable())
$mutable = new JsArray([1, 2, 3], true);
```

## Converting Between Modes

### Convert to Mutable

```php
$array = JsArray::from([1, 2, 3]);

// Option 1: Convert existing array to mutable
$array->toMutable();
// Now $array is mutable

// Option 2: Create mutable copy (original unchanged)
$mutableCopy = $array->getMutableCopy();
```

### Convert to Immutable

```php
$array = JsArray::mutable([1, 2, 3]);

// Option 1: Convert existing array to immutable
$array->toImmutable();
// Now $array is immutable

// Option 2: Create immutable copy (original unchanged)
$immutableCopy = $array->getImmutableCopy();
```

## Mode Detection

```php
$immutable = JsArray::from([1, 2, 3]);
$mutable = JsArray::mutable([1, 2, 3]);

// Check mode
$immutable->isImmutable;  // true
$immutable->isMutable;    // false

$mutable->isImmutable;    // false
$mutable->isMutable;      // true
```

## When to Use Each Mode

### Use Immutable When:

1. **Data safety is important** - Prevent accidental modifications
2. **Processing user input** - Sanitize without affecting original
3. **Building complex logic** - Chain operations predictably
4. **Debugging** - Easier to trace data flow
5. **Small to medium arrays** (< 10,000 items)
6. **Functional programming** - Pure functions principle

```php
// Example: Processing user form input
$userInput = JsArray::from($request->all());

// Sanitize without affecting original data
$sanitized = $userInput
    ->filter(fn($value) => !empty($value))
    ->map(fn($value) => htmlspecialchars($value));

// Original user input remains for debugging
logData($userInput->toArray());
return $sanitized->toArray();
```

### Use Mutable When:

1. **Processing large datasets** (> 10,000 items)
2. **Performance is critical** - 7x speed improvement
3. **Building or accumulating data** - Iterative construction
4. **Bulk operations** - Imports, migrations, transformations
5. **Memory efficiency matters** - Reduce allocations

```php
// Example: Building large dataset
$results = JsArray::mutable([]);

foreach ($batches as $batch) {
    foreach ($batch as $item) {
        if (processItem($item)) {
            $results->push($item);
        }
    }
}

return $results->toImmutable()->toArray();
```

## Common Patterns

### Start Immutable, Convert When Needed

```php
$array = JsArray::from($initialData);

// Do safe operations
$result = $array
    ->filter(fn($item) => $item['valid'])
    ->map(fn($item) => transform($item));

// If dataset is large, switch to mutable
if (count($result->toArray()) > 50000) {
    $result->toMutable();
    // Now operations are faster
    $result->map(fn($item) => heavyComputation($item));
}

return $result->toImmutable()->toArray();
```

### Use Mutable for Building, Immutable for Storage

```php
// Build data with mutable
$builder = JsArray::mutable([]);
for ($i = 0; $i < 100000; $i++) {
    $builder->push(generateItem($i));
}

// Convert before storing/returning
return $builder->toImmutable()->toArray();
```

### Mixed Mode in Chain

```php
// Start with immutable for safety
$array = JsArray::from($data);

// First pass: filter and map safely
$result = $array
    ->filter(fn($item) => $item['status'] === 'active')
    ->map(fn($item) => ['id' => $item['id'], 'score' => calculate($item)]);

// If more processing needed, convert to mutable
if (count($result->toArray()) > 10000) {
    $result->toMutable();
    $result->sort(fn($a, $b) => $b['score'] <=> $a['score']);
}

return $result->toImmutable()->toArray();
```

## Performance Implications

### Immutable Mode Overhead

Each operation creates a new array:

```php
$array = JsArray::from([1, 2, 3, 4, 5]);

// Creates 3 intermediate arrays
$result = $array
    ->map(fn($n) => $n * 2)      // New array: [2, 4, 6, 8, 10]
    ->filter(fn($n) => $n > 4)   // New array: [6, 8, 10]
    ->map(fn($n) => $n + 1);     // New array: [7, 9, 11]

// Memory: 4 arrays total
// Time: Multiple allocations
```

### Mutable Mode Efficiency

Same operations, single array:

```php
$array = JsArray::mutable([1, 2, 3, 4, 5]);

// Modifies same array
$array
    ->map(fn($n) => $n * 2)      // [2, 4, 6, 8, 10]
    ->filter(fn($n) => $n > 4)   // [6, 8, 10]
    ->map(fn($n) => $n + 1);     // [7, 9, 11]

// Memory: 1 array
// Time: Single allocation
```

## Best Practices

### 1. Default to Immutable

```php
// Safe default
$array = JsArray::from($data);
```

### 2. Switch to Mutable Explicitly

```php
// When performance is needed
$array = JsArray::from($data)->toMutable();
// or
$array = JsArray::mutable($data);
```

### 3. Document Mutable State

```php
/** @var JsArray $results Mutable array of results */
$results = JsArray::mutable([]);
```

### 4. Return Immutable Results

```php
function processData(array $data): JsArray
{
    $array = JsArray::mutable($data);
    // ... processing ...
    return $array->toImmutable();  // Always return immutable
}
```

### 5. Use Type Hints

```php
function getProcessedData(JsArray $immutableData): JsArray
{
    // Method expects immutable, but works with mutable too
    return $immutableData->map(fn($item) => transform($item));
}
```

## Common Mistakes

### Mistake 1: Assuming Mutable Affects Original

```php
// ❌ WRONG - thinking original is modified
$original = JsArray::from([1, 2, 3]);
$doubled = $original->map(fn($n) => $n * 2);

echo $original->toArray();  // [1, 2, 3] - NOT modified!

// ✅ CORRECT - original is unchanged
$doubled = JsArray::mutable([1, 2, 3]);
$doubled->map(fn($n) => $n * 2);
echo $doubled->toArray();  // [2, 4, 6] - modified
```

### Mistake 2: Forgetting to Convert Before Storage

```php
// ❌ WRONG - storing mutable array
$results = JsArray::mutable([]);
$results->push(...);
return $results;  // Caller might modify!

// ✅ CORRECT - convert before return
return $results->toImmutable();
```

### Mistake 3: Using Mutable for Small Data

```php
// ❌ OVERKILL - mutable for 10 items
$array = JsArray::mutable([1, 2, 3]);

// ✅ APPROPRIATE - immutable for small data
$array = JsArray::from([1, 2, 3]);
```

---

## Related Documentation

- [PATTERNS.md](./PATTERNS.md) - Common patterns and recipes
- [EXAMPLES.md](./EXAMPLES.md) - Real-world usage examples
- [PERFORMANCE.md](./PERFORMANCE.md) - Performance analysis and tips
- [API.md](./API.md) - Complete API reference
