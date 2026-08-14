{{-- _form_questions.blade.php — Screening Questions Component --}}
{{-- Intent: Modular partial for candidate screening questions builder. --}}
<div class="card card-custom mb-4" id="section-questions">
    <div class="card-header">
        <h3 class="card-title">Candidate Screening Questions</h3>
    </div>
    <div class="card-body">
        <div id="questionsContainer" class="questions-list mb-3">
            {{-- Dynamically populated via JS --}}
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuestionPrompt()">
            <i class="fas fa-plus"></i> Add Question
        </button>
    </div>
</div>
