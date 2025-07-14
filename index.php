<?php
require 'vendor/autoload.php';

use App\Auth;
use App\Fetcher;
use App\APT;
use App\Technique;
use App\RunbookGenerator;

// Load configuration (adjust `config.php` for specifics)
$config = include __DIR__ . '/config.php';

$pdo = new PDO(
    "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
    $config['db_user'],
    $config['db_pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$auth = new Auth($pdo);
$auth->requireLogin();

$fetcher = new Fetcher();
$aptsData = $fetcher->listAPTGroups();

$tacts = $_REQUEST['tactics'] ?? [];
$selectedName = $_REQUEST['apt'] ?? null;

$apt = null;
$techniques = [];

if ($selectedName && isset($aptsData[$selectedName])) {
    $apt = new APT($aptsData[$selectedName]);
    $rawTechs = $fetcher->fetchTechniquesByAPT($apt->id);
    foreach ($rawTechs as $rt) {
        $techniques[] = new Technique($rt);
    }
}

// Handle PDF export or save
if ($apt && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['export_pdf'])) {
        RunbookGenerator::generatePdf($apt, $techniques, $tacts);
        exit;
    }
    if (isset($_POST['save_runbook'])) {
        $html = RunbookGenerator::renderHtml($apt, $techniques, $tacts);
        $stmt = $pdo->prepare("INSERT INTO saved_runbooks (user_id, apt, tactics, runbook_html) VALUES (?, ?, ?, ?)");
        $stmt->execute([$auth->userId(), $apt->name, json_encode($tacts), $html]);
        header('Location: history.php');
        exit;
    }
}

include __DIR__ . '/templates/main_ui.php';
