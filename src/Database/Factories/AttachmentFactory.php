<?php

namespace TomShaw\Mediable\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TomShaw\Mediable\Models\Attachment;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * @var class-string<Attachment>
     */
    protected $model = Attachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = $this->faker->unique()->word().'.jpg';

        return [
            'file_name' => $fileName,
            'file_original_name' => $fileName,
            'file_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(1_000, 5_000_000),
            'file_dir' => 'uploads/'.$fileName,
            'file_url' => 'uploads/'.$fileName,
            'title' => $this->faker->sentence(3),
            'caption' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'styles' => '',
            'hidden' => false,
        ];
    }
}
