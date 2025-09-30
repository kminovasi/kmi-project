<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\View\Component;

class TotalPotentialBenefitCompanyChart extends Component
{
    public $eventId;
    public $event_name;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
        $this->event_name = Event::where('id', $eventId)->value('event_name');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Ambil event dan tipe event-nya
        $event = Event::with(['papers.team.company'])
            ->where('id', $this->eventId)
            ->first();

        if (!$event) {
            // Jika event tidak ditemukan, kembalikan tampilan kosong atau error
            return view('components.dashboard.event.default');
        }

        // Menyiapkan data yang dibutuhkan
        $data = [
            'event' => $event, // Menyediakan data event untuk template
        ];

        // Cek tipe event
        if ($event->type === 'group' || $event->type === 'national' || $event->type === 'international') {
            // Ambil data perusahaan beserta total financial benefit
            $companies = $event->papers
                ->groupBy(function ($paper) {
                    $companyCode = $paper->team->company->company_code ?? null;
        
                    // Gabungkan 7000 ke dalam 2000
                    return $companyCode == 7000 ? 2000 : $companyCode;
                })
                ->map(function ($papers, $companyCode) {
                    // Ambil nama perusahaan asli
                    $companyName = $papers->first()->team->company->company_name ?? 'Unknown';
        
                    // Jika company code adalah 2000 dan ada tim dari 7000 juga, ubah nama menjadi gabungan
                    if ($companyCode == 2000) {
                        $has7000 = $papers->filter(fn($p) => $p->team->company->company_code == 7000)->isNotEmpty();
                        if ($has7000) {
                            $companyName = 'PT Semen Indonesia (Persero) Tbk';
                        }
                    }
        
                    $totalBenefit = $papers->sum('potential_benefit');
        
                    // Proses nama perusahaan untuk mencari logo
                    $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($companyName));
                    $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
                    $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
                    $logoPath = public_path("assets/logos/{$sanitizedCompanyName}.png");
        
                    return [
                        'company_name' => $companyName,
                        'total_benefit' => $totalBenefit,
                        'logo' => file_exists($logoPath)
                            ? asset("assets/logos/{$sanitizedCompanyName}.png")
                            : asset('assets/logos/default-logo.png'),
                    ];
                })
                ->values();
        
            // Menambahkan data ke array view
            $data['companies'] = $companies;
        
            $data['chartData'] = $companies->map(function ($company) {
                return [
                    'company_name' => $company['company_name'],
                    'total_benefit' => $company['total_benefit'],
                    'logo' => $company['logo'],
                ];
            });
            
        } elseif ($event->type === 'internal') {
            // Ambil data perusahaan beserta total financial benefit untuk tipe event 'group'
            $companies = $event->papers
                ->groupBy('team.company.company_name') // Kelompokkan berdasarkan nama perusahaan
                ->map(function ($papers, $companyName) {
                    $totalBenefit = $papers->sum('potential_benefit'); // Hitung total benefit setiap perusahaan

                    // Proses nama perusahaan untuk logo
                    $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($companyName));
                    $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
                    $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
                    $logoPath = public_path("assets/logos/{$sanitizedCompanyName}.png");

                    return [
                        'company_name' => $companyName,
                        'total_benefit' => $totalBenefit,
                        'logo' => file_exists($logoPath)
                            ? asset("assets/logos/{$sanitizedCompanyName}.png")
                            : asset('assets/logos/default-logo.png'), // Default logo jika tidak ditemukan
                    ];
                })
                ->values();

            // Menambahkan data perusahaan ke array
            $data['companies'] = $companies;

            // Menambahkan data chart untuk ditampilkan dalam view
            $data['chartData'] = $companies->map(function ($company) {
                return [
                    'company_name' => $company['company_name'],
                    'total_benefit' => $company['total_benefit'],
                    'logo' => $company['logo'], // Menyertakan logo dalam data chart
                ];
            });
        } elseif ($event->type === 'AP') {
            // Ambil total benefit untuk event tipe 'AP'
            $totalBenefit = $event->papers->sum('potential_benefit');

            // Menambahkan totalBenefit ke data
            $data['totalBenefit'] = $totalBenefit;
        }

        // Menampilkan card dan chart jika tipe event adalah 'group' atau 'AP'
        return view('components.dashboard.event.total-potential-benefit-company-chart', $data, ['event_name' => $this->event_name]);
    }
}
