<?php

declare(strict_types=1);

namespace Louisgab\Calc;

use JsonSerializable;
use NumberFormatter;

final class Percentage implements JsonSerializable
{
    private Number $value;

    private function __construct(Number $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __get($name)
    {
        if (in_array($name, ['value', 'ratio', 'sign'], true)) {
            return $this->{$name}();
        }
    }

    public static function of($value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return new self(Number::of($value));
    }

    public static function fromRatio($ratio): self
    {
        return self::of(
            Number::hundred()->multiply($ratio)
        );
    }

    public static function between($fraction, $total): self
    {
        return self::fromRatio(
            Number::of($fraction)->divide($total)
        );
    }

    public static function difference($from, $to): self
    {
        return self::fromRatio(
            Number::of($to)->minus($from)->divide($from)
        );
    }

    // Accessors -----------------------------------------------------------------------------

    public function value(): float
    {
        return $this->value->value;
    }

    public function ratio(): float
    {
        return $this->value->divide(Number::hundred())->value;
    }

    public function sign(): string
    {
        return $this->value->sign;
    }

    // Actions -------------------------------------------------------------------------------

    public function applyTo($number): Number
    {
        return Number::of($number)->multiply($this->ratio());
    }

    public function addTo($number): Number
    {
        return Number::of($number)->plus($this->applyTo($number));
    }

    public function removeFrom($number): Number
    {
        return Number::of($number)->minus($this->applyTo($number));
    }

    public function format(string $locale='en_US', int $decimals = 2, bool $forceDecimals = false): string
    {
        static $formatter;

        if ($formatter === null) {
            $formatter = new NumberFormatter($locale, NumberFormatter::PERCENT);
        }

        if ($forceDecimals) {
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        }

        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $formatter->format($this->ratio());
    }

    // Config --------------------------------------------------------------------------------

    public function toFloat(): float
    {
        return $this->value();
    }

    public function toString(): string
    {
        return (string) $this->value();
    }

    public function toArray(): array
    {
        return [
            'value'  => $this->value(),
            'ratio'  => $this->ratio(),
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

    public static function fifty(): self
    {
        static $fifty;

        if ($fifty === null) {
            $fifty = self::of(50);
        }

        return $fifty;
    }
}
