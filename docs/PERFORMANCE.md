# Performance Guide

This document provides detailed performance analysis and optimization tips for JsArray.

## Recommended Array Sizes by Mode

| Size | Mode | Speed | Recommendation |
|------|------|-------|----------------|
| < 100 items | Immutable | Very fast | No issues, use immutable |
| 100 - 10K items | Immutable | Fast | Acceptable for web apps |
| 10K - 50K items | Either | Good | Consider mutable for heavy operations |
| > 50K items | **Mutable** | 7x faster | Use mutable for best performance |
| > 1M items | **Mutable** | Required | Must use mutable to avoid timeout |

## Performance Comparison

### Immutable vs Mutable Mode

```php
// Immutable mode - creates new array each operation
$array = JsArray::from(range(1, 10000));
$result = $array->map(fn($n) => $n * 2)->filter(fn($n) => $n > 1000);
// Memory: Creates 2 intermediate arrays
// Time: Slower due to array copying

// Mutable mode - modifies in place
$array = JsArray::mutable(range(1, 10000));
$array->map(fn($n) => $n * 2);
$array->filter(fn($n) => $n > 1000);
// Memory: Single array, minimal allocation
// Time: 7x faster on average
```

## Benchmark Results

### Map Operation (10,000 items)

| Mode | Time | Memory |
|------|------|--------|
| Immutable | ~15ms | ~2MB |
| Mutable | ~2ms | ~0.5MB |

### Filter Operation (10,000 items)

| Mode | Time | Memory |
|------|------|--------|
| Immutable | ~12ms | ~1.5MB |
| Mutable | ~2ms | ~0.3MB |

### Chain Operations (10,000 items)

| Mode | Time | Memory |
|------|------|--------|
| Immutable (3 ops) | ~45ms | ~6MB |
| Mutable (3 ops) | ~6ms | ~1MB |

## Optimization Strategies

### 1. Choose the Right Mode from the Start

```php
// Small dataset - use immutable (safe default)
$users = JsArray::from($request->input('users'));
// Processing: ~1ms for <100 items

// Large dataset - use mutable (performance)
$data = JsArray::mutable(range(1, 1000000));
// Processing: ~100ms vs ~700ms with immutable
```

### 2. Avoid Recreating Arrays in Loops

```php
// ❌ SLOW - recreates array each iteration
foreach ($batch as $item) {
    $result = JsArray::from($data)->filter(...)->map(...);
}
// Creates thousands of JsArray instances

// ✅ FAST - create once, use many times
$array = JsArray::from($data);
foreach ($batch as $item) {
    $result = $array->filter(...)->map(...);
}
// Single JsArray instance reused
```

### 3. Use Mutable for Building Data

```php
// Building array from multiple operations
$builder = JsArray::mutable([]);
while ($row = getNextRow()) {
    $builder->push($row);
}
// Much faster than immutable mode!

// For very large datasets (>1M items)
$builder = JsArray::mutable(range(1, 10000000));
```

### 4. Convert to Immutable When Done

```php
// Do heavy processing with mutable
$data = JsArray::mutable($large_dataset)
    ->filter(...)
    ->map(...);

// Convert to immutable before storing/returning
return $data->toImmutable();
```

### 5. Use Appropriate Callbacks

```php
// ❌ Unnecessary parameters
$array->map(fn($item, $index, $arr) => $item * 2);

// ✅ Use only what you need
$array->map(fn($item) => $item * 2);
// Slightly faster with fewer parameters
```

### 6. Batch Operations When Possible

```php
// ❌ Multiple filter passes
$result = $array
    ->filter(fn($n) => $n > 10)
    ->filter(fn($n) => $n < 100)
    ->filter(fn($n) => $n % 2 === 0);

// ✅ Single filter pass
$result = $array->filter(fn($n) => $n > 10 && $n < 100 && $n % 2 === 0);
// 3x faster
```

### 7. Use Native Array for Final Result

```php
// If you don't need JsArray methods anymore
$array = JsArray::from($data);
$result = $array->filter(...)->map(...)->toArray();

// Use native PHP array
$result = array_filter(...);
```

## Memory Usage

### Immutable Mode Memory Profile

```
10,000 items (~800KB raw data):
├── Original array: ~800KB
├── After map: ~1.6MB (2 arrays)
├── After filter: ~1.2MB (3 arrays)
└── Final result: ~400KB (4 arrays total)
```

### Mutable Mode Memory Profile

```
10,000 items (~800KB raw data):
├── Original array: ~800KB
├── After map: ~800KB (modified in place)
├── After filter: ~400KB (modified in place)
└── Final result: ~400KB (1 array)
```

## Large Dataset Examples

### Processing CSV Import

```php
// Use mutable for CSV imports > 10K rows
$import = JsArray::mutable([]);

$handle = fopen('large_file.csv', 'r');
while (($row = fgetcsv($handle)) !== false) {
    $import->push($row);
}
fclose($handle);

// Process the data
$import
    ->filter(fn($row) => !empty($row[0]))
    ->map(fn($row) => [
        'name' => $row[0],
        'email' => $row[1],
        'date' => $row[2],
    ])
    ->sort(fn($a, $b) => $a['name'] <=> $b['name']);

// Convert to immutable for safe storage
return $import->toImmutable()->toArray();
```

### Data Transformation Pipeline

```php
// Large dataset transformation
$rawData = JsArray::mutable(getDataFromAPI(100000));

$processed = $rawData
    ->filter(fn($item) => $item['status'] === 'active')
    ->map(fn($item) => [
        'id' => $item['id'],
        'normalized_name' => strtolower($item['name']),
        'score' => calculateScore($item),
    ])
    ->filter(fn($item) => $item['score'] > 50)
    ->sort(fn($a, $b) => $b['score'] <=> $a['score'])
    ->slice(0, 100);  // Top 100

return $processed->toImmutable()->toArray();
```

## Performance Tips Summary

1. **Use mutable for datasets > 10K items**
2. **Avoid recreating JsArray in loops**
3. **Convert to immutable when done processing**
4. **Combine multiple filter/map calls into single operations**
5. **Use native PHP arrays for final results when possible**
6. **Choose appropriate callback parameter count**
7. **Batch operations when possible**

---

## Related Documentation

- [PATTERNS.md](./PATTERNS.md) - Common patterns and recipes
- [EXAMPLES.md](./EXAMPLES.md) - Real-world usage examples
- [MUTABILITY.md](./MUTABILITY.md) - Deep dive into mutable/immutable modes
- [API.md](./API.md) - Complete API reference
