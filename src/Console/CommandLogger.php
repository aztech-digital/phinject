<?php

namespace Aztech\Phinject\Console;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

class CommandLogger implements LoggerInterface
{

    private static $levels = array(
        'debug' => 0,
        'info' => 1,
        'notice' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5,
        'alert' => 6,
        'emergency' => 7
    );

    private $messageStack = array();

    private $threshold = null;

    private $apply_threshold = false;

    private $filtering = false;

    private $enabled = array();

    private $output;

    public function __construct(OutputInterface $output)
    {
          $this->output = $output;
    }

    public function enableFiltering()
    {
        $this->filtering = true;
    }

    public function enableThreshold($level)
    {
        $this->threshold = self::$levels[$level];
        $this->apply_threshold = true;
    }

    public function addLevel($level)
    {
        $this->enabled[] = $level;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
    */
    public function log($level, $message, array $context = array())
    {

        if ($this->filtering && ! in_array($level, $this->enabled)) {
            return;
        }

        $output = $this->output;

        $write = function($level, $message) use($output) {
            if ($level == LogLevel::ERROR) {
                $output->writeln(sprintf('<error>%s</error>', $message));
            }
            elseif ($level == LogLevel::WARNING) {
                $output->writeln(sprintf('<comment>%s</comment>', $message));
            }
            elseif ($level == LogLevel::INFO) {
                $output->writeln(sprintf('<fg=green>%s</fg=green>', $message));
            }
            else {
                $output->writeln($message);
            }
        };

        if ($this->apply_threshold) {
            $this->messageStack[] = array($level, $message);

            if (self::$levels[$level] >= $this->threshold) {
                foreach ($this->messageStack as $stackedMessage) {
                    list($plevel, $pmessage) = $stackedMessage;

                    $write($plevel, $pmessage);
                }

                $this->messageStack = array();
                $this->apply_threshold = false;
            }

            return;
        }

        $write($level, $message);
    }

    public function resetStack() {
        $this->messageStack = array();
        $this->apply_threshold = true;
    }
}
