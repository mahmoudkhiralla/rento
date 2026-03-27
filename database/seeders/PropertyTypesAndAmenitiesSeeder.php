<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypesAndAmenitiesSeeder extends Seeder
{
    /**
     * Seed property types, amenities, and link them to existing properties.
     */
    public function run(): void
    {
        $typeNames = ['شقة', 'فيلا', 'شاليه', 'استوديو'];
        foreach ($typeNames as $name) {
            PropertyType::firstOrCreate(['name' => $name]);
        }

        $amenityNames = [
            'الانترنت لاسلكي',
            'شاشة مسطحة',
            'مكيف هواء مركزي',
            'حمام سباحة خاص',
            'حديقة خاصة',
            'موقف خاص للسيارات',
        ];
        $amenityModels = [];
        foreach ($amenityNames as $name) {
            $amenityModels[] = Amenity::firstOrCreate(['name' => $name]);
        }

        Property::chunk(50, function ($props) use ($amenityModels) {
            foreach ($props as $p) {
                $p->address = $p->address ?: ($p->city ? ($p->city.' - حي مركزي') : 'عنوان غير محدد');
                $p->rental_type = $p->rental_type ?: 'يومي';
                $p->capacity = $p->capacity ?: 6;
                $p->bedrooms = $p->bedrooms ?: 3;
                $p->bathrooms = $p->bathrooms ?: 2;

                if (! $p->property_type_id) {
                    $type = PropertyType::inRandomOrder()->first();
                    if ($type) {
                        $p->property_type_id = $type->id;
                    }
                }

                $p->save();

                $ids = collect($amenityModels)->shuffle()->take(rand(3, 6))->pluck('id')->all();
                if (count($ids)) {
                    $p->amenities()->syncWithoutDetaching($ids);
                }
            }
        });
    }
}
