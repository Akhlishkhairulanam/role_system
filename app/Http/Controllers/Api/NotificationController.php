<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Notification;

class NotificationController extends ApiController
{
    public function index(Request $request)
    {
        $notifications = Notification::where(

            'user_id',

            $request->user()->id

        )

        ->latest()

        ->get();

        return $this->success(

            $notifications->toArray(),

            'Notifikasi berhasil diambil.'

        );
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->is_read = true;

        $notification->save();

        return $this->success(

            $notification->toArray(),

            'Notifikasi dibaca.'

        );
    }
}
