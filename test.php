<?php
/**
 * Find the most common 3-page sequence from the corresponding Apache log.
 *
 * example `php test.php Apache.log`
 *
 * @author Seth M.
 *
 * Length: 1.5 hours
 * Issues: mostly syntax issues. Formats for calling functions, adding to arrays
 */
class Test {

    private $userPaths = [];
    private $pathCount = [];
    private $fileLineCount = 0;
    private $lessThanThree = 0;

    /**
     * Formated output max path count 
     * 
     * @return {bool}
     */
    function printPathCount() {
        $topCount = 0;
        arsort($this->pathCount);
        echo "\nTop 3 Page Sequence:\n";

        foreach($this->pathCount as $key => $value) {

            if($topCount === 0) {
                $topCount = $value;
            } else if ($topCount !== $value) {
                return true;
            }

            $pages= explode("::", $key);
            echo "\t - Occurred {$value} times.\n";
            echo "\t\t - {$pages[0]}\n";
            echo "\t\t - {$pages[1]}\n";
            echo "\t\t - {$pages[2]}\n\n";
        }
        return false; //Should only ever get here if max path is 1 or there are no records
    }

    /**
     * Extra checking to validate output
     *
     * @return {bool}
     */
    function runTest() {
        $count = 0;
        foreach($this->pathCount as $key => $value) {
            $count += $value;
        }
        if(($this->lessThanThree + $count) !== $this->fileLineCount) {
            echo "\nError, expected {$this->fileLineCount } Lines, "+
                "received {$count} lines with three pages and "+
                "{$this->lessThanThree} lines with less than 3 pages\n";
            return false;
        }
        return true;
    }

    /**
     * Store user path and update path count
     *
     * @param {String} ip
     * @param {String} referer
     */
    function storeData($ip, $referer) {

        $this->userPaths[$ip][] = $referer;

        if(count($this->userPaths[$ip]) === 3) {
            //store value   	     
            $ident = implode('::',$this->userPaths[$ip]);
            if(!isset($this->pathCount[$ident])) {
                $tmp = array($ident => 1);
                $this->pathCount = array_merge($this->pathCount, $tmp);
            } else {
                $this->pathCount[$ident] += 1;
            }
            if($this->pathCount[$ident] < 1) {
                echo "Error:: {$ident} should not be less than 1";
            }

            array_shift($this->userPaths[$ip]);//Missing [$ip]

        } else {
            //echo "Not three pages in sequence yet";
            $this->lessThanThree ++;
        }
    }

    /**
     * Open file and parse through lines
     *
     * @Param {string} logFile
     */
    function loadFile($logFile) {
        //http://www.startupcto.com/server-tech/apache/importing-apache-httpd-logs-into-mysql
        $pattern = '/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] "(\S+) (.+?) (\S+)" (\S+) (\S+) "([^"]+)" "([^"]+)"$/';
        $handle = @fopen('./'.$logFile, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $this->fileLineCount ++;
                if (preg_match($pattern, $buffer, $m)) {
                    $ip = $m[1];
                    $referer = $m[8];
                    $this->storeData($ip, $referer);
                } else {
                    echo "\nunable to pregmatch\n";
                }
            }
            fclose($handle);
        }
    }
}

$test = new Test;
$test->loadFile($argv[1]);
$test->printPathCount();
$test->runTest();
?>
