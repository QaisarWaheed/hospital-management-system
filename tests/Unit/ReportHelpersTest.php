<?php

namespace Ycdo\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ReportHelpersTest extends TestCase
{
    public function testSummaryResolveBranchIdPrefersBId(): void
    {
        $get = array('b_id' => '15', 'u' => '99', 'br_id' => '7');
        $this->assertSame(15, summary_resolve_branch_id($get, array(), 0));
    }

    public function testSummaryLoginBranchIdFallsBackToLegacyU(): void
    {
        $get = array('u' => '15', 'un' => 'POLICE');
        $this->assertSame(15, summary_login_branch_id($get, array(), 0));
    }

    public function testSummaryResolveBranchIdUsesSessionDefault(): void
    {
        $this->assertSame(9, summary_resolve_branch_id(array(), array(), 9));
    }

    public function testSummaryGenderCode(): void
    {
        $this->assertSame('F', summary_gender_code(1));
        $this->assertSame('M', summary_gender_code(2));
        $this->assertSame('O', summary_gender_code(0));
        $this->assertSame('O', summary_gender_code(null));
    }

    public function testSummaryLabConversionPercent(): void
    {
        $this->assertSame(0, summary_lab_conversion_percent(0, 5));
        $this->assertSame(0, summary_lab_conversion_percent(10, 0));
        $this->assertSame(50, summary_lab_conversion_percent(10, 5));
        $this->assertSame(100, summary_lab_conversion_percent(5, 10));
    }

    public function testSummaryPreviousTokanDisplay(): void
    {
        $this->assertSame('NULL', summary_previous_tokan_display('NULL'));
        $this->assertSame('NULL', summary_previous_tokan_display(null));
        $this->assertSame('3802822', summary_previous_tokan_display('3802822'));
    }

    public function testSummaryTokenReportParamsFromGet(): void
    {
        $params = summary_token_report_params(
            array('s' => '2026-04-01', 'e' => '2026-04-30', 'u' => '0', 'un' => 'ALL', 'br_id' => '15'),
            array()
        );
        $this->assertNotNull($params);
        $this->assertSame('2026-04-01', $params['from']);
        $this->assertSame('2026-04-30', $params['to']);
        $this->assertSame(15, $params['branch_id']);
        $this->assertSame(0, $params['user_id']);
        $this->assertSame('ALL', $params['user_name']);
    }

    public function testSummaryTokenReportParamsReturnsNullWhenMissingDates(): void
    {
        $this->assertNull(summary_token_report_params(array('s' => ''), array()));
        $this->assertNull(summary_token_report_params(array(), array()));
    }

    public function testSummaryLoginReportParamsAcceptsLegacyUParam(): void
    {
        $params = summary_login_report_params(
            array('s' => '2026-04-22', 'e' => '2026-04-22', 'u' => '15'),
            array(),
            9
        );
        $this->assertNotNull($params);
        $this->assertSame(15, $params['branch_id']);
    }

    public function testSummaryLoginReportParamsUsesSessionWhenNoBranchInRequest(): void
    {
        $params = summary_login_report_params(
            array('s' => '2026-04-22', 'e' => '2026-04-22', 'b_id' => '15'),
            array(),
            9
        );
        $this->assertSame(15, $params['branch_id']);
    }

    public function testProgressTokansSubquerySql(): void
    {
        $sql = progress_tokans_subquery_sql(9, '2026-04-23%');
        $this->assertStringContainsString("branch_id = '9'", $sql);
        $this->assertStringContainsString("created LIKE '2026-04-23%'", $sql);
    }

    public function testYcdoDaysInMonth(): void
    {
        $this->assertSame(31, ycdo_days_in_month(2026, 3));
        $this->assertSame(29, ycdo_days_in_month(2024, 2));
        $this->assertSame(0, ycdo_days_in_month(2026, 0));
    }

    public function testYcdoParseYearMonth(): void
    {
        $ym = ycdo_parse_year_month('2026-03');
        $this->assertSame(2026, $ym['year']);
        $this->assertSame('03', $ym['month']);
        $this->assertSame(31, $ym['days']);
    }

    public function testGynaeReportResolveParamsUsesBranchFromRequest(): void
    {
        $params = gynae_report_resolve_params(array('br_id' => '15', 'date' => '2026-04-01'), array(), 9);
        $this->assertSame(15, $params['br_id']);
        $this->assertSame('2026-04-01', $params['date']);
    }

    public function testReportSafeNumberFormatHandlesNull(): void
    {
        $this->assertSame('0', report_safe_number_format((float)(null ?? 0)));
        $this->assertSame('1,234', report_safe_number_format((float)(1234 ?? 0)));
    }
}
