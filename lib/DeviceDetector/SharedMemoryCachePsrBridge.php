<?php
declare(strict_types=1);

namespace RMS\DeviceDetector;

use Psr\SimpleCache\CacheInterface;

class SharedMemoryCachePsrBridge implements CacheInterface
{
    private $ns;


    public function __construct(string $ns)
    {
        $this->ns = $ns;
    }


    public function get($key, $default = null)
    {
        return rm_shared_memory_cache_get($this->ns, $key) ?? $default;
    }


    public function set($key, $value, $ttl = null)
    {
        // @todo process DateInterval TTL
        return rm_shared_memory_cache_set($this->ns, $key, $value, $ttl ?? 0) ?? false;
    }


    public function delete($key)
    {
        return rm_shared_memory_cache_unset($this->ns, $key);
    }


    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        return false;
    }


    public function getMultiple($keys, $default = null)
    {
        throw new \RuntimeException("Not implemented");
    }


    public function setMultiple($values, $ttl = null)
    {
        throw new \RuntimeException("Not implemented");
    }


    public function deleteMultiple($keys)
    {
        throw new \RuntimeException("Not implemented");
    }


    public function has($key)
    {
        return rm_shared_memory_cache_isset($this->ns, $key);
    }
}
