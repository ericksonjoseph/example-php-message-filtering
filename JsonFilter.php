<?php

include_once 'Filter.php';

class JsonFilter extends Filter {

    private $stringMap = [
        '0001' => true,
        '0003' => true,
        '0005' => true,
        '0007' => true,
        '0009' => true
    ];

    public function getPrefix(int $i): string {
        return '';
    }

    protected function perform(string $data): bool {

        $decoded = json_decode($data);

        return isset($this->stringMap[$decoded->id]);
    }
}
