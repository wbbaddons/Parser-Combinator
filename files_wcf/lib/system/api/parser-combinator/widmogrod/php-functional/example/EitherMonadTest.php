<?php
namespace example;

use Widmogrod\Functional as f;
use Widmogrod\Monad\Either;

function read($file)
{
    return is_file($file)
        ? Either\Right::of(file_get_contents($file))
        : Either\Left::of(sprintf('File "%s" does not exists', $file));
}

class EitherMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_concat_content_of_two_files_only_when_files_exists()
    {
        $concatF = f\liftM2(function ($first, $second) {
            return $first . $second;
        });

        $concat = $concatF(
            read(__FILE__),
            read('aaa')
        );

        $this->assertInstanceOf(Either\Left::class, $concat);
        $this->assertEquals('File "aaa" does not exists', f\valueOf($concat));
    }
}


