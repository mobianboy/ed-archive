<?php
//namespace Eardish\Gateway\Agents;
//
//class CacheAgentsTest extends AppConfigTest
//{
//
//    protected $cache;
//
//    public function setUp()
//    {
//        parent::setUp();
//
//        $conn = $this->getMockBuilder('Eardish\Gateway\Agents\Core\Connection')
//            ->getMock();
//
//        $this->cache = new CacheAgent($conn, $this->appConfig, "cache");
//    }
//
//    public function testCacheable()
//    {
//
//        $cacheableData = "{\"status\":{\"cacheable\": \"true\"}}";
//
//        $this->assertTrue($this->cache->cacheable($cacheableData));
//
//        $uncacheableData = "{\"status\":{\"cacheable\": \"false\"}}";
//
//        $this->assertFalse($this->cache->cacheable($uncacheableData));
//    }
//
//    public function testAddCache()
//    {
//        $this->assertNull(
//            $this->cache->addCache("", "", "")
//        );
//    }
//
//    public function testExpireCache()
//    {
//
//        $this->assertNull(
//            $this->cache->expireCache("", "", "")
//        );
//    }
//
//    public function testLookupCache()
//    {
//
//        $this->assertNull(
//            $this->cache->lookupCache("", "", "")
//        );
//    }
//}