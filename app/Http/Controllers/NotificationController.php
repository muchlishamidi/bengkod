<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman daftar semua notifikasi milik user yang sedang login.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai sebuah notifikasi spesifik sebagai sudah dibaca.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        // Jika notifikasi memiliki URL tujuan, redirect ke URL tersebut
        $url = $notification->data['url'] ?? null;
        if ($url) {
            return redirect($url);
        }

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi user yang belum dibaca sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
