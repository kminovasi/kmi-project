<?php

namespace App\View\Components\patent;

use App\Models\Patent;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class PatentTable extends Component
{
    public $patentData;

    public function __construct()
    {
        // Load data langsung di constructor
        $this->patentData = Patent::with(['paper', 'employee', 'patenMaintenance'])
            ->visibleTo(Auth::user())
            ->paginate(10);
    }

    public function render()
    {
        return view('components.patent.patent-table');
    }
}
