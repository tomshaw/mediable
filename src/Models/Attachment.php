<?php

namespace TomShaw\Mediable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $file_name
 * @property string $file_original_name
 * @property string $file_type
 * @property int $file_size
 * @property string $file_dir
 * @property string $file_url
 * @property string $title
 * @property string $caption
 * @property string $description
 * @property int $sort_order
 * @property string $styles
 * @property bool $hidden
 * @property \Illuminate\Support\Carbon|string $created_at
 * @property \Illuminate\Support\Carbon|string $updated_at
 */
class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_original_name',
        'file_type',
        'file_size',
        'file_dir',
        'file_url',
        'title',
        'caption',
        'description',
        'sort_order',
        'styles',
        'hidden',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
            'hidden' => 'boolean',
        ];
    }
}
