<?php

declare(strict_types=1);

namespace Yuloh\Recover;

use PHPUnit\Framework\TestCase;

class ThrowableSerializerTest extends TestCase
{
    function test_it_serializes_throwables()
    {
        $e      = new \Exception('Fail');
        $result = (new ThrowableSerializer())->serialize($e);

        $this->assertSame('Fail', $result['message']);
        $this->assertSame(\Exception::class, $result['class']);
        $this->assertSame(0, $result['code']);
        $this->assertSame(__FILE__, $result['file']);
        $this->assertInternalType('array', $result['trace']);
        $this->assertSame(__FUNCTION__, $result['trace'][0]['function']);
        $this->assertSame(__CLASS__, $result['trace'][0]['class']);
    }

    function test_it_serializes_trace_args()
    {
        try {
            (function (array $arry, bool $booly, int $int, float $floaty, string $stringy, $nully, $objecty, $resourcey) {
                throw new \Exception('whoops');
            })(['a' => 'hey'], false, 200, 1.2, 'hello', null, new \stdClass(), fopen('php://temp', 'r'));
        } catch (\Exception $e) {
            $result = (new ThrowableSerializer())->serialize($e);
        }

        $this->assertEquals(['array', ['a' => ['string', 'hey']]], $result['trace'][0]['args'][0]);
        $this->assertEquals(['boolean', false], $result['trace'][0]['args'][1]);
        $this->assertEquals(['integer', 200], $result['trace'][0]['args'][2]);
        $this->assertEquals(['float', 1.2], $result['trace'][0]['args'][3]);
        $this->assertEquals(['string', 'hello'], $result['trace'][0]['args'][4]);
        $this->assertEquals(['null', null], $result['trace'][0]['args'][5]);
        $this->assertEquals(['object', 'stdClass'], $result['trace'][0]['args'][6]);
        $this->assertEquals(['resource', 'stream'], $result['trace'][0]['args'][7]);
    }
}
