<?php
// app/VisionProviders/OpenGraphScraper.php
class OpenGraphScraper {
    public function fetch(string $url): array {
        $ctx = stream_context_create([
            'http' => ['method' => 'GET', 'timeout' => 6, 'header' => "User-Agent: InventoryApp/1.0\r\n"]
        ]);
        $html = @file_get_contents($url, false, $ctx);
        if ($html === false) throw new Exception('Failed to fetch URL');

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $meta = [];
        foreach ($xpath->query('//meta') as $m) {
            $p = $m->getAttribute('property');
            $n = $m->getAttribute('name');
            if ($p) $meta[$p] = $m->getAttribute('content');
            if ($n) $meta[$n] = $m->getAttribute('content');
        }
        return $meta;
    }
}
