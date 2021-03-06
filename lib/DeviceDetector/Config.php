<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */


namespace RMS\DeviceDetector;

class Config
{
    public static function isRequestLogEnabled(): bool
    {
        return (bool)C('request_data_debug_enabled', 0);
    }

    public static function getSharedMemoryTtl(): int
    {
        return (int)C('shared_memory_cache_ttl', 300);
    }

    public static function getShowDetailedErrors(): bool
    {
        return (bool)C('show_detailed_errors', false);
    }
}
