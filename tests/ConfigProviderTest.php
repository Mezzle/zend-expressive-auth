<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest;

use PHPUnit\Framework\TestCase;
use Stickee\Auth\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testIsIterable()
    {
        $config = new ConfigProvider();

        $generated_config = $config();

        $this->assertInternalType('array', $generated_config);

        $this->assertArrayHasKey('dependencies', $generated_config);
    }
}
