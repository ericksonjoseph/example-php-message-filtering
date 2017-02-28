<?php

include_once 'Filter.php';

class StringFilter extends Filter {

    protected $byte_count = 5;

    protected $strategy = 'onlyOneSizeWildcard';

    /* All possible incoming messages */
    private $prefixes = [
        'sl.ul',
        'sl.um',
        'sl.us',
        'sl.uc',
        'sl.ad',
        'cp.rw',
        'cp.sh',
        'cp.ad',
        'cp.in',
        'cp.uc',
    ];

    /* Messages we are interested in */
    private $stringMap = [
    ];

    /* In order to support wildcards efficiently, we separate these into a different array */
    private $wildcardMap = [
        'cp' => true,
    ];

    /**
     * Provide the prefixes that will be used to create messages
     */
    public function getPrefix(int $i): string {
        return $this->prefixes[$i];
    }

    /**
     * Run the filtering logic!
     */
    protected function perform(string $data): bool {
        return $this->mainStrategy($data); // NOTE: Making variable function will slow this filter down
    }

    /**
     * Strategy to use when we are filtering with a map that only has wild-cards of the same string length
     */
    private function mainStrategy(string $data): bool {
        $header = substr($data, 0, $this->byte_count);
        if (isset($this->stringMap[$header]))
            return true;
        $x = explode('.', $header);
        return isset($this->wildcardMap[reset($x)]);
    }

    /**
     * This will tell the Worker how many matches we expected to get
     */
    public function getExpectedMatches(int $loops, int $iterations): int {
        $c = 0;
        $strategy = 'mainStrategy';
        foreach ($this->prefixes as $k)
            if ($this->$strategy($k))
                $c++;
        return $c * $loops;
    }
}
