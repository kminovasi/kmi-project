<div class="accordion-item">
    <h2 class="accordion-header lead" id="heading-{{ $index }}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#item-{{ $index }}" aria-expanded="false" aria-controls="item-{{ $index }}">
            {{ $question }}
        </button>
    </h2>
    <div id="item-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}"
        data-bs-parent="#{{ $parentId }}">
        <div class="accordion-body">
            <p class="text-muted">
                {{ $answer }}
            </p>
        </div>
    </div>
</div>
