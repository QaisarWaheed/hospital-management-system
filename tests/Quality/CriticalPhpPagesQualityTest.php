<?php

namespace Ycdo\Tests\Quality;

use PHPUnit\Framework\TestCase;

final class CriticalPhpPagesQualityTest extends TestCase
{
    /** @return array<int, string> */
    public static function criticalPhpFilesProvider(): array
    {
        $root = dirname(__DIR__, 2);
        $paths = array(
            'fr/print_summary.php',
            'fr/print_summary_login.php',
            'fr/print_summary_time.php',
            'fr/print_account_summary.php',
            'fr/print_report_account.php',
            'fr/print_report_month.php',
            'fr/print_accounts_monthly_report.php',
            'fr/user_summary.php',
            'fr/user_summary_login.php',
            'fr/user_summary_time.php',
            'fr/account_summary.php',
            'bk/print_progress_report_daily_branch.php',
            'bk/print_progress_report_daily_branch_time.php',
            'bk/includes/progress_report_params.php',
            'includes/report_helpers.php',
            'includes/account_report_helpers.php',
            'includes/month_report_helpers.php',
            'includes/account_summary_helpers.php',
            'dr/fr/print_summary.php',
            'dr/fr/print_summary_login.php',
        );

        $cases = array();
        foreach ($paths as $path) {
            $cases[] = array($path, $root . '/' . $path);
        }

        return $cases;
    }

    /**
     * @dataProvider criticalPhpFilesProvider
     */
    public function testPhpFileHasValidSyntax(string $relativePath, string $absolutePath): void
    {
        $this->assertFileExists($absolutePath, "Missing file: $relativePath");

        $output = array();
        $exitCode = 0;
        exec('php -l ' . escapeshellarg($absolutePath) . ' 2>&1', $output, $exitCode);

        $this->assertSame(0, $exitCode, implode("\n", $output));
    }

    /**
     * @dataProvider criticalPhpFilesProvider
     */
    public function testPhpFileStartsWithoutLeadingWhitespace(string $relativePath, string $absolutePath): void
    {
        $contents = file_get_contents($absolutePath);
        $this->assertIsString($contents);
        $this->assertStringStartsWith('<?php', ltrim($contents, "\xEF\xBB\xBF"), "$relativePath must start with <?php (no BOM/whitespace before it)");
    }

    /**
     * @dataProvider criticalPhpFilesProvider
     */
    public function testPhpFileHasNoMergeConflictMarkers(string $relativePath, string $absolutePath): void
    {
        $contents = file_get_contents($absolutePath);
        $this->assertIsString($contents);
        $this->assertStringNotContainsString('<<<<<<<', $contents, "$relativePath contains merge conflict markers");
        $this->assertStringNotContainsString('>>>>>>>', $contents, "$relativePath contains merge conflict markers");
    }

    public function testSummaryPrintFilesDoNotUseDrNameBeforeAssignment(): void
    {
        $root = dirname(__DIR__, 2);
        $patterns = array(
            'fr/print_summary.php',
            'fr/print_summary_time.php',
            'fr/print_summary_with_phone.php',
            'dr/fr/print_summary.php',
        );

        foreach ($patterns as $path) {
            $contents = file_get_contents($root . '/' . $path);
            $this->assertIsString($contents);
            $this->assertStringNotContainsString(
                'strpos($dr_name',
                $contents,
                "$path must not call strpos on \$dr_name before it is set"
            );
        }
    }

    public function testProgressReportDailyBranchUsesBatchHelpers(): void
    {
        $path = dirname(__DIR__, 2) . '/bk/print_progress_report_daily_branch.php';
        $contents = file_get_contents($path);
        $this->assertIsString($contents);
        $this->assertStringContainsString('progress_dia_patient_stats_by_doctor', $contents);
        $this->assertStringContainsString('progress_item_row_counts_by_doctor', $contents);
        $this->assertStringNotContainsString('mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id', $contents);
    }

    public function testFrSummaryPagesUseVisibleActionButtons(): void
    {
        $root = dirname(__DIR__, 2);
        $pages = array(
            'fr/user_summary.php',
            'fr/user_summary_time.php',
            'fr/user_summary_login.php',
            'fr/user_complete_summary.php',
            'fr/account_summary.php',
            'fr/comparision_all_branches.php',
            'dr/fr/user_summary.php',
            'bk/user_summary.php',
            'mm/user_summary.php',
        );

        foreach ($pages as $page) {
            $contents = file_get_contents($root . '/' . $page);
            $this->assertIsString($contents, $page);
            $this->assertStringContainsString('fr_summary_form_actions', $contents, $page);
            $this->assertStringNotContainsString('target="_blank"', $contents, $page);
        }
    }

    public function testUserSummaryOpensPrintWithHttpBuildQuery(): void
    {
        $path = dirname(__DIR__, 2) . '/fr/user_summary.php';
        $contents = file_get_contents($path);
        $this->assertIsString($contents);
        $this->assertStringContainsString('http_build_query', $contents);
        $this->assertStringNotContainsString('target="_blank"', $contents);
    }

    public function testPrintSummaryLoginResolvesBranchFromParams(): void
    {
        $path = dirname(__DIR__, 2) . '/fr/print_summary_login.php';
        $contents = file_get_contents($path);
        $this->assertIsString($contents);
        $this->assertStringContainsString('summary_login_report_params', $contents);
        $this->assertStringContainsString("\$b_id = \$loginParams['branch_id']", $contents);
        $this->assertStringContainsString('$b_id = (int) $b_id', $contents);
    }

    public function testAccountMonthReportsDoNotUseCalendarExtension(): void
    {
        $root = dirname(__DIR__, 2);
        $paths = array(
            'fr/print_report_account.php',
            'fr/print_report_month.php',
            'fr/print_accounts_monthly_report.php',
            'fr/print_account_summary.php',
            'dr/fr/print_report_account.php',
            'dr/fr/print_report_month.php',
            'dr/fr/print_accounts_monthly_report.php',
            'dr/fr/print_account_summary.php',
        );

        foreach ($paths as $path) {
            $contents = file_get_contents($root . '/' . $path);
            $this->assertIsString($contents, $path);
            $this->assertStringNotContainsString('cal_days_in_month', $contents, $path);
            $this->assertStringContainsString('ycdo_', $contents, $path);
        }
    }
}
