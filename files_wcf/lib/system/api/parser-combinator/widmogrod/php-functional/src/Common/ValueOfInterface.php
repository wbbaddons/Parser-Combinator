<?php
namespace Widmogrod\Common;

interface ValueOfInterface
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function extract();
}
