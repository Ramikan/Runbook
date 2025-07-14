<?php
require 'vendor/autoload.php';
require 'classes/Fetcher.php';
require 'classes/APT.php';
require 'classes/Technique.php';

header('Content-Type: application/json');
if (!$_GET['apt']) { http_response_code(400); echo json_encode(['error'=>'Missing apt']); exit; }

$fetcher = new Fetcher();
$raw = $fetcher->fetchTechniquesByAPT($_GET['apt']);
$techs = array_map(fn($t)=>[
  'id'=> $t['external_references'][0]['external_id'] ?? $t['id'],
  'name'=> $t['name'],
  'tactics'=> $t['x_mitre_tactics'] ?? []
], $raw);
echo json_encode(['apt'=>$_GET['apt'], 'techniques'=>$techs], JSON_PRETTY_PRINT);
