<?php
$lines = file('C:/Users/filbe/.gemini/antigravity/brain/363cc46b-5d30-4c49-b767-2d14430aa781/.system_generated/logs/transcript.jsonl');
foreach ($lines as $line) {
    if (strpos($line, '"type":"USER_INPUT"') !== false) {
        $json = json_decode($line, true);
        echo "=== STEP " . $json['step_index'] . " ===\n";
        echo ($json['content'] ?? '') . "\n\n";
    }
}
