Proof-of-concept for a traceable wrapper to [PHPVerbalExpressions](
     https://github.com/VerbalExpressions/PHPVerbalExpressions
 )
 for eliminating the need to hand-write JSON for [verbal-expressions-tests](
     https://github.com/SignpostMarv/Verbal-Expressions-Tests
 )

# Example
Expected output, ```bool(true)```
```php
use TraceverbalExpressions\PHPTraceverbalExpressions\VerbalExpressions;
$foo = new VerbalExpressions;
$foo->startOfLine()->then('foo')->anyOf('abc')->multiple('')->endOfLine();
$bar = json_encode($foo);
$baz = new VerbalExpressions($bar);
var_dump($bar === json_encode($baz));
```
