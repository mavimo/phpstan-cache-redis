<?php

declare(strict_types = 1);

namespace Mavimo\PHPStan\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use PHPStan\Cache\CacheStorage;

class PsrCacheStorage implements CacheStorage
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var int
     */
    private $expireTime;

    public function __construct(
        CacheItemPoolInterface $cacheItemPool,
        int $expireTime
    ) {
        $this->cacheItemPool = $cacheItemPool;
        $this->expireTime = $expireTime;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $key, string $variableKey)
    {
        $item = $this->cacheItemPool->getItem(
            $this->getPsrKey($key, $variableKey)
        );

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    public function save(string $key, string $variableKey, $data): void
    {
        $item = $this->cacheItemPool->getItem(
            $this->getPsrKey($key, $variableKey)
        );

        $item->set($data);
        $item->expiresAfter($this->expireTime);

        $this->cacheItemPool->save($item);
    }

    private function getPsrKey(string $key, string $variableKey): string
    {
        return sprintf('%s-%s', $key, $variableKey);
    }

}
