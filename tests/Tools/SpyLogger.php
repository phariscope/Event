<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests\Tools;

use Psr\Log\LoggerInterface;

class SpyLogger implements LoggerInterface
{
    /**
     * @var list<array{level:string,message:string,context:array<mixed>}> $records
     */
    public array $records = [];

    public function emergency(mixed $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
    public function alert(mixed $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }
    public function critical(mixed $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    public function error(mixed $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    public function warning(mixed $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    public function notice(mixed $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }
    public function info(mixed $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    public function debug(mixed $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * @param array<mixed> $context
     */
    public function log(mixed $level, mixed $message, array $context = []): void
    {
        $levelString = match (true) {
            is_string($level) => $level,
            is_object($level) && method_exists($level, '__toString') => (string) $level,
            default => json_encode($level) ?: 'unknown'
        };

        $messageString = match (true) {
            is_string($message) => $message,
            is_object($message) && method_exists($message, '__toString') => (string) $message,
            default => json_encode($message) ?: 'unknown'
        };

        $this->records[] = [
            'level' => $levelString,
            'message' => $messageString,
            'context' => $context,
        ];
    }

    public function countLevel(string $level): int
    {
        return count(array_filter(
            $this->records,
            /** @param array{level:string,message:string,context:array<mixed>} $r */
            fn (array $r): bool => $r['level'] === $level
        ));
    }

    /**
     * @return array{level:string,message:string,context:array<mixed>}|null
     */
    public function lastRecord(): ?array
    {
        return $this->records === [] ? null : $this->records[array_key_last($this->records)];
    }
}
