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
        $lines = explode("\n", $content);
        foreach ($lines as $l) {
            $cols = explode("\t", trim($l));
            if (count($cols) > 4 && str_contains($cols[1] ?? '', 'Field Assistant')) {
                echo "{$cols[0]} | {$cols[1]} | R:{$cols[2]}, T:{$cols[3]}, L:{$cols[4]} | Trainee:{$cols[5]}, Culled:{$cols[6]}, Passed:{$cols[7]}, Resign:{$cols[8]}, Eksis:{$cols[9]}\n";
            }
        }
    }
}
fclose($f);
