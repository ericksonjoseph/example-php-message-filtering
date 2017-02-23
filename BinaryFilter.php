<?php

include_once 'Filter.php';

class BinaryFilter extends Filter {

    // Bytes to chop off string
    protected $byte_count = 4;

    // Binary number to match
    protected $mask = 1;

    public function getPrefix(int $i): string {
        return '000' . $i;
    }

    protected function perform(string $data): bool {
        return $this->work($data);
    }

    private function work(string $data): bool {
        $header = substr($data, 0, $this->byte_count);
        return ($this->mask & $header) !== 0;
    }
}
