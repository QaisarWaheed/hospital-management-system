<?php
/**
 * Standard visible action row for summary / report filter forms (FR, DR/FR, MM, BK).
 *
 * @param string $submitName  GET/POST parameter name for submit (e.g. print_summary)
 * @param string $submitLabel Button label (e.g. PRINT SUMMARY)
 */
function fr_summary_form_actions($submitName, $submitLabel)
{
    $submitName = htmlspecialchars($submitName, ENT_QUOTES, 'UTF-8');
    $submitLabel = htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8');
    echo '<div class="col-md-12 col-sm-12 col-xs-12 fr-summary-form-actions" style="margin-top: 1.5em; padding-bottom: 2em;">';
    echo '<input class="btn btn-primary" type="submit" name="' . $submitName . '" value="' . $submitLabel . '" />';
    echo '<input class="btn btn-danger" type="reset" value="CLEAR FORM" />';
    echo '</div>';
}

function fr_summary_content_open()
{
    echo '<div class="col-md-9 background_image_ycdo" style="min-height: 450px; padding: 20px;">';
}

function fr_summary_print_redirect($printUrl, $returnPage)
{
    $printUrl = json_encode($printUrl);
    $returnPage = json_encode($returnPage);
    echo '<!DOCTYPE html><html><head><title>Opening report...</title></head><body>';
    echo '<script>window.open(' . $printUrl . ', "_blank");window.location.replace(' . $returnPage . ');</script>';
    echo '</body></html>';
}
