<?php
namespace App;

use Dompdf\Dompdf;

class RunbookGenerator
{
    public static function renderHtml(APT $apt, array $techniques, array $tacticsFilter): string
    {
        ob_start();
        include __DIR__ . '/../templates/runbook_template.php';
        return ob_get_clean();
    }

    public static function generatePdf(APT $apt, array $techniques, array $tacticsFilter): void
    {
        $html = self::renderHtml($apt, $techniques, $tacticsFilter);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"{$apt->name}_runbook.pdf\"");
        echo $dompdf->output();
    }
}
