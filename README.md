# Calc

[![Packagist Version](https://img.shields.io/packagist/v/louisgab/php-calc.svg?style=flat-square)](https://packagist.org/packages/louisgab/php-calc)
[![Packagist Downloads](https://img.shields.io/packagist/dt/louisgab/php-calc.svg?style=flat-square)](https://packagist.org/packages/louisgab/php-calc)
[![GitHub license](https://img.shields.io/github/license/louisgab/php-calc.svg?style=flat-square)](https://github.com/louisgab/php-calc/blob/master/LICENSE)

💯 Simple fluent float manipulation library.

Calc aims to provide tools for easy and readable calculations, without any dependency.  
It comes with `Number` which is an immutable value object that enables fluent float manipulations.  
  
## Why

If you ever worked with a codebase full of that kind of crap:

```php
$result = round(($b != 0 ? ((1+$a)/$b) : $c)*0.25, 2)
```

I'm sure you will enjoy that:

```php
$result = Number::of($b)
    ->when(Number::of($b)->isZero(),
        fn($b) => $c
        fn($b) => Number::one()->plus($a)->divide($b),
    )
    ->multiply(0.25)
    ->round(2)
    ->value()
```

You may think it's like [brick/math](https://github.com/brick/math), which is a really great package, but Calc serves a different purpose.  
If floats are good enough for you - and unless you're dealing with sensible data like accounting or science, it should be - then using GMP or bcmath is overkill. 
  
That's what Calc is made for, still using floats while enjoying nice readability.
Another good point is that it handles floating point problems (e.g `0.1 + 0.2 == 0.3 // false`) as much as possible, so you don't have to think about it each time (and if you are working with junior developers, it will save them from having problems they didn't even know existed!).

## Install

Via composer:

```bash
composer require louisgab/php-calc
```

## Usage
Simple as:
```php
use Louisgab\Calc\Number;

Number::of($anything);
```

And good as :
```php
public function carsNeeded(Number $people, Number $placesPerCar): int
{
    return $people->divide($placesPerCar)->round(0)->toInt();
}
```

Please see [DOCS](DOCS.md)

## Testing

```bash
composer test
```

## Roadmap

- [x] `Number`
- [ ] `Fraction`
- [ ] `Percentage` 

## Changelog

Please see [CHANGELOG](CHANGELOG.md) 

## Contributing

Highly welcomed!

## License

Please see [The MIT License (MIT)](LICENSE.md).
