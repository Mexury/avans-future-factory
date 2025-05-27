<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Modules\ChassisModule;
use App\Models\Modules\WheelSetModule;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\ModuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ModuleSeeder::class
        ]);
        // Second attachment is redundant. Pivot table handles bidirectional association.
//        $wheelSetModule->compatibleChassisModules()->attach($chassisModule->id);
    }
}
