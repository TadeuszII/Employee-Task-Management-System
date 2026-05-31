<?php

// Adres URL pod którym stoi LM Studio na naszym komputerze
define('LM_STUDIO_URL', 'http://127.0.0.1:1234/v1/chat/completions');
// Nazwa modelu pobranego w LM Studio
define('LM_MODEL', 'mistralai/ministral-3-3b');

// Funkcja która buduje treść zapytania (prompt) dla sztucznej inteligencji
function lm_build_prompt($mode, $title, $description, $userPrompt) {
    
    // Zbieramy obecne dane z formularza, jeśli są wpisane
    $contextBlock = "";
    if ($title != "" || $description != "") {
        $contextBlock = "Obecny Tytuł Zadania: \"" . $title . "\"\nObecny Opis Zadania: \"" . $description . "\"\n";
    }

    // Dodatkowe wytyczne wpisane ręcznie przez użytkownika
    $userInstruction = "";
    if ($userPrompt != "") {
        $userInstruction = "Dodatkowe instrukcje od użytkownika: \"" . $userPrompt . "\"\n";
    }

    // Przypisujemy instrukcje w zależności od wybranego trybu działania
    $instruction = "";
    if ($mode === 'rewrite') {
        $instruction = 'Rewrite the task title and description to be clearer, more professional, and better structured for an employee task management system. Keep the original meaning. Be concise and factual - no filler words, no fluff. Write in a professional office tone with respect and efficiency.';
    } else if ($mode === 'grammar') {
        $instruction = 'Fix all grammar, spelling, and punctuation mistakes in the task title and description. Do NOT change the meaning or wording beyond fixing errors. Keep the result concise and professional.';
    } else if ($mode === 'both') {
        $instruction = 'First fix all grammar and spelling mistakes, then rewrite the task to be clearer and more professional. Keep the original meaning. Be concise and factual - no filler words, no fluff. Write in a professional office tone with respect and efficiency.';
    } else if ($mode === 'scratch') {
        $instruction = 'Create a completely new task title and description. You MUST follow the user instruction exactly - if they say 1 sentence, write exactly 1 sentence. If they say short, keep it short. Do not add extra details beyond what is asked. Be concise and factual - no filler words, no fluff. Write in a professional office tone with respect and efficiency.';
    } else {
        $instruction = 'Rewrite the task title and description to be clearer and more professional.';
    }

    // Składamy pełną treść zapytania dla bota
    $prompt = "You are a task writing assistant for an employee management system.\n"
            . $contextBlock
            . "\nTask: " . $instruction . "\n"
            . $userInstruction
            . "\nStyle rules: Be direct and factual. No unnecessary padding or overly long explanations. Professional office language only.\n"
            . "\nRespond ONLY with a raw JSON object. No markdown, no code fences, no bold text, no explanation. "
            . "The description must be plain text only, no bullet points, no markdown. Example:\n"
            . "{\"title\": \"Short task title\", \"description\": \"Detailed task description in plain sentences.\"}";

    return $prompt;
}

// Główna funkcja łącząca się z naszym LM Studio i parsująca odpowiedź
function lm_ask($mode, $title, $description, $userPrompt) {
    
    // Budujemy prompt dla modelu
    $prompt = lm_build_prompt($mode, $title, $description, $userPrompt);

    // Pakujemy dane do wysłania w ładną tablicę
    $dataToSend = array(
        'model' => LM_MODEL,
        'messages' => array(
            array('role' => 'user', 'content' => $prompt)
        ),
        'temperature' => 0.4,
        'max_tokens' => 512,
        'stream' => false
    );

    // Kodujemy dane do formatu JSON
    $payload = json_encode($dataToSend);

    // Konfigurujemy cURL krok po kroku na chłopski rozum
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, LM_STUDIO_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    // Wykonujemy zapytanie do LM Studio
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Jeśli curl sypnął błędem (np. brak sieci/LM Studio wyłączone)
    if ($curlError) {
        throw new Exception('Błąd połączenia z LM Studio: ' . $curlError);
    }

    // Jeśli LM Studio zwróciło kod inny niż OK (200)
    if ($httpCode !== 200) {
        throw new Exception('LM Studio zwróciło błąd HTTP ' . $httpCode . '. Upewnij się, że program jest uruchomiony na porcie 1234!');
    }

    // Dekodujemy całą odpowiedź z API bota
    $data = json_decode($response, true);
    
    $content = "";
    if (isset($data['choices'][0]['message']['content'])) {
        $content = trim($data['choices'][0]['message']['content']);
    }

    // Czyszczenie śmieci z markdowna które bot mógł dorzucić na siłę
    $content = str_ireplace('```json', '', $content);
    $content = str_replace('```', '', $content);
    $content = str_replace('**', '', $content);
    $content = str_replace('`', '', $content);

    // Próbujemy od razu odczytać to jako JSON
    $result = json_decode($content, true);

    // Jeśli się nie udało (bo bot dopisał jakiś komentarz przed lub po JSONie)
    if (empty($result)) {
        // Szukamy klamry otwierającej i zamykającej
        $startPos = strpos($content, '{');
        $endPos = strrpos($content, '}');
        if ($startPos !== false && $endPos !== false) {
            // Wycinamy czysty kod JSON i dekodujemy ponownie
            $jsonOnly = substr($content, $startPos, $endPos - $startPos + 1);
            $result = json_decode($jsonOnly, true);
        }
    }

    // Ostateczny ratunek - prosty preg_match do wyciągnięcia pól, gdy JSON jest zepsuty
    if (empty($result) || empty($result['title']) || empty($result['description'])) {
        preg_match('/"title"\s*:\s*"([^"]+)"/i', $content, $tMatch);
        preg_match('/"description"\s*:\s*"([^"]+)"/i', $content, $dMatch);
        
        $titleVal = isset($tMatch[1]) ? trim($tMatch[1]) : '';
        $descVal = isset($dMatch[1]) ? trim($dMatch[1]) : '';

        // Jeśli opis nie miał cudzysłowu na końcu (częsty błąd LLM), bierzemy wszystko do końca klamry
        if (empty($descVal)) {
            preg_match('/"description"\s*:\s*([^"}]+)/i', $content, $dMatchRaw);
            $descVal = isset($dMatchRaw[1]) ? trim($dMatchRaw[1], " \t\n\r\",") : '';
        }

        if ($titleVal != "" && $descVal != "") {
            $result = array(
                'title' => $titleVal,
                'description' => $descVal
            );
        }
    }

    // Jeśli po wszystkich próbach nadal brakuje tytułu lub opisu
    if (empty($result) || empty($result['title']) || empty($result['description'])) {
        throw new Exception('Bot zwrócił dane w niepoprawnym formacie. Spróbuj ponownie.');
    }

    // Zwracamy tablicę z oczyszczonymi danymi
    return array(
        'title' => $result['title'],
        'description' => $result['description']
    );
}

// Funkcja obsługująca zapytanie AJAX wysłane przez nasz panel w JS
function lm_handle_ajax_request() {
    header('Content-Type: application/json');

    session_start();
    // Bezpieczeństwo - sprawdzamy czy użytkownik jest zalogowany
    if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
        echo json_encode(array('success' => false, 'error' => 'Brak autoryzacji'));
        exit();
    }

    // Odbieramy surowe dane wysłane metodą POST (fetch)
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);

    // Pobieramy dane i obcinamy zbędne spacje na początku i końcu
    $mode = isset($body['action']) ? trim($body['action']) : '';
    $title = isset($body['title']) ? trim($body['title']) : '';
    $description = isset($body['description']) ? trim($body['description']) : '';
    $userPrompt = isset($body['prompt']) ? trim($body['prompt']) : '';

    // Lista dozwolonych trybów
    $validModes = array('rewrite', 'grammar', 'both', 'scratch');
    if (!in_array($mode, $validModes)) {
        echo json_encode(array('success' => false, 'error' => 'Niepoprawny tryb działania bota.'));
        exit();
    }

    // Prosta walidacja formularza przed wysłaniem do bota
    if ($mode === 'scratch' && $userPrompt === '') {
        echo json_encode(array('success' => false, 'error' => 'Wpisz instrukcję dla bota, aby wiedział co wygenerować.'));
        exit();
    }

    if ($mode !== 'scratch' && $title === '' && $description === '') {
        echo json_encode(array('success' => false, 'error' => 'Uzupełnij najpierw tytuł lub opis zadania, albo użyj opcji "Stwórz od zera".'));
        exit();
    }

    // Wywołujemy bota i zwracamy wynik z powrotem do JS
    try {
        $result = lm_ask($mode, $title, $description, $userPrompt);
        echo json_encode(array(
            'success' => true,
            'title' => $result['title'],
            'description' => $result['description']
        ));
    } catch (Exception $e) {
        echo json_encode(array('success' => false, 'error' => $e->getMessage()));
    }

    exit();
}

// Jeśli ten skrypt został wywołany bezpośrednio przez POST (zapytanie AJAX z frontendu)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    lm_handle_ajax_request();
}