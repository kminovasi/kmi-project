<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;

    public function mount()
    {
        // Pastikan notifications adalah koleksi
        $this->notifications = collect(Auth::user()->notifications);
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            $this->notifications = collect(Auth::user()->notifications); // Update sebagai koleksi
            $this->dispatchBrowserEvent('notificationDeleted'); // Emit event untuk animasi
        }
    }

    public function updatedNotifications()
    {
        // Perbarui notifications sebagai koleksi
        $this->notifications = collect(Auth::user()->notifications);
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        $this->notifications = collect([]); // Kosongkan sebagai koleksi
        $this->dispatchBrowserEvent('allNotificationsDeleted'); // Emit event untuk animasi
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
