<?php

declare(strict_types=1);

namespace Mavimo\Tests\PHPStan\Cache;

use Mavimo\PHPStan\Cache\PsrCacheStorage;
use PHPStan\Testing\TestCase;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class PsrCacheStorageTest extends TestCase
{
    public function testEmptyItemReturnNull(): void
    {
        $psrCachePool = $this->prophesize(CacheItemPoolInterface::class);
        $psrCacheItem = $this->prophesize(CacheItemInterface::class);

        $psrCachePool->getItem(Argument::any())->willReturn($psrCacheItem);

        $psrCacheItem->isHit()->willReturn(false);

        $sut = new PsrCacheStorage(
            $psrCachePool->reveal(),
            1000
        );

        $this->assertNull($sut->load('foo', 'bar'));
    }

    public function testNonEmptyItemReturnValue(): void
    {
        $psrCachePool = $this->prophesize(CacheItemPoolInterface::class);
        $psrCacheItem = $this->prophesize(CacheItemInterface::class);

        $psrCachePool->getItem(Argument::any())->willReturn($psrCacheItem);

        $psrCacheItem->isHit()->willReturn(true);
        $psrCacheItem->get()->willReturn('expectedValue');

        $sut = new PsrCacheStorage(
            $psrCachePool->reveal(),
            1000
        );

        $this->assertSame('expectedValue', $sut->load('foo', 'bar'));
    }
}

