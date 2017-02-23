<?php

include_once 'Filter.php';

class Worker {

    /**
     * filter
     * 
     * @var Filter
     * @access private
     */
    private $filter;

    private $messages = [];

    private $matches = 0;

    private $loops = LOOPS / 10;

    // Inner loop
    private $iterations = 10;

    private $json;

    public function __construct(Filter $filter) {
        $this->filter = $filter;
        $this->json = file_get_contents('payload.json');
    }

    /**
     * Run through sample messages and match the ones we are interested in
     *
     * @access public
     * @return float - microseconds spent filtering through messages
     */
    public function run() {

        $this->matches = 0;

        $this->populateMessagesArray();

        // START
        $this->start = microtime(true);
        for ($j=0;$j<$this->loops;$j++) {
            $this->iterateMessagesArray();
        }

        // END
        $this->end = microtime(true);

        return $this->getTimeTook();
    }

    // Return results string
    public function getResults() {

        $f = money_format('%!.0i', $this->loops * $this->iterations);
        $m = money_format('%!.0i', $this->matches);

        $r = $this->getName() . ": " . $f . " messages. " . $m . " matches " . $this->getTimeTook();

        // Verify
        if ($this->matches !== intval($this->getExpected())) {
            $r .= ' !';
        }

        return $r . PHP_EOL;
    }

    // Run through the messages array and detect when we find a message we are interested in
    private function iterateMessagesArray() {
        for ($i=0;$i<$this->iterations;$i++) {
            if ($this->filter->match($this->messages[$i])) {
                $this->matches++;
            }
        }
    }

    // Fills our messages array with sample data
    private function populateMessagesArray() {
        $sources_count = 10;
        $this->messages = [];

        $decoded = json_decode($this->json);

        for ($i=0;$i<$sources_count;$i++) {
            $prefix = $this->filter->getPrefix($i);
            $decoded->id = '000' . $i;
            $this->messages []= $prefix . json_encode($decoded);
        }
    }

    // Returns the time the worker took
    private function getTimeTook() {
        return $this->end - $this->start;
    }

    // Returns the expected number of matches that this worker should have detected
    private function getExpected() {
        return $this->loops * $this->iterations * .5;
    }

    // Returns the type of filter used
    public function getName() {
        return get_class($this->filter);
    }
}

