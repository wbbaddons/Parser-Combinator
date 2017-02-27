<?php
namespace example;

use Widmogrod\Monad;
use Widmogrod\Functional as f;

class ApplicativeFunctorTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_apply_every_function_in_collection_with_every_item_in_second()
    {
        $collectionA = Monad\Collection::of([
            function ($a) {
                return 3 + $a;
            },
            function ($a) {
                return 4 + $a;
            },
        ]);
        $collectionB = Monad\Collection::of([
            1,
            2
        ]);

        $result = $collectionA->ap($collectionB);

        $this->assertInstanceOf(Monad\Collection::class, $result);
        $this->assertEquals([4, 5, 5, 6], f\valueOf($result));
    }
}



