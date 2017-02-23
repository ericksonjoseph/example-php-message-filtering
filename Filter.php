<?php

abstract class Filter
{
    protected $byte_count = 1;

    protected $mask = 1;

    abstract public function getPrefix(int $i): string;

    abstract protected function perform(string $data): bool;

    public function match(string $data): bool
    {
        if (DEBUG != true) {
            return $this->perform($data);
        }

        $header = substr($data, 0, $this->byte_count);
        echo $this->getBinary($header);
        echo PHP_EOL;
        echo $this->getBinary($this->mask);
        echo PHP_EOL;
        echo "--------\n";
        if ($r = $this->perform($data)) {
            echo $this->getBinary($this->mask);
            echo ' -> ' . $header . ' == ' . $this->mask;
            echo PHP_EOL;
        }
        echo PHP_EOL;

        return $r;
    }

    protected function getBinary(string $input): string
    {
        return str_pad(decbin($input), 8, 0, STR_PAD_LEFT);
    }
}
