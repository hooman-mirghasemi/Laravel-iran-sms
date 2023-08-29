<?php

namespace HoomanMirghasemi\Sms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HoomanMirghasemi\Sms\Models\SmsReport;

class SmsReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmsReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $drivers = ['kavehnegar', 'fakesmssender'];

        return [
            'mobile' => '+98'.substr($this->faker->mobileNumber(), 1),
            'message' => $this->faker->paragraph(),
            'from' => $drivers[array_rand($drivers)],
            'number' => $this->faker->phoneNumber,
            'web_service_response' => $this->faker->paragraph(),
            'success' => $this->faker->boolean,
        ];
    }
}
