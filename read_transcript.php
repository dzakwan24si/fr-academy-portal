<?php
$transcriptPath = 'C:/Users/filbe/.gemini/antigravity/brain/363cc46b-5d30-4c49-b767-2d14430aa781/.system_generated/logs/transcript_full.jsonl';
if (!file_exists($transcriptPath)) {
    $transcriptPath = 'C:/Users/filbe/.gemini/antigravity/brain/363cc46b-5d30-4c49-b767-2d14430aa781/.system_generated/logs/transcript.jsonl';
}

$f = fopen($transcriptPath, 'r');
while (($line = fgets($f)) !== false) {
    if (str_contains($line, 'here\'s the entire excel file for Each program')) {
        $json = json_decode($line, true);
        $content = $json['content'] ?? '';
        // Extract lines around 2024/2025/2026
        $lines = explode("\n", $content);
        foreach ($lines as $l) {
            if (str_contains($l, 'Field Assistant') || str_contains($l, 'SUB TOTAL')) {
                if (preg_match('/202[3-6]/', $l) || str_contains($l, 'SUB TOTAL') || str_contains($l, '1645')) {
                    echo $l . "\n";
                }
            }
        }
    }
}
fclose($f);
