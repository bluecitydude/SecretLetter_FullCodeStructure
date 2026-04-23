<?php
namespace Middleware;

class RateLimiter {

    private string $storagePath;
    private int $maxRequests;
    private int $timeWindow;

    public function __construct(
        string $storagePath = "limits/",
        int $maxRequests = 5,
        int $timeWindow = 60
    ){

        $this->storagePath = rtrim($storagePath, '/') . '/';
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;

        if(!is_dir($this->storagePath)){
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function handleRequest(): bool {

        $ip = $this->getClientIP();

        $file = $this->generateStorageFile($ip);

        $requests = $this->loadRequests($file);

        $requests = $this->filterExpiredRequests($requests);

        if($this->isRateLimitExceeded($requests)){

            http_response_code(429);

            header("Content-Type: application/json");

            header("Retry-After: {$this->timeWindow}");

            echo json_encode([
                "status" => "failed",
                "message" => "Rate limit exceeded"
            ]);
            return false;
        }

        $requests[] = time();

        $this->persistRequests($file, $requests);
        return true;
    }

    private function getClientIP(): string {

        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private function generateStorageFile(string $ip): string {

        return $this->storagePath . md5($ip) . ".json";
    }

    private function loadRequests(string $file): array {

        if(!file_exists($file)){
            return [];
        }

        $content = file_get_contents($file);

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function filterExpiredRequests(array $requests): array {

        $currentTime = time();

        return array_filter(
            $requests,
            function($timestamp) use ($currentTime){

                return ($currentTime - $timestamp) < $this->timeWindow;
            }
        );
    }

    private function isRateLimitExceeded(array $requests): bool {

        return count($requests) >= $this->maxRequests;
    }

    private function persistRequests(string $file, array $requests): void {

        file_put_contents(
            $file,
            json_encode(array_values($requests))
        );
    }
}