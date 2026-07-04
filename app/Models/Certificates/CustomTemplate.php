<?php

namespace App\Models\Certificates;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomTemplate extends Model
{
    protected $fillable = [
        'user_id', 'name', 'recipient_name',
        'title', 'subtitle', 'body_text',
        'primary_color', 'secondary_color', 'accent_color',
        'background_type', 'background_image', 'logo_image',
        'font_family', 'text_align',
        'title_x', 'title_y', 'title_size', 'title_rotation',
        'subtitle_x', 'subtitle_y', 'subtitle_size', 'subtitle_rotation',
        'name_x', 'name_y', 'name_size', 'name_rotation',
        'body_x', 'body_y', 'body_size', 'body_rotation',
        'logo_x', 'logo_y', 'logo_width', 'logo_rotation',
        'stamp_size', 'overlay_opacity', 'border_radius',
        'show_logo', 'show_stamp', 'is_issued', 'issued_at',
        'title_color', 'subtitle_color', 'name_color', 'body_color',
        'background_position_x', 'background_position_y', 'background_size',
    ];

    protected function casts(): array
    {
        return [
            'is_issued' => 'boolean',
            'issued_at' => 'datetime',
            'show_logo' => 'boolean',
            'show_stamp' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
