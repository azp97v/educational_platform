<?php

namespace App\Notifications;

use App\Support\TextEncodingNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    private string $title;
    private string $message;
    private string $url;
    private string $category;
    private string $icon;

    public function __construct(
        string $title,
        string $message,
        string $url,
        string $category = 'general',
        string $icon = 'ri-notification-3-line'
    ) {
        $this->title = TextEncodingNormalizer::normalizeString($title) ?? $title;
        $this->message = TextEncodingNormalizer::normalizeString($message) ?? $message;
        $this->url = $url;
        $this->category = $category;
        $this->icon = $icon;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'category' => $this->category,
            'icon' => $this->icon,
        ];
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
