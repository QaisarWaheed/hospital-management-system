<?php

namespace Ycdo\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ProgressReportParamsTest extends TestCase
{
    public function testProgressTokansSubqueryFormat(): void
    {
        $sql = progress_tokans_subquery(null, 15, '2026-04-23%');
        $this->assertStringContainsString('tokans', $sql);
        $this->assertStringContainsString("branch_id = '15'", $sql);
        $this->assertStringContainsString("status = 1", $sql);
    }

    public function testProgressMapIntWithEmptyConnectionReturnsEmpty(): void
    {
        $map = progress_map_int(null, 'SELECT 1', 'id', 'cnt');
        $this->assertSame(array(), $map);
    }
}
