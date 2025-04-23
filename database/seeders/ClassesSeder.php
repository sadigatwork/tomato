<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Classes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Student;

class ClassesSeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classes::factory()
            ->count(10)
            ->sequence(
                fn($sequence) => [
                    'name' => 'Class ' . $sequence->index + 1
                    // 'section_id' => $sequence->index % 3 + 1, // Assuming you have 3 sections
                ]
            )
            ->has(
                Section::factory()
                    ->count(3)
                    ->state(
                        new Sequence(
                            [
                                'name' => 'Section A',
                            ],
                            [
                                'name' => 'Section B',
                            ],
                            [
                                'name' => 'Section C',
                            ]
                        )
                    )
                    ->has(
                        Student::factory()
                            ->count(5)
                            ->state(
                                function (array $attributes, Section $section) {return ['class_id' => $section->class_id];}
                            )
                        )
             )
            ->create();
    }
}
