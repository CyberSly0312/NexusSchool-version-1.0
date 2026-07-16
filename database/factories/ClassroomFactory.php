<?php

namespace Database\Factories;

use App\Domains\Classroom\Enums\ClassroomStatus;
use App\Domains\Classroom\Enums\RoomType;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        $uniqueCode = strtoupper(fake()->bothify('??-###'));

        return [
            'school_id' => '1',
            'name' => 'Room-'.$uniqueCode,
            'code' => $uniqueCode,
            'building' => fake()->optional(0.7)->word(),
            'floor' => fake()->optional(0.7)->numerify('#'),
            'capacity' => fake()->numberBetween(10, 50),
            'room_type' => fake()->randomElement(RoomType::cases()),
            'description' => fake()->optional(0.5)->sentence(),
            'display_order' => fake()->numberBetween(1, 50),
            'status' => ClassroomStatus::Active,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => ClassroomStatus::Active,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => ClassroomStatus::Archived,
        ]);
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn () => [
            'school_id' => $schoolId,
        ]);
    }

    public function withOrder(int $order): static
    {
        return $this->state(fn () => [
            'display_order' => $order,
        ]);
    }

    public function withCapacity(int $capacity): static
    {
        return $this->state(fn () => [
            'capacity' => $capacity,
        ]);
    }

    public function withRoomType(RoomType $roomType): static
    {
        return $this->state(fn () => [
            'room_type' => $roomType,
        ]);
    }

    public function standard(): static
    {
        return $this->withRoomType(RoomType::StandardClassroom);
    }

    public function laboratory(): static
    {
        return $this->withRoomType(RoomType::ScienceLaboratory);
    }
}
