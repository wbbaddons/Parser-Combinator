<?php declare(strict_types=1);
/*
The MIT License (MIT)

Copyright (c) 2017 Tim Düsterhus

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Bastelstube\ParserCombinator;

use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Apply;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\FantasyLand\Monad;
use Widmogrod\Monad\Either;

abstract class Parser implements
    Functor,
    Applicative,
    Monad
{
    abstract public function run(Input $input) : Either\Either;

    public function __invoke(Input $input) : Either\Either
    {
        return $this->run($input);
    }

    public function map(callable $function)
    {
        return Parser::of($function)->ap($this);

        // Implementation without using ap.
        return new class($this, $function) extends Parser {
            private $parent;
            private $function;

            public function __construct(Parser $parent, callable $function)
            {
                $this->parent = $parent;
                $this->function = $function;
            }

            public function run(Input $input) : Either\Either
            {
                $result = $this->parent->run($input);
                return $result->map(function (Result $r) : Result {
                    return $r->map($this->function);
                });
            }
        };
    }

    public static function of($value)
    {
        return new class($value) extends Parser {
            private $value;

            public function __construct($value) {
                $this->value = $value;
            }

            public function run(Input $input) : Either\Either
            {
                return new Either\Right(new Result($this->value, $input));
            }
        };
    }

    public function ap(Apply $b)
    {
        return $this->bind(function ($f) use ($b) {
            return $b->bind(function ($x) use ($f) {
                return Parser::of($f($x));
            });
        });

        // Implementation without using bind.
        return new class($this, $b) extends Parser {
            private $a;
            private $b;

            public function __construct(Parser $a, Parser $b) {
                $this->a = $a;
                $this->b = $b;
            }

            public function run(Input $input) : Either\Either
            {
                return $this->a->run($input)
                ->bind(function (Result $resultA) : Either\Either {
                    return $this->b->run($resultA->getRest())
                    ->bind(function (Result $resultB) use ($resultA) : Either\Either {
                        return new Either\Right(new Result($resultA->getResult()($resultB->getResult()), $resultB->getRest()));
                    });
                });
            }
        };
    }

    public function apL(Apply $b)
    {
        return \Widmogrod\Functional\liftA2(function ($a) {
            return $a;
        }, $this, $b);
    }

    public function apR(Apply $b)
    {
        return \Widmogrod\Functional\liftA2(function ($_, $b) {
            return $b;
        }, $this, $b);
    }

    public function bind(callable $function)
    {
        return new class($this, $function) extends Parser {
            private $parent;
            private $function;

            public function __construct(Parser $parent, callable $function)
            {
                $this->parent = $parent;
                $this->function = $function;
            }

            public function run(Input $input) : Either\Either
            {
                return $this->parent->run($input)
                ->bind(function (Result $result) : Either\Either {
                    $function = $this->function;
                    return $function($result->getResult())->run($result->getRest());
                });
            }

            public function __toString()
            {
                return $this->parent.' >>=';
            }
        };
    }

    public function __toString()
    {
        return get_called_class().'(???)';
    }
}
