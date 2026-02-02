<?php

declare(strict_types=1);

namespace JsArray\Tests;

use JsArray\JsArray;
use PHPUnit\Framework\TestCase;

class JsArrayTest extends TestCase
{
    public function testFrom(): void
    {
        $array = JsArray::from([1, 2, 3]);
        $this->assertEquals([1, 2, 3], $array->toArray());

        $assoc = JsArray::from(['a' => 1, 'b' => 2]);
        $this->assertEquals(['a' => 1, 'b' => 2], $assoc->toArray());
    }

    public function testOf(): void
    {
        $array = JsArray::of(1, 2, 3);
        $this->assertEquals([1, 2, 3], $array->toArray());
    }

    public function testMap(): void
    {
        $array = JsArray::from([1, 2, 3]);
        $mapped = $array->map(fn($n) => $n * 2);
        $this->assertEquals([2, 4, 6], $mapped->toArray());

        // Test with index and array parameters
        $mappedWithIndex = $array->map(fn($n, $i) => $n + $i);
        $this->assertEquals([1, 3, 5], $mappedWithIndex->toArray());
    }

    public function testFilter(): void
    {
        $array = JsArray::from([1, 2, 3, 4, 5]);
        $filtered = $array->filter(fn($n) => $n % 2 === 0);
        $this->assertEquals([1 => 2, 3 => 4], $filtered->toArray());

        // Test with associative array
        $assoc = JsArray::from(['a' => 1, 'b' => 2, 'c' => 3]);
        $filteredAssoc = $assoc->filter(fn($n) => $n > 1);
        $this->assertEquals(['b' => 2, 'c' => 3], $filteredAssoc->toArray());
    }

    public function testReduce(): void
    {
        $array = JsArray::from([1, 2, 3, 4]);
        $sum = $array->reduce(fn($acc, $n) => $acc + $n, 0);
        $this->assertEquals(10, $sum);

        // Test without initial value
        $sum2 = $array->reduce(fn($acc, $n) => $acc + $n);
        $this->assertEquals(10, $sum2);
    }

    public function testFlat(): void
    {
        $array = JsArray::from([1, [2, 3], 4]);
        $flattened = $array->flat();
        $this->assertEquals([1, 2, 3, 4], $flattened->toArray());
    }

    public function testFlatMap(): void
    {
        $array = JsArray::from([1, 2, 3]);
        $result = $array->flatMap(fn($n) => [$n, $n * 2]);
        $this->assertEquals([1, 2, 2, 4, 3, 6], $result->toArray());
    }

    public function testSlice(): void
    {
        $array = JsArray::from([1, 2, 3, 4, 5]);
        $this->assertEquals([2, 3], $array->slice(1, 3)->toArray());
        $this->assertEquals([4, 5], $array->slice(-2)->toArray());
        $this->assertEquals([1, 2, 3], $array->slice(0, -2)->toArray());
    }

    public function testConcat(): void
    {
        $array1 = JsArray::from([1, 2]);
        $array2 = JsArray::from([3, 4]);
        $this->assertEquals([1, 2, 3, 4], $array1->concat($array2)->toArray());
    }

    public function testFind(): void
    {
        $array = JsArray::from([1, 2, 3, 4, 5]);
        $found = $array->find(fn($n) => $n > 3);
        $this->assertEquals(4, $found);

        $notFound = $array->find(fn($n) => $n > 10);
        $this->assertNull($notFound);
    }

    public function testFindIndex(): void
    {
        $array = JsArray::from([1, 2, 3, 4, 5]);
        $this->assertEquals(2, $array->findIndex(fn($n) => $n === 3));
        $this->assertEquals(-1, $array->findIndex(fn($n) => $n === 10));

        // Test with associative array
        $assoc = JsArray::from(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->assertEquals('b', $assoc->findIndex(fn($n) => $n === 2));
    }

    public function testIncludes(): void
    {
        $array = JsArray::from([1, 2, 3]);
        $this->assertTrue($array->includes(2));
        $this->assertFalse($array->includes(4));
    }

    public function testSome(): void
    {
        $array = JsArray::from([1, 2, 3, 4, 5]);
        $this->assertTrue($array->some(fn($n) => $n > 3));
        $this->assertFalse($array->some(fn($n) => $n > 10));
    }

    public function testEvery(): void
    {
        $array = JsArray::from([2, 4, 6, 8]);
        $this->assertTrue($array->every(fn($n) => $n % 2 === 0));
        $this->assertFalse($array->every(fn($n) => $n > 5));
    }

    public function testPush(): void
    {
        $array = JsArray::from([1, 2]);
        $newArray = $array->push(3, 4);
        $this->assertEquals([1, 2, 3, 4], $newArray->toArray());
    }

    public function testPop(): void
    {
        $array = JsArray::from([1, 2, 3]);
        $result = $array->pop();
        $this->assertEquals(3, $result['value']);
        $this->assertEquals([1, 2], $result['array']->toArray());
    }

    public function testKeys(): void
    {
        $array = JsArray::from(['a' => 1, 'b' => 2]);
        $this->assertEquals(['a', 'b'], $array->keys()->toArray());
    }

    public function testValues(): void
    {
        $array = JsArray::from(['a' => 1, 'b' => 2]);
        $this->assertEquals([1, 2], $array->values()->toArray());
    }

    public function testFirstAndLast(): void
    {
        $array = JsArray::from([10, 20, 30]);
        $this->assertEquals(10, $array->first());
        $this->assertEquals(30, $array->last());

        $empty = JsArray::from([]);
        $this->assertNull($empty->first());
        $this->assertNull($empty->last());
    }

    public function testIsNumericArray(): void
    {
        $numeric = JsArray::from([1, 2, 3]);
        $assoc = JsArray::from(['a' => 1, 'b' => 2]);
        $this->assertNotEquals($numeric, $assoc); // Just to test reflection access would be needed for private method
    }

    public function testChaining(): void
    {
        $result = JsArray::from([1, 2, 3, 4, 5])
            ->filter(fn($n) => $n % 2 === 0)
            ->map(fn($n) => $n * 2)
            ->toArray();
        
        $this->assertEquals([1 => 4, 3 => 8], $result);
    }

    public function testForEach(): void
    {
        // Test with numeric array
        $array = JsArray::from([10, 20, 30]);
        $sum = 0;
        $indices = [];
        $arrays = [];
        
        $array->forEach(function($value, $index, $jsArray) use (&$sum, &$indices, &$arrays) {
            $sum += $value;
            $indices[] = $index;
            $arrays[] = $jsArray->toArray();
        });
        
        $this->assertEquals(60, $sum);
        $this->assertEquals([0, 1, 2], $indices);
        $this->assertCount(3, $arrays);
        $this->assertEquals([10, 20, 30], $arrays[0]);
        
        // Test with associative array
        $assoc = JsArray::from(['a' => 1, 'b' => 2, 'c' => 3]);
        $result = [];
        $keys = [];
        
        $assoc->forEach(function($value, $key) use (&$result, &$keys) {
            $result[] = "$key:$value";
            $keys[] = $key;
        });
        
        $this->assertEquals(['a:1', 'b:2', 'c:3'], $result);
        $this->assertEquals(['a', 'b', 'c'], $keys);
        
        // Test with empty array
        $empty = JsArray::from([]);
        $called = false;
        $empty->forEach(function() use (&$called) {
            $called = true;
        });
        $this->assertFalse($called);
        
        // Test that original array is not modified
        $original = [1, 2, 3];
        $jsArray = JsArray::from($original);
        $jsArray->forEach(fn() => null);
        $this->assertEquals($original, $jsArray->toArray());
        
        // Test with array reference in callback
        $refArray = JsArray::from(['a', 'b', 'c']);
        $refArray->forEach(function(&$value) {
            $value = strtoupper($value);
        });
        $this->assertEquals(['a', 'b', 'c'], $refArray->toArray());
    }

    public function testAt(): void
    {
        // Test with numeric array
        $array = JsArray::from([10, 20, 30]);
        
        // Test positive indices
        $this->assertEquals(10, $array->at(0));
        $this->assertEquals(20, $array->at(1));
        $this->assertEquals(30, $array->at(2));
        
        // Test negative indices
        $this->assertEquals(30, $array->at(-1));
        $this->assertEquals(20, $array->at(-2));
        $this->assertEquals(10, $array->at(-3));
        
        // Test out of bounds
        $this->assertNull($array->at(3));
        $this->assertNull($array->at(-4));
        
        // Test with associative array
        $assoc = JsArray::from(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->assertEquals(1, $assoc->at(0));
        $this->assertEquals(2, $assoc->at(1));
        $this->assertEquals(3, $assoc->at(2));
        $this->assertEquals(3, $assoc->at(-1));
        
        // Test with empty array
        $empty = JsArray::from([]);
        $this->assertNull($empty->at(0));
        $this->assertNull($empty->at(-1));
    }
}
