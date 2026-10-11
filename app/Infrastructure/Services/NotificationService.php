<?php

namespace App\Infrastructure\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'general',
        ?string $link = null
    ): AppNotification {
        return AppNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }

    public static function sendToRole(
        string $roleName,
        string $title,
        string $message,
        string $type = 'general',
        ?string $link = null
    ): void {
        $users = User::whereHas('roles', fn ($q) => $q->where('name', $roleName))->get();
        foreach ($users as $user) {
            self::send($user->id, $title, $message, $type, $link);
        }
    }
}
