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

    public function testLabReportUsesBatchedIbdQuery(): void
    {
        $path = dirname(__DIR__, 2) . '/bk/includes/progress_report_params.php';
        $contents = file_get_contents($path);
        $this->assertIsString($contents);
        $this->assertStringContainsString('function progress_ibd_lab_stats_by_doctor', $contents);
        $this->assertStringContainsString('GROUP BY ibd.doctor_id', $contents);
        $this->assertStringContainsString('progress_sql_date_clause($con, $like, \'ibd.created\')', $contents);
        $this->assertStringNotContainsString("item_by_doctor.created LIKE '", $contents);

        $printPath = dirname(__DIR__, 2) . '/bk/print_progress_report_monthly_lab.php';
        $printContents = file_get_contents($printPath);
        $this->assertIsString($printContents);
        $this->assertStringContainsString('progress_lab_monthly_report_maps', $printContents);
        $this->assertStringNotContainsString('GROUP BY items.category_id', $printContents);
    }
}
