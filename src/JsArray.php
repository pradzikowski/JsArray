<?php

namespace JsArray;

class JsArray
{
    private array $items;
    public int $length;

    public function __construct(array $items = [])
    {
        $this->items = $items;
        $this->length = count($items);
    }

    public static function from(array $items): self
    {
        return new self($items);
    }

    public static function of(...$items): self
    {
        return new self($items);
    }

    public function map(callable $callback): self
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            $result[$key] = $callback($value, $key, $this);
        }
        return new self($result);
    }

    public function filter(callable $callback): self
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key, $this)) {
                $result[$key] = $value;
            }
        }

        return new self($result);
    }

    public function reduce(callable $callback, $initial = null)
    {
        $accumulator = $initial;
        $first = true;

        foreach ($this->items as $key => $value) {
            if ($first && $initial === null) {
                $accumulator = $value;
                $first = false;
                continue;
            }
            $accumulator = $callback($accumulator, $value, $key, $this);
            $first = false;
        }

        return $accumulator;
    }

    public function flat(): self
    {
        $result = [];
        foreach ($this->items as $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $result[] = $item;
                }
            } else {
                $result[] = $value;
            }
        }
        return new self($result);
    }

    public function flatMap(callable $callback): self
    {
        return $this->map($callback)->flat();
    }

    public function concat(self ...$arrays): self
    {
        $result = $this->items;

        foreach ($arrays as $array) {
            foreach ($array->items as $value) {
                $result[] = $value;
            }
        }

        return new self($result);
    }

    public function find(callable $callback)
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key, $this)) {
                return $value;
            }
        }
        return null;
    }

    public function findIndex(callable $callback)
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key, $this)) {
                return $this->isNumericArray($this->items) ? array_search($key, array_keys($this->items)) : $key;
            }
        }
        return $this->isNumericArray($this->items) ? -1 : null;
    }

    private function isNumericArray(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    public function includes($value): bool
    {
        foreach ($this->items as $item) {
            if ($item === $value) {
                return true;
            }
        }
        return false;
    }

    public function some(callable $callback): bool
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key, $this)) {
                return true;
            }
        }
        return false;
    }

    public function every(callable $callback): bool
    {
        foreach ($this->items as $key => $value) {
            if (!$callback($value, $key, $this)) {
                return false;
            }
        }
        return true;
    }

    public function push(...$values): self
    {
        $result = $this->items;
        foreach ($values as $value) {
            $result[] = $value;
        }
        return new self($result);
    }

    public function pop()
    {
        $result = $this->items;
        $value = array_pop($result);
        return [
            'array' => new self($result),
            'value' => $value
        ];
    }

    public function keys(): self
    {
        return new self(array_keys($this->items));
    }

    public function values(): self
    {
        return new self(array_values($this->items));
    }

    public function first()
    {
        if (empty($this->items)) {
            return null;
        }
        return reset($this->items);
    }

    public function at(int $index)
    {
        if (empty($this->items)) {
            return null;
        }

        $normalizedIndex = $index < 0 ? $this->length + $index : $index;
        if ($normalizedIndex < 0 || $normalizedIndex >= $this->length) {
            return null;
        }

        $itemsArray = array_values($this->items);
        return $itemsArray[$normalizedIndex];
    }

    public function last()
    {
        if (empty($this->items)) {
            return null;
        }
        return end($this->items);
    }

    public function join(string $separator = ','): string
    {
        return implode($separator, $this->items);
    }

    public function forEach(callable $callback): void
    {
        foreach ($this->items as $key => $value) {
            $callback($value, $key, $this);
        }
    }

    public function slice(int $start, ?int $end = null): self
    {
        $length = count($this->items);
        $start = $this->normalizeIndex($start, $length);
        $end = $end === null ? $length : $this->normalizeIndex($end, $length);

        $result = [];
        $index = 0;
        foreach ($this->items as $key => $value) {
            if ($index >= $start && $index < $end) {
                $result[$key] = $value;
            }
            $index++;
        }

        if ($this->isNumericArray($this->items)) {
            $result = array_values($result);
        }

        return new self($result);
    }

    public function splice(int $start, ?int $deleteCount = null, array $items = []): array
    {
        $length = count($this->items);
        $start = $this->normalizeIndex($start, $length);

        if ($deleteCount === null) {
            $deleteCount = $length - $start;
        } else {
            $deleteCount = max(0, min($deleteCount, $length - $start));
        }

        $deletedItems = array_slice($this->items, $start, $deleteCount, true);
        $before = array_slice($this->items, 0, $start, true);
        $after = array_slice($this->items, $start + $deleteCount, null, true);

        $result = array_merge($before, $items, $after);

        if ($this->isNumericArray($this->items)) {
            $result = array_values($result);
        }

        return [
            'deleted' => new self($deletedItems),
            'array' => new self($result)
        ];
    }

    public function toArray(): array
    {
        return $this->items;
    }

    private function normalizeIndex(int $index, int $length): int
    {
        if ($index < 0) {
            $index = $length + $index;
        }
        return max(0, min($index, $length));
    }
}
