<?php

namespace App\Http\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;

    public function mount()
    {
        $this->notifications = Auth::user()->notifications;
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            $this->notifications = Auth::user()->notifications; // Update notifications
            $this->dispatchBrowserEvent('notificationDeleted'); // Emit event for animation
        }
    }

    public function updatedNotifications()
    {
        $this->notifications = Auth::user()->notifications;
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        $this->notifications = []; // Clear notifications
        $this->dispatchBrowserEvent('allNotificationsDeleted'); // Emit event for animation
    }

    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
