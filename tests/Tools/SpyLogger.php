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

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    /**
     * @param mixed $level
     * @param array<mixed> $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $levelString = is_string($level)
            ? $level
            : ((is_object($level) && method_exists($level, '__toString'))
                ? (string) $level
                : (string) json_encode($level)
            );

        $this->records[] = [
            'level' => $levelString,
            'message' => (string) $message,
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
