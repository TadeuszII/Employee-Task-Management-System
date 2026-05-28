<?php

define('LM_STUDIO_URL', 'http://127.0.0.1:1234/v1/chat/completions');
define('LM_MODEL',      'mistralai/ministral-3-3b');

// pre build prompt with placeholders for dynamic content
function lm_build_prompt(string $mode, string $title, string $description, string $userPrompt): string {
    $contextBlock = ($title || $description)
        ? "Current Task Title: \"{$title}\"\nCurrent Task Description: \"{$description}\"\n"
        : '';

    $userInstruction = $userPrompt
        ? "Additional user instruction: \"{$userPrompt}\"\n"
        : '';

    $modeInstructions = [
        'rewrite' => 'Rewrite the task title and description to be clearer, more professional, and better structured for an employee task management system. Keep the original meaning. Be concise and factual — no filler words, no fluff. Write in a professional office tone with respect and efficiency.',
        'grammar' => 'Fix all grammar, spelling, and punctuation mistakes in the task title and description. Do NOT change the meaning or wording beyond fixing errors. Keep the result concise and professional.',
        'both'    => 'First fix all grammar and spelling mistakes, then rewrite the task to be clearer and more professional. Keep the original meaning. Be concise and factual — no filler words, no fluff. Write in a professional office tone with respect and efficiency.',
        'scratch' => 'Create a completely new task title and description. You MUST follow the user instruction exactly — if they say 1 sentence, write exactly 1 sentence. If they say short, keep it short. Do not add extra details beyond what is asked. Be concise and factual — no filler words, no fluff. Write in a professional office tone with respect and efficiency.',
    ];

    $instruction = $modeInstructions[$mode] ?? $modeInstructions['rewrite'];

    return "You are a task writing assistant for an employee management system.\n"
         . $contextBlock
         . "\nTask: {$instruction}\n"
         . $userInstruction
         . "\nStyle rules: Be direct and factual. No unnecessary padding or overly long explanations. Professional office language only.\n"
         . "\nRespond ONLY with a raw JSON object. No markdown, no code fences, no bold text, no explanation. "
         . "The description must be plain text only, no bullet points, no markdown. Example:\n"
         . "{\"title\": \"Short task title\", \"description\": \"Detailed task description in plain sentences.\"}";
}


// Main function to call LM Studio and get the rewritten/fixed task
function lm_ask(string $mode, string $title, string $description, string $userPrompt): array {
    $prompt = lm_build_prompt($mode, $title, $description, $userPrompt);

    $payload = json_encode([
        'model'       => LM_MODEL,
        'messages'    => [['role' => 'user', 'content' => $prompt]],
        'temperature' => 0.4,
        'max_tokens'  => 512,
        'stream'      => false,
    ]);

    $ch = curl_init(LM_STUDIO_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 60,
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new RuntimeException('Could not connect to LM Studio: ' . $curlError);
    }

    if ($httpCode !== 200) {
        throw new RuntimeException('LM Studio returned HTTP ' . $httpCode . '. Is it running on port 1234?');
    }

    $data    = json_decode($response, true);
    $content = trim($data['choices'][0]['message']['content'] ?? '');

    // Strip markdown code fences
    $content = preg_replace('/```(?:json)?\s*/i', '', $content);
    $content = str_replace('```', '', $content);

    // Strip markdown bold (**text**)
    $content = preg_replace('/\*\*([^*]*)\*\*/', '$1', $content);

    // --- Attempt 1: standard json_decode after cleaning newlines inside values ---
    $cleaned = preg_replace_callback(
        '/"(title|description)"\s*:\s*"([\s\S]*?)(?<!\\\\)"/U',
        function ($m) {
            $value = str_replace(["\r\n", "\n", "\r", "\t"], ['\n', '\n', '\n', ' '], $m[2]);
            return '"' . $m[1] . '": "' . $value . '"';
        },
        $content
    );

    preg_match('/\{[\s\S]*"title"[\s\S]*"description"[\s\S]*\}/', $cleaned ?? $content, $matches);
    $result = !empty($matches[0]) ? json_decode($matches[0], true) : null;

    // --- Attempt 2: model forgot quotes around value — extract by key position ---
    if (empty($result['title']) || empty($result['description'])) {

        // Extract title — always quoted
        preg_match('/"title"\s*:\s*"([^"]+)"/i', $content, $tMatch);

        // Extract description — quoted or unquoted (grab everything until closing })
        preg_match('/"description"\s*:\s*"([\s\S]+?)"\s*\}/i', $content, $dMatchQuoted);
        preg_match('/"description"\s*:\s*([^"{\[}][^\}]*)/i',  $content, $dMatchRaw);

        $titleVal       = trim($tMatch[1]       ?? '');
        $descriptionVal = trim($dMatchQuoted[1] ?? $dMatchRaw[1] ?? '');

        // Clean up leftover markdown and whitespace from description
        $descriptionVal = preg_replace('/\*\*([^*]*)\*\*/', '$1', $descriptionVal);
        $descriptionVal = preg_replace('/`([^`]*)`/', '$1', $descriptionVal);
        $descriptionVal = preg_replace('/\s+/', ' ', $descriptionVal);
        $descriptionVal = trim($descriptionVal, " \t\n\r,");

        $result = ($titleVal && $descriptionVal)
            ? ['title' => $titleVal, 'description' => $descriptionVal]
            : null;
    }

    if (empty($result['title']) || empty($result['description'])) {
        throw new RuntimeException('AI returned an unexpected format. Please try again.');
    }

    return [
        'title'       => $result['title'],
        'description' => $result['description'],
    ];
}

// Handle AJAX request from the frontend
function lm_handle_ajax_request(): void {
    header('Content-Type: application/json');

    session_start();
    if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit();
    }

    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);

    $mode        = trim($body['action']      ?? '');
    $title       = trim($body['title']       ?? '');
    $description = trim($body['description'] ?? '');
    $userPrompt  = trim($body['prompt']      ?? '');

    $validModes = ['rewrite', 'grammar', 'both', 'scratch'];
    if (!in_array($mode, $validModes, true)) {
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        exit();
    }

    if ($mode === 'scratch' && $userPrompt === '') {
        echo json_encode(['success' => false, 'error' => 'Please enter an instruction so the AI knows what task to create.']);
        exit();
    }

    if ($mode !== 'scratch' && $title === '' && $description === '') {
        echo json_encode(['success' => false, 'error' => 'Please fill in the title or description first, or use "Create from Scratch".']);
        exit();
    }

    try {
        $result = lm_ask($mode, $title, $description, $userPrompt);
        echo json_encode(['success' => true, 'title' => $result['title'], 'description' => $result['description']]);
    } catch (RuntimeException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    exit();
}

// If this file is requested directly via POST (AJAX call), handle it immediately.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    lm_handle_ajax_request();
}