<?php
namespace App;

class APT
{
    public string $id;
    public string $name;
    public string $description;
    public array $aliases;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? 'Unknown APT';
        $this->description = $data['description'] ?? 'No description available.';
        $this->aliases = $data['aliases'] ?? [];
    }

    public function getAliasesAsString(): string
    {
        return implode(', ', $this->aliases);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'aliases' => $this->aliases
        ];
    }
}
