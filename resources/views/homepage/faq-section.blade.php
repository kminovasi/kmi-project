@php
$accordionItems = [
    [
        'question' => 'Bagaimana cara mendaftar untuk acara?',
        'answer' =>
            'Anda dapat mendaftar melalui situs web kami. Cukup pilih acara yang ingin Anda ikuti dan ikuti petunjuk pendaftaran yang tersedia.',
    ],
    [
        'question' => 'Apa syarat untuk mengikuti acara?',
        'answer' => 'Anda perlu memenuhi persyaratan acara yang tercantum pada halaman detail acara.',
    ],
];
@endphp

<section class="container py-5">
    <header class="text-center">
        <h2 class="display-5 fw-bold text-danger mt-3">FAQ</h2>
        <p class="lead text-muted mt-3">
            Pertanyaan yang sering diajukan.
        </p>
    </header>

    <x-accordion id="faqAccordion">
        @foreach ($accordionItems as $index => $item)
            <x-accordion-item :index="$index" :question="$item['question']" :answer="$item['answer']" parentId="faqAccordion" />
        @endforeach
    </x-accordion>

</section>


