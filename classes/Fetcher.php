<?php
namespace App;

use GuzzleHttp\Client;

class Fetcher {
    private Client $http;
    private string $apiRoot;

    public function __construct() {
        $this->http = new Client([
            'base_uri' => 'https://attack-taxii.mitre.org/',
            'headers'  => ['Accept' => 'application/taxii+json;version=2.1']
        ]);
        $info = $this->cachedFetch('root', function() {
            $r = $this->http->get('taxii2/');
            return json_decode($r->getBody(), true);
        });
        $this->apiRoot = $info['api_roots'][0] . '/';
    }

    public function listAPTGroups(): array {
        $collections = $this->getCollections();
        $apt = [];
        foreach ($collections as $c) {
            $objs = $this->getObjects($c['id']);
            foreach ($objs as $o) if ($o['type']==='intrusion-set') {
                $apt[$o['name']] = $o;
            }
        }
        return $apt;
    }

    public function fetchTechniquesByAPT(string $aptId): array {
        $collections = $this->getCollections();
        $techs = [];
        foreach ($collections as $c) {
            $objs = $this->getObjects($c['id']);
            foreach ($objs as $o) {
                if ($o['type']==='relationship'
                 && $o['relationship_type']==='uses'
                 && $o['source_ref']===$aptId
                ) {
                    foreach ($objs as $tgt) {
                        if ($tgt['id']===$o['target_ref']) {
                            $techs[] = $tgt;
                        }
                    }
                }
            }
        }
        return $techs;
    }

    private function getCollections(): array {
        $data = $this->cachedFetch('collections', function() {
            $r = $this->http->get("{$this->apiRoot}collections/");
            return json_decode($r->getBody(), true)['collections'] ?? [];
        });
        return $data;
    }

    private function getObjects(string $collectionId): array {
        return $this->cachedFetch("objects_{$collectionId}", fn() => json_decode(
            $this->http->get("{$this->apiRoot}collections/{$collectionId}/objects/")->getBody(), true
        )['objects'] ?? []);
    }

    private function cachedFetch(string $key, callable $fetcher) {
        $fn = __DIR__ . '/../data/cache/' . $key . '.json';
        if (file_exists($fn) && filemtime($fn) > time() - 3600) {
            return json_decode(file_get_contents($fn), true);
        }
        $data = $fetcher();
        file_put_contents($fn, json_encode($data));
        return $data;
    }
}
