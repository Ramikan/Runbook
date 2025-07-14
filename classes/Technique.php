<?php
namespace App;

class Technique
{
    public string $id;
    public string $name;
    public string $description;
    public array $killChainPhases;
    public string $platforms;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? 'Unnamed Technique';
        $this->description = $data['description'] ?? 'No description.';
        $this->killChainPhases = [];

        if (isset($data['kill_chain_phases'])) {
            foreach ($data['kill_chain_phases'] as $phase) {
                $this->killChainPhases[] = $phase['phase_name'];
            }
        }

        $this->platforms = isset($data['x_mitre_platforms']) ? implode(', ', $data['x_mitre_platforms']) : 'Unknown';
    }

    public function getKillChainAsString(): string
    {
        return implode(', ', $this->killChainPhases);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'kill_chain_phases' => $this->killChainPhases,
            'platforms' => $this->platforms
        ];
    }
}
