<?php

namespace Aztech\Phinject\Config\Parser;

use Aztech\Phinject\Config\Parser;

class JsonParser implements Parser
{

    public function parse($data)
    {
        $data = $data ? json_decode($data, true) : array();
        
        if ($data === null) {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : "unknown";
            throw new \Exception(sprintf('Invalid JSON data : %s', $message));
        }
        
        return $data;
    }

    public function unparse(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
