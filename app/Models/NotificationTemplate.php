<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = ['event_key', 'name', 'email_subject', 'email_body', 'sms_body', 'whatsapp_body', 'description'];

    public static function getTemplate(string $eventKey): ?self
    {
        return self::where('event_key', $eventKey)->first();
    }

    public function renderSms(array $data): string
    {
        return self::replacePlaceholders($this->sms_body, $data);
    }

    public function renderWhatsApp(array $data): string
    {
        return self::replacePlaceholders($this->whatsapp_body, $data);
    }

    public function renderEmailSubject(array $data): string
    {
        return self::replacePlaceholders($this->email_subject ?? '', $data);
    }

    public function renderEmailBody(array $data): string
    {
        return self::replacePlaceholders($this->email_body ?? '', $data);
    }

    protected static function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}
