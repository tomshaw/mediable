<?php

namespace TomShaw\Mediable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TomShaw\Mediable\Traits\MediaFilter;

class Attachment extends Model
{
    use HasFactory;
    use MediaFilter;

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
        'sortorder',
        'styles',
    ];
}
