<?php

include_once 'Filter.php';

class StringFilter extends Filter {

    protected $byte_count = 4;

    private $stringMap = [
        '0001','0003','0005','0007','0009'
    ];

    public function getPrefix(int $i): string {
        return '000' . $i;
    }

    protected function perform(string $data): bool {

        $header = substr($data, 0, $this->byte_count);
        return in_array($header, $this->stringMap);
    }
}
