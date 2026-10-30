<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        collect(Service::cases())
            ->each(fn (Service $service) => DB::table('providers')
                ->upsert([
                    'id' => $service->id(),
                    'name' => $service->value,
                    'image_path' => $service->imagePath(),
                    'enabled' => $service->enabled(),
                    'access_object' => $service->accessObject(),
                    'auth_route' => $service->authRoute(),
                ], 'id')
            );
    }
}
