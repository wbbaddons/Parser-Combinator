<?php
namespace test\Monad;

use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\Helpful\ApplicativeLaws;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Monad\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use Widmogrod\Helpful\MonadLaws;

class EitherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            $return,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        return [
            'Right' => [
                '$return' => Right::of,
                '$f' => function ($x) {
                    return Right::of($x + 1);
                },
                '$g' => function ($x) {
                    return Right::of($x + 2);
                },
                '$x' => 10,
            ],
            // I don't know if Left should be tested?
            'Left' => [
                '$return' => Left::of,
                '$f' => function ($x) {
                    return Left::of($x);
                },
                '$g' => function ($x) {
                    return Left::of($x);
                },
                '$x' => 10,
            ],
        ];
    }

    /**
     * @dataProvider provideApplicativeTestData
     */
    public function test_it_should_obey_applicative_laws(
        $pure,
        Applicative $u,
        Applicative $v,
        Applicative $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            [$this, 'assertEquals'],
            $pure,
            $u,
            $v,
            $w,
            $f,
            $x
        );
    }

    public function provideApplicativeTestData()
    {
        return [
            'Right' => [
                '$pure' => Either\pure,
                '$u' => Right::of(function () {
                    return 1;
                }),
                '$v' => Right::of(function () {
                    return 5;
                }),
                '$w' => Right::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
            'Left' => [
                '$pure' => Either\pure,
                '$u' => Left::of(function () {
                    return 1;
                }),
                '$v' => Left::of(function () {
                    return 5;
                }),
                '$w' => Left::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }

    /**
     * @dataProvider provideFunctorTestData
     */
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x
    ) {
        FunctorLaws::test(
            [$this, 'assertEquals'],
            $f,
            $g,
            $x
        );
    }

    public function provideFunctorTestData()
    {
        return [
            'Right' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Right::of(1),
            ],
            'Left' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Left::of(1),
            ],
        ];
    }
}
