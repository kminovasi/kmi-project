<?php

namespace App\View\Components\replication;

use App\Models\ReplicationInnovation;
use Illuminate\View\Component;

class ReplicationTable extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $replicationData = ReplicationInnovation::with(['paper', 'personInCharge', 'company'])->paginate(10);
        return view('components.replication.replication-table', compact('replicationData'));
    }
}