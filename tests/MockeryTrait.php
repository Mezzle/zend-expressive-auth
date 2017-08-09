<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;

trait MockeryTrait
{
    use MockeryPHPUnitIntegration;

    public function tearDown()
    {
        M::close();
        parent::tearDown();
    }
}
