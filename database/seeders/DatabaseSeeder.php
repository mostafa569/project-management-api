<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,  
            ProjectSeeder::class,
            TaskSeeder::class,
        ]);

        
        \App\Models\Task::all()->each(function ($task) {
            $users = \App\Models\User::inRandomOrder()->limit(rand(1, 3))->get();
            $task->users()->attach($users);
        });
    }
}