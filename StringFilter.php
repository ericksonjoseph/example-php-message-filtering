<?php

include_once 'Filter.php';

class StringFilter extends Filter {

    protected $byte_count;

    protected $strategy = 'only_one_size_wildcard';

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

    private $stringMap = [
        'sl.u' => true,
    ];

    public function __construct() {

        // Opportunity to do some optimization before running the test
        switch ($this->strategy) {

            // If we only have one wildcard we only have to read up to the wildcard's length
            case "only_one_size_wildcard":
                $keys = array_keys($this->stringMap);
                $this->byte_count = strlen(array_pop($keys));
                break;

            default:
                throw new \DomainException('cmon don\'t be a newb');
        }
    }

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
        return $this->only_one_size_wildcard($data); // NOTE: Making variable function will slow this filter down
    }

    /**
     * Strategy to use when we are filtering with a map that only has wild-cards of the same string length
     */
    private function only_one_size_wildcard(string $data): bool {
        $header = substr($data, 0, $this->byte_count);
        return isset($this->stringMap[$header]);
    }

    /**
     * This will tell the Worker how many matches we expected to get
     */
    public function getExpectedMatches(int $loops, int $iterations): int {
        $c = 0;
        $strategy = $this->strategy;
        foreach ($this->prefixes as $k)
            if ($this->$strategy($k))
                $c++;
        return $c * $loops;
    }
}
