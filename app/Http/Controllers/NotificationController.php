<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function destroyAll()
    {
        // Menghapus semua notifikasi milik pengguna yang sedang login
        auth()->user()->notifications()->delete();

        return redirect()->route('notifications.index')->with('success', 'Semua notifikasi berhasil dihapus.');
    }
    public function destroy($id)
    {
        // Pastikan hanya menghapus notifikasi berdasarkan ID yang valid
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->delete();
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }


    // Tampilkan halaman manajemen notifikasi
    public function index()
    {
        $notifications = auth()->user()->notifications; // Mendapatkan semua notifikasi user
        return view('notifications.index', compact('notifications'));
    }


    // Tandai notifikasi sebagai telah dibaca
    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    // Tandai semua notifikasi sebagai telah dibaca
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->route('notifications.index')->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }

}
