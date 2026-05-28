<!-- AI Assistant Panel to reuse right now in create task and edit task admin -->
<div class="ai-panel">
    <div class="ai-panel-header" id="aiPanelToggle">
        <h5><i class="fa-solid fa-wand-magic-sparkles"></i> AI Task Assistant</h5>
        <i class="fa-solid fa-chevron-down toggle-icon"></i>
    </div>
    <div class="ai-panel-body" id="aiPanelBody">

        <label class="ai-prompt-label">
            <i class="fa-solid fa-comment-dots"></i>
            Your instruction for the AI
            <span class="ai-prompt-hint">(optional — leave empty to use title &amp; description as-is)</span>
        </label>
        <textarea
            id="aiPrompt"
            class="ai-prompt-input"
            placeholder='e.g. "Make it more formal" or "Create a task for deploying the new API endpoint"'
        ></textarea>

        <div class="ai-actions">
            <button type="button" class="ai-btn ai-btn-rewrite" data-mode="rewrite">
                <i class="fa-solid fa-pen-nib"></i> Rewrite Task
            </button>
            <button type="button" class="ai-btn ai-btn-grammar" data-mode="grammar">
                <i class="fa-solid fa-spell-check"></i> Fix Grammar
            </button>
            <button type="button" class="ai-btn ai-btn-both" data-mode="both">
                <i class="fa-solid fa-rotate"></i> Fix &amp; Rewrite
            </button>
            <button type="button" class="ai-btn ai-btn-scratch" data-mode="scratch">
                <i class="fa-solid fa-robot"></i> Create from Scratch
            </button>
        </div>

        <div class="ai-status" id="aiStatus">
            <div class="ai-spinner"></div>
            <span id="aiStatusText"></span>
        </div>

    </div>
</div>