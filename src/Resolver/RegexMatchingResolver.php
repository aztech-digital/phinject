<?php

namespace Aztech\Phinject\Resolver;

abstract class RegexMatchingResolver implements Resolver
{

    private $regex;

    /**
     * @param string $regex
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    protected function extractMatch($reference)
    {
        $matches = array();

        preg_match($this->regex, $reference, $matches);

        return $matches[1];
    }

    public function accepts($reference)
    {
        return preg_match($this->regex, $reference);
    }
}
