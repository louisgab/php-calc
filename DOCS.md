# Documentation
## Instanciation
`Number::of(any)` accepts any numeric value like floats, integers and even strings or other Number instances

e.g: `Number::of("1.5")`, `Number::of(1/3)`, `Number::of(1)`, `Number::of(Number::of(12.8))` are valid and correctly handled  

On the other hand, the strings must be sane  
e.g `Number::of('1/3')`, `Number::of('1,2')`, `Number::of('1.5€')` will throw an exception

Basic instances are already built-in:
-   `Number::zero()` alias for Number::of(0)
-   `Number::one()` alias for Number::of(1)
-   `Number::ten()` alias for Number::of(10)
-   `Number::hundred()` alias for Number::of(100)
-   `Number::thousand()` alias for Number::of(1000)

## Collections
-   `Number::sum(...any)` return a Number which values to the sum of the values in the collection
-   `Number::max(...any)` return a Number which values to the max value in the collection
-   `Number::min(...any)` return a Number which values to the min value in the collection
-   `Number::average(...any)` return a Number which values to the average value of the collection

Those methods accepts a list of valid values (e.g: `Number::sum("2", 4, 1/2)`) as well as an iterable object (e.g: `Number::sum(["2", 4, 1/2])`)

## Methods
### Basic maths methods:
-   `plus(any)` returns a Number which values to the sum between the two Numbers
-   `minus(any)` returns a Number which values to the difference between the two Numbers
-   `multiply(any)` returns a Number which values to the multiplication between the two Numbers
-   `divide(any)` returns a Number which values to the division between the two Numbers
-   `power(exponent)` returns a Number which values powered to the exponent
-   `square()` alias for power(2)
-   `cube()` alias for power(3)
-   `absolute()` returns a Number which value is positive
-   `negate()` returns a Number which value will be positive if was negative, negative if positive
-   `inverse()` returns a Number which values to one over the number
-   `round(precision, mode)` returns a Number which values will rounded according to parameters, defaults to 15 digits and up
-   `round(precision)` returns a Number which decimals will be truncated according to precision without any rounding

### Comparison methods:

-   `compare(any)` returns -1 if Number is less, 1 if Number is greater and 0 if Numbers are equal, like strcmp does for strings
-   `equals(any)` returns whether the two Numbers are equal. It handles floats epsilon comparison.
-   `isDifferent(any)` returns the inverse of equals
-   `isGreater(any)` returns the strict superiority comparison result
-   `isGreaterOrEqual(any)` returns the superiority comparison result
-   `isLess(any)` returns the strict inferiority comparison result
-   `isLessOrEqual(any)` returns the inferiority comparison result

### Other useful methods:
-   `wholePart()` returns a Number which value is the left part of the floating point (e.g 3.52 is 3.0)
-   `decimalPart()` returns a Number which value is the right part of the floating point (e.g 3.52 is 0.52)
-   `isPositive()` returns whether the Number value is positive or not
-   `isNegative()` returns whether the Number value is negative or not
-   `isZero()` returns whether the Number value is equal to zero
-   `isWhole()` returns whether the Number has a decimal part
-   `apply(callback)` returns the callback result, useful for custom functions
-   `when(bool, callback)` returns the callback result if the condition is truthy
-   `format(locale, decimals, forceDecimals)` returns the display format of the number in the desired locale (not for database storage!)


## Accessors
-   `get()` returns the raw internal float value (for debug purposes)
-   `value()` returns the float value, preferred over value(), because the format will be right (no strange values like -0.0)
-   `sign()` returns `+` or `-`. N.B: Zero will be positive.

All accessors can be retrieved as properties like `->value` or `->sign`.


## Warning

While floats you be fine for most usages, please read [What Every Programmer Should Know About Floating-Point Arithmetic](https://floating-point-gui.de/), so you will be aware of its limits. Calc tries to overcome it as much as possible (see the `equals()` method implementation), but still some edge cases can occur.
If so, new tests are welcomed (please PR).
