<?php

namespace Phariscope\Event\Tests\Tools;

use Psr\Log\LoggerInterface;

class SpyLogger implements LoggerInterface
{
    public array $records = [];

    public function emergency($message, array $context = []): void { $this->log('emergency', $message, $context); }
    public function alert($message, array $context = []): void { $this->log('alert', $message, $context); }
    public function critical($message, array $context = []): void { $this->log('critical', $message, $context); }
    public function error($message, array $context = []): void { $this->log('error', $message, $context); }
    public function warning($message, array $context = []): void { $this->log('warning', $message, $context); }
    public function notice($message, array $context = []): void { $this->log('notice', $message, $context); }
    public function info($message, array $context = []): void { $this->log('info', $message, $context); }
    public function debug($message, array $context = []): void { $this->log('debug', $message, $context); }

    public function log($level, $message, array $context = []): void
    {
        $this->records[] = [
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }

    public function countLevel(string $level): int
    {
        return count(array_filter($this->records, fn ($r) => $r['level'] === $level));
    }

    public function lastRecord(): ?array
    {
        return empty($this->records) ? null : $this->records[array_key_last($this->records)];
    }
}


