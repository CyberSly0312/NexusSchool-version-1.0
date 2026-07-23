<?php

namespace Database\Seeders;

use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Enums\RoomType;
use App\Models\Classroom;
use App\Domains\School\Models\School;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('slug', 'nexus-demo')->first();

        if (! $school) {
            return;
        }

        $classrooms = [
            ['name' => 'Room 101', 'code' => 'R101', 'building' => 'Building A', 'floor' => '1', 'capacity' => 40, 'room_type' => RoomType::StandardClassroom, 'description' => 'Standard classroom on ground floor', 'display_order' => 1],
            ['name' => 'Room 102', 'code' => 'R102', 'building' => 'Building A', 'floor' => '1', 'capacity' => 35, 'room_type' => RoomType::StandardClassroom, 'description' => 'Standard classroom on ground floor', 'display_order' => 2],
            ['name' => 'Science Lab 1', 'code' => 'SL1', 'building' => 'Building A', 'floor' => '2', 'capacity' => 30, 'room_type' => RoomType::ScienceLaboratory, 'description' => 'Fully equipped science laboratory', 'display_order' => 3],
            ['name' => 'Computer Lab 1', 'code' => 'CL1', 'building' => 'Building B', 'floor' => '1', 'capacity' => 35, 'room_type' => RoomType::ComputerLaboratory, 'description' => 'Computer lab with 35 workstations', 'display_order' => 4],
            ['name' => 'Main Library', 'code' => 'LIB', 'building' => 'Building B', 'floor' => '2', 'capacity' => 60, 'room_type' => RoomType::Library, 'description' => 'School main library', 'display_order' => 5],
            ['name' => 'Exam Hall', 'code' => 'EXH', 'building' => 'Building C', 'floor' => '1', 'capacity' => 100, 'room_type' => RoomType::ExaminationHall, 'description' => 'Large examination hall', 'display_order' => 6],
        ];

        foreach ($classrooms as $classroom) {
            Classroom::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'code' => $classroom['code'],
                ],
                [
                    'name' => $classroom['name'],
                    'building' => $classroom['building'],
                    'floor' => $classroom['floor'],
                    'capacity' => $classroom['capacity'],
                    'room_type' => $classroom['room_type']->value,
                    'description' => $classroom['description'],
                    'display_order' => $classroom['display_order'],
                    'status' => ClassroomStatus::Active->value,
                ],
            );
        }
    }
}
