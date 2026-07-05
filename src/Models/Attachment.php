<?php

namespace TomShaw\Mediable\Models;

use Illuminate\Database\Eloquent\Attributes\{Scope, UseFactory};
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use TomShaw\Mediable\Database\Factories\AttachmentFactory;

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
 * @property Carbon|string $created_at
 * @property Carbon|string $updated_at
 *
 * @method static Builder<static> visible()
 * @method static Builder<static> hidden()
 */
#[UseFactory(AttachmentFactory::class)]
class Attachment extends Model
{
    /** @use HasFactory<AttachmentFactory> */
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

    /**
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('hidden', false);
    }

    /**
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function hidden(Builder $query): void
    {
        $query->where('hidden', true);
    }
}
