const AI_ENDPOINT = 'app/LM_Bot.php';

// Toggle panel open/close
document.getElementById('aiPanelToggle').addEventListener('click', function () {
    this.classList.toggle('open');
    document.getElementById('aiPanelBody').classList.toggle('open');
});

// Attach click handlers to all AI action buttons
document.querySelectorAll('.ai-btn[data-mode]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        runAI(this.dataset.mode);
    });
});

async function runAI(mode) {
    const title       = document.getElementById('taskTitle').value.trim();
    const description = document.getElementById('taskDescription').value.trim();
    const userPrompt  = document.getElementById('aiPrompt').value.trim();
    const buttons     = document.querySelectorAll('.ai-btn');

    // Client-side validation
    if (mode === 'scratch' && !userPrompt) {
        setStatus('error', 'Please enter an instruction so the AI knows what task to create.');
        return;
    }

    if (mode !== 'scratch' && !title && !description) {
        setStatus('error', 'Fill in the title or description first, or use "Create from Scratch".');
        return;
    }

    buttons.forEach(b => b.disabled = true);
    setStatus('thinking', 'AI is thinking...');

    try {
        const response = await fetch(AI_ENDPOINT, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({
                action:      mode,
                title:       title,
                description: description,
                prompt:      userPrompt
            })
        });

        if (!response.ok) {
            throw new Error('Server returned HTTP ' + response.status);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Unknown error from AI.');
        }

        document.getElementById('taskTitle').value       = data.title;
        document.getElementById('taskDescription').value = data.description;

        setStatus('success', '✓ Done! Review the result and submit when ready.');

    } catch (err) {
        setStatus('error', err.message || 'Failed to reach the AI. Check that LM Studio is running.');
        console.error('AI panel error:', err);
    } finally {
        buttons.forEach(b => b.disabled = false);
    }
}

function setStatus(type, message) {
    const statusEl  = document.getElementById('aiStatus');
    const statusText = document.getElementById('aiStatusText');
    statusEl.className  = 'ai-status ' + type;
    statusText.textContent = message;
}