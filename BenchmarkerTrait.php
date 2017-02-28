<?php

trait BenchmarkerTrait {

    private static $LOG_STAMPS = false;

    private $logfile = STAT_LOG;

    private $timeStamps = [];

    private $memoryStamps = [];

    public function stampTime($note = '')
    {
        $t = $this->getMicrotime();
        $this->timeStamps[] = $t;
        if (self::$LOG_STAMPS)
            $this->log($note . $t);
    }

    public function stampMemory($note = '')
    {
        $t = $this->getMemoryUsage();
        $this->memoryStamps[] = $t;
        if (self::$LOG_STAMPS)
            $this->log($note . $this->convert($t));
    }

    public function getMemoryStamps()
    {
        return end($this->memoryStamps) - reset($this->memoryStamps);
    }

    public function getTimeStamps()
    {
        return end($this->timeStamps) - reset($this->timeStamps);
    }

    private function getMemoryPeakUsage($float = true)
    {
        return memory_get_peak_usage($float);
    }

    private function getMemoryUsage($float = true)
    {
        return memory_get_usage($float);
    }

    private function getDateTime()
    {
        $now = new \DateTime();
        return $now->format('Y-m-d H:i:s');
    }

    private function getMicrotime($float = true)
    {
        return microtime($float);
    }

    private function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    private function log($msg){
        global $argv;


        $fmsg = $msg . PHP_EOL;
        file_put_contents($this->logfile, $fmsg, FILE_APPEND);
    }

    public function __destruct()
    {
        $totalMemory = end($this->memoryStamps) - reset($this->memoryStamps);
        $totalTime = end($this->timeStamps) - reset($this->timeStamps);
        $this->log('Date Run: ' . $this->getDateTime());
        $this->log('Time: ' . $totalTime);
        $this->log('Memory: ' . $this->convert($totalMemory));
        $this->log('memory (peak): ' . $this->convert($this->getmemorypeakusage()));
        $this->log(PHP_EOL);
    }
}
