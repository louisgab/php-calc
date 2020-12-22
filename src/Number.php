<?php

declare(strict_types=1);

namespace Louisgab\Calc;

use Exception;
use JsonSerializable;
use NumberFormatter;

final class Number implements JsonSerializable
{
    private float $value;

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }

    public function __get($name)
    {
        if (in_array($name, ['value', 'sign', 'wholePart', 'decimalPart'], true)) {
            return $this->{$name}();
        }
    }

    public static function of($value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (! is_numeric($value)) {
            throw new Exception(sprintf('Value "%s" should be castable to float or a Number instance', (string) $value));
        }

        return new self((float) $value);
    }

    // Accessors -----------------------------------------------------------------------------

    public function get(): float
    {
        return $this->value;
    }

    public function value(int $decimals = 6): float
    {
        return (float) number_format($this->value, $decimals, '.', '');
    }

    public function sign(): string
    {
        return $this->isPositive() ? '+' : '-';
    }

    public function wholePart(): int
    {
        return abs((int) $this->__toString());
    }

    public function decimalPart(): float
    {
        return abs($this->value) - $this->wholePart();
    }

    // Actions -------------------------------------------------------------------------------

    public function plus($that): self
    {
        $that = self::of($that);

        if ($that->isZero()) {
            return $this;
        }

        return self::of($this->value + $that->value);
    }

    public function minus($that): self
    {
        $that = self::of($that);

        if ($that->isZero()) {
            return $this;
        }

        return self::of($this->value - $that->value);
    }

    public function multiply($that): self
    {
        $that = self::of($that);

        if ($that->isZero()) {
            return $that;
        }

        return self::of($this->value * $that->value);
    }

    public function divide($that): self
    {
        $that = self::of($that);

        if ($that->isZero()) {
            throw new Exception('Division by zero is not possible');
        }

        return self::of($this->value / $that->value);
    }

    public function power(int $exponent): self
    {
        if ($exponent === 0) {
            return self::one();
        }

        if ($exponent === 1) {
            return $this;
        }

        return self::of($this->value ** $exponent);
    }

    public function square(): self
    {
        return $this->power(2);
    }

    public function cube(): self
    {
        return $this->power(3);
    }

    /**
     * Float equality isn't trivial.
     * @link: https://www.php.net/manual/en/language.types.float.php#language.types.float.comparison
     * @see: https://floating-point-gui.de/
     */
    public function equals($that): bool
    {
        $that = self::of($that);

        if ($this->value === $that->value) {
            return true;
        }

        $diff = abs($this->value - $that->value);

        if ($diff < PHP_FLOAT_EPSILON * 4) {
            return true;
        }

        $absA = abs($this->value);
        $absB = abs($that->value);

        if ($this->value === 0.0 || $that->value === 0.0 || ($absA + $absB < PHP_FLOAT_MIN)) {
            return $diff < (PHP_FLOAT_EPSILON * PHP_FLOAT_MIN);
        }

        return $diff / min(($absA + $absB), PHP_FLOAT_MAX) < PHP_FLOAT_EPSILON;
    }

    public function isDifferent($that): bool
    {
        return ! $this->equals($that);
    }

    public function isGreater($that): bool
    {
        return $this->compare($that) > 0;
    }

    public function isGreaterOrEqual($that): bool
    {
        return $this->compare($that) >= 0;
    }

    public function isLess($that): bool
    {
        return $this->compare($that) < 0;
    }

    public function isLessOrEqual($that): bool
    {
        return $this->compare($that) <= 0;
    }

    public function isNegative(): bool
    {
        return $this->isLess(self::zero());
    }

    public function isPositive(): bool
    {
        return ! $this->isNegative();
    }

    public function isZero(): bool
    {
        return $this->equals(self::zero());
    }

    public function isWhole(): bool
    {
        return self::of($this->decimalPart())->isZero();
    }

    public function absolute(): self
    {
        return $this->isNegative() ? $this->negate() : $this;
    }

    public function negate(): self
    {
        return $this->multiply(-1);
    }

    public function inverse(): self
    {
        return self::one()->divide($this);
    }

    public function round(int $precision = PHP_FLOAT_DIG, int $mode = PHP_ROUND_HALF_UP): self
    {
        return self::of(round($this->value, $precision, $mode));
    }

    public function truncate(int $precision): self
    {
        return self::of($this->sign() . $this->wholePart() . '.' . mb_substr((string) $this->decimalPart(), 2, $precision));
    }

    public function compare($that): int
    {
        $that = self::of($that);

        if ($this->equals($that)) {
            return 0;
        }

        return $this->value <=> $that->value;
    }

    public function apply(callable $callback): self
    {
        return self::of($callback($this));
    }

    public function when(bool $condition, callable $callback, ?callable $default = null): self
    {
        if ($condition) {
            return $this->apply($callback);
        }

        if ($default) {
            return $this->apply($default);
        }

        return $this;
    }

    public function format(string $locale = 'en_US', int $decimals = 2, bool $forceDecimals = false): string
    {
        static $formatter;

        if ($formatter === null) {
            $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        }

        if ($forceDecimals) {
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        }

        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $formatter->format($this->value);
    }

    // Config --------------------------------------------------------------------------------

    public function toInt(): int
    {
        if (! $this->isWhole()) {
            throw new Exception('This number cant be casted to int without precision loss, use round() instead');
        }

        return (int) $this->value();
    }

    public function toFloat(): float
    {
        return $this->get();
    }

    public function toArray(): array
    {
        return [
            'value'  => $this->value(),
            'sign'   => $this->sign(),
            'format' => $this->format(),
        ];
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // Helpers -------------------------------------------------------------------------------

    public static function zero(): self
    {
        static $zero;

        if ($zero === null) {
            $zero = self::of(0);
        }

        return $zero;
    }

    public static function one(): self
    {
        static $one;

        if ($one === null) {
            $one = self::of(1);
        }

        return $one;
    }

    public static function ten(): self
    {
        static $ten;

        if ($ten === null) {
            $ten = self::of(10);
        }

        return $ten;
    }

    public static function hundred(): self
    {
        static $hundred;

        if ($hundred === null) {
            $hundred = self::of(100);
        }

        return $hundred;
    }

    public static function thousand(): self
    {
        static $thousand;

        if ($thousand === null) {
            $thousand = self::of(1000);
        }

        return $thousand;
    }

    // Collections ---------------------------------------------------------------------------

    public static function max(...$collection): self
    {
        $collection = self::collection(...$collection);

        $max = null;

        foreach ($collection as $item) {
            if ($max !== null && ! $item->isGreater($max)) {
                continue;
            }

            $max = $item;
        }

        return $max;
    }

    public static function min(...$collection): self
    {
        $collection = self::collection(...$collection);

        $min = null;

        foreach ($collection as $item) {
            if ($min !== null && ! $item->isLess($min)) {
                continue;
            }

            $min = $item;
        }

        return $min;
    }

    public static function sum(...$collection): self
    {
        $collection = self::collection(...$collection);

        $sum = null;

        foreach ($collection as $item) {
            $sum = $sum === null ? $item : $sum->plus($item);
        }

        return $sum;
    }

    public static function average(...$collection): self
    {
        $collection = self::collection(...$collection);

        $sum = self::sum($collection);

        return $sum->divide(count($collection));
    }

    private static function collection($first, ...$items): array
    {
        $items = is_iterable($first) ? [...$first, ...$items] : [$first, ...$items];

        if (empty($items)) {
            throw new Exception('Collection expects at least one argument');
        }

        foreach ($items as $key => $item) {
            $items[$key] = self::of($item);
        }

        return $items;
    }
}
