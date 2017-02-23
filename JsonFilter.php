<?php

include_once 'Filter.php';

class JsonFilter extends Filter {

    private $stringMap = [
        '0001','0003','0005','0007','0009'
    ];

    public function getPrefix(int $i): string {
        return '';
    }

    protected function perform(string $data): bool {

        $decoded = json_decode($data);

        return in_array($decoded->id, $this->stringMap);
    }
}
