<?php

setlocale(LC_MONETARY, 'en_US');

include 'Worker.php';
include 'JsonFilter.php';
include 'BinaryFilter.php';
include 'StringFilter.php';

// Will create a lot of output if set to true
define('DEBUG', false);

// How many messages to process (>= 10)
define('LOOPS', 100000);

// How many times to consecutively run each filter test
define('COUNT', 10);

$app = new App();

class App {

    private $count = COUNT;

    private $runs = [];

    public function __construct() {

        $jsonFilter = new JsonFilter();
        $binaryFilter = new BinaryFilter();
        $stringFilter = new StringFilter();

        $worker0 = new Worker($jsonFilter);
        $worker1 = new Worker($stringFilter);
        $worker2 = new Worker($binaryFilter);

        $this->runWorker($worker0);
        $this->runWorker($worker1);
        $this->runWorker($worker2);

        echo $this->getResults();
    }

    /**
     * Run the worker $this->count number of times.
     * Each run simulates LOOP amound of messages being filtered
     *
     * @param Worker $worker
     * @access private
     * @return void
     */
    private function runWorker(Worker $worker) {

        for ($i=0; $i<$this->count; $i++) {

            // Will run the worker and return the time it took to run all LOOPs
            $timeTook []= $worker->run();
            echo $worker->getResults();
        }

        // The average of the consecutive jobs that just took place
        $average = array_sum($timeTook) / count($timeTook);
        echo "average: " . $average . PHP_EOL;

        // Store data about the job that just ran
        $name = $worker->getName();
        $this->runs[$name] = $this->runs[$worker->getName()] ?? array();
        $this->runs[$name]['average'] = $average;
    }

    /**
     * Return a string with the results of all workers run
     *
     * @access private
     * @return string
     */
    private function getResults() {

        $s = "------------------------------------------\n";
        $s .= "\t\tResults\n";
        $s .= "------------------------------------------\n";

        foreach ($this->runs as $name => $stats) {

            foreach ($this->runs as $name2 => $stats2) {

                if ($name === $name2) continue;

                $avg1 = $stats['average'];
                $avg2 = $stats2['average'];

                $diff = $avg2 * (100 / $avg1) - $avg1 * (100 / $avg1);
                $round = round($diff, 2);

                $s .= "{$name} is %$round faster than {$name2}\n";
            }

            $s .= PHP_EOL;
        }

        $s .= "\n\n\n";
        return $s;
    }
}
