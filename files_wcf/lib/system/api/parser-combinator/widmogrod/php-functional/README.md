# PHP Functional [![Build Status](https://travis-ci.org/widmogrod/php-functional.svg)](https://travis-ci.org/widmogrod/php-functional)
## Introduction

Functional programing is a fascinating concept.
The purpose of this library is to explore `Functors`, `Applicative Functors` and `Monads` in OOP PHP, and provide examples of real world use case.

Monad types available in the project:
 * State Monad
 * IO Monad
 * Collection Monad (a.k.a List Monad, since `list` is a protected keyword in PHP I name it `collection`)
 * Either Monad
 * Maybe Monad

## Installation

```
composer require widmogrod/php-functional
```

## Development

This repository follows [semantic versioning concept](http://semver.org/). 
If you want to contribute, just follow [GitHub workflow](https://guides.github.com/introduction/flow/) and open a pull request. 

More information about changes you can find in [change log](/CHANGELOG.md)

## Testing

Quality assurance is brought to you by [PHPSpec](http://www.phpspec.net/)

```
composer test
```

## Use Cases
You can find more use cases and examples in the [example directory](/example/).

> **NOTE:** Don't be confused when browsing thought examples you will see phrase like "list functor" and in code you will see `Monad\Collection`. 
Monad is Functor and Applicative. You could say that Monad implements Functor and Applicative.

### List Functor
``` php
use Widmogrod\Functional as f;

$collection = Monad\Collection::of([
   ['id' => 1, 'name' => 'One'],
   ['id' => 2, 'name' => 'Two'],
   ['id' => 3, 'name' => 'Three'],
]);

$result = $collection->map(function($a) {
    return $a['id'] + 1;
});

assert(f\extract($result) === [2, 3, 4]);
```

### List Applicative Functor
Apply function on list of values and as a result, receive list of all possible combinations 
of applying function from the left list to a value in the right one.

``` haskell
[(+3),(+4)] <*> [1, 2] == [4, 5, 5, 6]
```

``` php
use Widmogrod\Functional as f;

$collectionA = Monad\Collection::of([
    function($a) {
        return 3 + $a;
    },
    function($a) {
        return 4 + $a;
    },
]);
$collectionB = Monad\Collection::of([
    1, 2
]);

$result = $collectionA->ap($collectionB);

assert($result instanceof Applicative\Collection);
assert(f\extract($result) === [4, 5, 5, 6]);
```

### Maybe and List Monad
Extracting from a list of uneven values can be tricky and produce nasty code full of `if (isset)` statements.
By combining List and Maybe Monad, this process becomes simpler and more readable.

``` php
use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\Collection;

$data = [
    ['id' => 1, 'meta' => ['images' => ['//first.jpg', '//second.jpg']]],
    ['id' => 2, 'meta' => ['images' => ['//third.jpg']]],
    ['id' => 3],
];

// $get :: String a -> Maybe [b] -> Maybe b
$get = function ($key) {
    return f\bind(function ($array) use ($key) {
        return isset($array[$key])
            ? Maybe\just($array[$key])
            : Maybe\nothing();
    });
};

$result = Collection::of($data)
    ->map(Maybe\maybeNull)
    ->bind($get('meta'))
    ->bind($get('images'))
    ->bind($get(0));

assert(f\valueOf($result) === ['//first.jpg', '//third.jpg', null]);
```

### Either Monad
In `php` world, the most popular way of saying that something went wrong is to throw an exception.
This results in nasty `try catch` blocks and many of if statements.
Either Monad shows how we can fail gracefully without breaking the execution chain and making the code more readable.
The following example demonstrates combining the contents of two files into one. If one of those files does not exist the operation fails gracefully.

``` php
use Widmogrod\Functional as f;
use Widmogrod\Monad\Either;

function read($file)
{
    return is_file($file)
        ? Either\Right::of(file_get_contents($file))
        : Either\Left::of(sprintf('File "%s" does not exists', $file));
}

$concat = f\liftM2(
    read(__DIR__ . '/e1.php'),
    read('aaa'),
    function ($first, $second) {
        return $first . $second;
    }
);

assert($concat instanceof Either\Left);
assert($concat->extract() === 'File "aaa" does not exists');
```

### IO Monad
Example usage of `IO Monad`. Read input from `stdin`, and print it to `stdout`.

``` php
use Widmogrod\Monad\IO as IO;
use Widmogrod\Functional as f;

// $readFromInput :: Monad a -> IO ()
$readFromInput = f\mcompose(IO\putStrLn, IO\getLine, IO\putStrLn);
$readFromInput(Monad\Identity::of('Enter something and press <enter>'))->run();
```

### Haskell Do Notation in PHP
``` php
use Widmogrod\Monad\IO as IO;
use Widmogrod\Monad\Control as C;
use Widmogrod\Functional as f;

$do = control\doo([
    IO\putStrLn('Your name:'),                      // put on screen line: Your name:
    '$name' =>
        IO\getLine(),                               // prompt for the name, and store it in '$name' key

    control\runWith(IO\putStrLn, ['$name']),        // put on screen entered name

    IO\putStrLn('Your surname:'),                   // put on screen line: Your surname:
    '$surname' =>
        IO\getLine(),                               // prompt for surname, and store it in '$surname' key

    control\runWith(function($surname, $name) {     // put on screen: "Hello $surname, $name"
        return IO\putStrLn(sprintf("Hello %s, %s", $surname, $name));
    }, ['$surname', '$name']),
]);

// performs operation, before that nothings happens from above code.
$do->run(); 
```

Example output:
```txt
Your name:
Gabriel
Your surname:
Habryn
Hello Habryn, Gabriel
```

### Sequencing Monad operations
This variant of `sequence_` ignores the result.

``` php
use Widmogrod\Monad\IO as IO;
use Widmogrod\Functional as f;

f\sequence_([
    IO\putStrLn('Your name:'),
    IO\getLine(),
    IO\putStrLn('Your surname:'),
    IO\getLine(),
    IO\putStrLn('Thank you'),
])->run();
```

## References
Here links to their articles`/`libraries that help me understood the domain:
 * http://drboolean.gitbooks.io/mostly-adequate-guide
 * https://github.com/fantasyland/fantasy-land
 * http://adit.io/posts/2013-04-17-functors,_applicatives,_and_monads_in_pictures.html
 * http://learnyouahaskell.com/functors-applicative-functors-and-monoids
 * http://learnyouahaskell.com/starting-out#im-a-list-comprehension
 * http://robotlolita.me/2013/12/08/a-monad-in-practicality-first-class-failures.html
 * http://robotlolita.me/2014/03/20/a-monad-in-practicality-controlling-time.html
 * https://github.com/folktale/data.either
