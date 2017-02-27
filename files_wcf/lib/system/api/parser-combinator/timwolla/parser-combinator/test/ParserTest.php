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

namespace Bastelstube\ParserCombinator\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Bastelstube\ParserCombinator;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $parser = ParserCombinator\Parser::of('a');
        $input = new ParserCombinator\Input('x');

        $parser->map('strtoupper')($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('A', $result->getResult());
        });
    }

    public function testOf()
    {
        $parser = ParserCombinator\Parser::of('a');
        $input = new ParserCombinator\Input('x');

        $parser($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('a', $result->getResult());
            $this->assertSame('x', $result->getRest()->getString());
        });
    }

    public function testAp()
    {
        $a = ParserCombinator\Parser::of('a');
        $b = ParserCombinator\Parser::of('b');
        $parser = ($a->map(function ($a) {
            return \Widmogrod\Functional\curry(function ($a, $b) {
                return $a.','.$b;
            }, [$a]);
        }))->ap($b);
        $input = new ParserCombinator\Input('x');

        $parser($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('a,b', $result->getResult());
            $this->assertSame('x', $result->getRest()->getString());
        });
    }

    public function testApL()
    {
        $a = new ParserCombinator\Parser\Byte('a');
        $b = new ParserCombinator\Parser\Byte('b');
        $parser = $a->apL($b);
        $input = new ParserCombinator\Input('ab');

        $parser($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('a', $result->getResult());
            $this->assertSame('', $result->getRest()->getString());
        });
    }

    public function testApR()
    {
        $a = new ParserCombinator\Parser\Byte('a');
        $b = new ParserCombinator\Parser\Byte('b');
        $parser = $a->apR($b);
        $input = new ParserCombinator\Input('ab');

        $parser($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('b', $result->getResult());
            $this->assertSame('', $result->getRest()->getString());
        });
    }

    public function testBind()
    {
        $a = ParserCombinator\Parser::of('a');
        $parser = $a->bind(function ($r) {
            return ParserCombinator\Parser::of('b');
        });
        $input = new ParserCombinator\Input('x');

        $parser($input)->either(function ($message) {
            $this->fail($message);
        }, function ($result) {
            $this->assertSame('b', $result->getResult());
            $this->assertSame('x', $result->getRest()->getString());
        });
    }
}