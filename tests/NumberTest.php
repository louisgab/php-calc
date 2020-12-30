<?php

declare(strict_types=1);

namespace Louisgab\Calc\Tests;

use Louisgab\Calc\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    /**
     * @dataProvider valueProvider
     */
    public function testValueIsCastedToFloat($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->get());
    }

    public function valueProvider(): array
    {
        return [
            'int' => [1, 1.0],
            'int string' => ['1', 1.0],
            'int negative' => [-1, -1.0],
            'int negative string' => ['-1', -1.0],
            'float' => [1.1, 1.1],
            'float string' => ['1.1', 1.1],
            'float negative'  => [-1.1, -1.1],
            'float negative string'  => ['-1.1', -1.1],
            'fraction'  => [1/2, 0.5],
            'fraction negative'  => [-1/2, -0.5],
        ];
    }

    /**
     * @dataProvider signProvider
     */
    public function testSignIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->sign);
    }

    public function signProvider(): array
    {
        return [
            'positive one' => [1, '+'],
            'negative one' => [-1, '-'],
            'positive zero' => [0, '+'],
            'negative zero' => [-0, '+'],
            'positive 1/3' => [1/3, '+'],
            'negative 1/3' => [-1/3, '-'],
        ];
    }

    /**
     * @dataProvider wholePartProvider
     */
    public function testWholePartIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->wholePart()->get());
    }

    public function wholePartProvider(): array
    {
        return [
            'zero' => [0, 0.0],
            'five quarter' => [5/4, 1.0],
            'minus one half' => [-1/2, 0.0],
        ];
    }

    /**
     * @dataProvider decimalPartProvider
     */
    public function testDecimalPartIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->decimalPart()->get());
    }

    public function decimalPartProvider(): array
    {
        return [
            'zero' => [0, 0.0],
            'five quarter' => [5/4, 0.25],
            'minus one half' => [-1/2, 0.5],
        ];
    }

    /**
     * @dataProvider plusProvider
     */
    public function testPlusIsRight($a, $b, $expected): void
    {
        $this->assertSame($expected, Number::of($a)->plus($b)->get());
        $this->assertTrue(Number::of($a)->plus($b)->equals($expected));
    }

    public function plusProvider(): array
    {
        return [
            'zeros' => [0, 0, 0.0],
            '0.3 problem' => [0.1, 0.2, 0.3],
            'inverse' => [3, -3, 0.0],
        ];
    }

    /**
     * @dataProvider minusProvider
     */
    public function testMinusIsRight($a, $b, $expected): void
    {
        $this->assertSame($expected, Number::of($a)->minus($b)->get());
        $this->assertTrue(Number::of($a)->minus($b)->equals($expected));
    }

    public function minusProvider(): array
    {
        return [
            'zeros' => [0, 0, 0.0],
            '1.6 problem' => [8, 6.4, 1.6],
            'inverse' => [3, -3, 6.0],
        ];
    }

    /**
     * @dataProvider multiplyProvider
     */
    public function testMultiplyIsRight($a, $b, $expected): void
    {
        $this->assertSame($expected, Number::of($a)->multiply($b)->get());
        $this->assertTrue(Number::of($a)->multiply($b)->equals($expected));
    }

    public function multiplyProvider(): array
    {
        return [
            'zeros' => [0, 0, 0.0],
            '410 problem' => [4.1, 100, 410.0],
            'inverse' => [3, 1/3, 1.0],
        ];
    }

    /**
     * @dataProvider divideProvider
     */
    public function testdivideIsRight($a, $b, $expected): void
    {
        $this->assertSame($expected, Number::of($a)->divide($b)->get());
        $this->assertTrue(Number::of($a)->divide($b)->equals($expected));
    }

    public function divideProvider(): array
    {
        return [
            '0.041 problem' => [4.1, 100, 0.041],
            'inverse' => [3, 3, 1.0],
        ];
    }

    /**
     * @dataProvider equalsProvider
     */
    public function testEqualsIsRight($a, $b): void
    {
        $this->assertTrue(Number::of($a)->equals($b));
    }

    public function equalsProvider(): array
    {
        return [
            'one third' => [1/3, (2/3) - (1/3)],
            '0.3 problem' => [0.1 + 0.2, 0.3],
            '0.3 problem variant' => [0.3 - (0.1 + 0.2), 0],
            'zero problem' => [(0.6/0.2) -3, 0],
        ];
    }

    /**
     * @dataProvider isDifferentProvider
     */
    public function testIsDifferentIsRight($a, $b): void
    {
        $this->assertTrue(Number::of($a)->isDifferent($b));
    }

    public function isDifferentProvider(): array
    {
        return [
            'negative one third' => [1/3,  -1/3],
            'round one third' => [1/3,  0.33],
            'big diff' => [1000000.0, 1000000.1],
            'negative big numbers' => [-1000000.0, -1000000.1],
            'small diff' => [0.1, 0.1000000001],
            'negative small diff' => [-0.1, -0.1000000001],
        ];
    }

    /**
     * @dataProvider isGreaterProvider
     */
    public function testIsGreaterIsRight($a, $b): void
    {
        $this->assertTrue(Number::of($a)->isGreater($b));
    }

    public function isGreaterProvider(): array
    {
        return [
            'zero' => [1/3,  0],
            'negative one third' => [1/3,  -1/3],
            'round one third' => [1/3,  0.33],
        ];
    }

    /**
     * @dataProvider isLessProvider
     */
    public function testIsLessIsRight($a, $b): void
    {
        $this->assertTrue(Number::of($a)->isLess($b));
    }

    public function isLessProvider(): array
    {
        return [
            'zero' => [0, 0.1],
            'negative one third' => [-1/3,  1/3],
            'round one third' => [0.33333333, 1/3],
        ];
    }

    /**
     * @dataProvider isInRangeProvider
     */
    public function testisInRangeIsRight($value, $low, $high): void
    {
        $this->assertTrue(Number::of($value)->isInRange($low, $high));
    }

    public function isInRangeProvider(): array
    {
        return [
            'zero' => [0, -0.001, 0.001],
            'small' => [0.001, 0.0009, 0.0011],
        ];
    }

    /**
     * @dataProvider isNegativeProvider
     */
    public function testIsNegativeIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->isNegative());
    }

    public function isNegativeProvider(): array
    {
        return [
            'negative number' => [-5, true],
            'positive number' => [5, false],
        ];
    }

    /**
     * @dataProvider isPositiveProvider
     */
    public function testIsPositiveIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->isPositive());
    }

    public function isPositiveProvider(): array
    {
        return [
            'positive number' => [5, true],
            'negative number' => [-5, false],
        ];
    }

    /**
     * @dataProvider isZeroProvider
     */
    public function testIsZeroIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->isZero());
    }

    public function isZeroProvider(): array
    {
        return [
            'int' => [0, true],
            'float' => [0.0, true],
            'string' => ['0', true],
            'calc' => [(-0.6/0.2)+3, true],
            'negative int' => [-0, true],
            'negative float' => [-0.0, true],
            'negative string' => ['-0', true],
            'negative calc' => [(0.6/0.2)-3, true],
        ];
    }

    /**
     * @dataProvider isWholeProvider
     */
    public function testIsWholeIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->isWhole());
    }

    public function isWholeProvider(): array
    {
        return [
            'whole float number' => [1.0, true],
            'whole int number' => [1, true],
            'whole negative number' => [-1, true],
            'decimal number' => [1.1, false],
        ];
    }

    /**
     * @dataProvider powerProvider
     */
    public function testPowerIsRight($a, $b, $expected): void
    {
        $this->assertSame($expected, Number::of($a)->power($b)->get());
    }

    public function powerProvider(): array
    {
        return [
            'zero' => [1234565.987, 0, 1.0],
            'one' => [1234565.987, 1, 1234565.987],
        ];
    }

    /**
     * @dataProvider squareProvider
     */
    public function testSquareIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->square()->get());
    }

    public function squareProvider(): array
    {
        return [
            'three' => [3.0, 9.0],
            'one half' => [1/2, 1/4],
        ];
    }

    /**
     * @dataProvider absoluteProvider
     */
    public function testAbsoluteIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->absolute()->get());
        $this->assertTrue(Number::of($value)->absolute()->equals($expected));
    }

    public function absoluteProvider(): array
    {
        return [
            'zeros' => [0, 0.0],
            'plus one' => [1, 1.0],
            'minus one' => [-1, 1.0],
        ];
    }

    /**
     * @dataProvider negateProvider
     */
    public function testNegateIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->negate()->get());
        $this->assertTrue(Number::of($value)->negate()->equals($expected));
    }

    public function negateProvider(): array
    {
        return [
            'zeros' => [0, 0.0],
            'plus one' => [1, -1.0],
            'minus one' => [-1, 1.0],
        ];
    }

    /**
     * @dataProvider inverseProvider
     */
    public function testInverseIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->inverse()->get());
        $this->assertTrue(Number::of($value)->inverse()->equals($expected));
    }

    public function inverseProvider(): array
    {
        return [
            'plus one' => [1, 1.0],
            'minus one' => [-1, -1.0],
            'one quarter' => [1/4, 4.0],
            'two third' => [2/3, 3/2],
        ];
    }

    /**
     * @dataProvider roundProvider
     */
    public function testRoundIsRight($value, $expected, $expectedtwo): void
    {
        $this->assertSame($expected, Number::of($value)->round()->get());
        $this->assertTrue(Number::of($value)->round()->equals($expected));

        $this->assertSame($expectedtwo, Number::of($value)->round(2)->get());
        $this->assertTrue(Number::of($value)->round(2)->equals($expectedtwo));
    }

    public function roundProvider(): array
    {
        return [
            'zeros' => [0, 0.0, 0.0],
            'minus one' => [-1, -1.0, -1.0],
            'one quarter' => [1/4, 0.25, 0.25],
            'one third' => [1/3, 0.333333333333333, 0.33],
            'two thirds' => [2/3, 0.666666666666667, 0.67],
        ];
    }

    /**
     * @dataProvider truncateProvider
     */
    public function testTruncateIsRight($value, $expected, $expectedtwo): void
    {
        $this->assertSame($expected, Number::of($value)->truncate(0)->get());
        $this->assertTrue(Number::of($value)->truncate(0)->equals($expected));

        $this->assertSame($expectedtwo, Number::of($value)->truncate(2)->get());
        $this->assertTrue(Number::of($value)->truncate(2)->equals($expectedtwo));
    }

    public function truncateProvider(): array
    {
        return [
            'zeros' => [0, 0.0, 0.0],
            'minus one' => [-1, -1.0, -1.0],
            'one quarter' => [1/4, 0.0, 0.25],
            'one third' => [1/3, 0.0, 0.33],
            'two thirds' => [2/3, 0.0, 0.66],
        ];
    }

    /**
     * @dataProvider applyProvider
     */
    public function testApplyIsRight($value, $callback, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->apply($callback)->get());
    }

    public function applyProvider(): array
    {
        return [
            'internal' => [3, fn ($number) => $number->square(), 9.0],
            'custom' => [1/3, fn ($number) => ceil($number->value()), ceil(1/3)],
        ];
    }

    /**
     * @dataProvider whenProvider
     */
    public function testWhenIsRight($value, $condition, $callback, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->when($condition, $callback)->get());
    }

    public function whenProvider(): array
    {
        return [
            'true' => [3, true, fn () => Number::one(), 1.0],
            'false' => [3, false, fn () => Number::one(), 3.0],
        ];
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormatIsRight($value, $expected): void
    {
        $this->assertSame($expected, Number::of($value)->format());
    }

    public function formatProvider(): array
    {
        return [
            'one half' => [1/2, '0.5'],
        ];
    }

    /**
     * @dataProvider maxProvider
     */
    public function testMaxIsRight($values, $expected): void
    {
        $this->assertSame($expected, Number::max($values)->get());
    }

    public function maxProvider(): array
    {
        return [
            'example' => [[-1, 1/2, 0.0, '0.75'], 0.75],
        ];
    }

    /**
     * @dataProvider minProvider
     */
    public function testMinIsRight($values, $expected): void
    {
        $this->assertSame($expected, Number::min($values)->get());
    }

    public function minProvider(): array
    {
        return [
            'example' => [[-1, 1/2, 0.0, '0.75'], -1.0],
        ];
    }

    /**
     * @dataProvider sumProvider
     */
    public function testSumIsRight($values, $expected): void
    {
        $this->assertSame($expected, Number::sum($values)->get());
    }

    public function sumProvider(): array
    {
        return [
            'example' => [[-1, 1/2, 0.0, '0.75'], 1/4],
        ];
    }

    /**
     * @dataProvider averageProvider
     */
    public function testAverageIsRight($values, $expected): void
    {
        $this->assertSame($expected, Number::average($values)->get());
    }

    public function averageProvider(): array
    {
        return [
            'example' => [[-1, 1/2, 0.0, '0.75'], 1/16],
        ];
    }
}
