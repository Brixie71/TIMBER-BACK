<?php

namespace Tests\Feature;

use App\Models\CompressiveData;
use App\Models\FlexureData;
use App\Models\ReferenceValue;
use App\Models\ShearData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestDataEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_compressive_data_crud_and_filters(): void
    {
        $payload = [
            'specimen_name' => 'Specimen 1',
            'test_type' => 'compression',
            'base' => 10,
            'height' => 20,
            'length' => 30,
            'area' => 40,
            'pressure_bar' => 5,
            'max_force' => 100,
            'stress' => 25,
            'moisture_content' => 8,
            'species_id' => 1,
            'photo' => 'image-data',
        ];

        $createResponse = $this->postJson('/api/compressive-data', $payload);
        $createResponse->assertCreated()->assertJsonFragment([
            'specimen_name' => 'Specimen 1',
            'test_type' => 'compression',
        ]);

        $compressiveId = $createResponse->json('compressive_id');

        $listResponse = $this->getJson('/api/compressive-data?test_type=compression&per_page=5');
        $listResponse->assertOk();
        $listResponse->assertJsonFragment(['specimen_name' => 'Specimen 1']);

        $typeResponse = $this->getJson('/api/compressive-data/type/compression?paginate=false&limit=5');
        $typeResponse->assertOk();
        $this->assertSame('Specimen 1', $typeResponse->json('0.specimen_name'));

        $updatePayload = $payload;
        $updatePayload['specimen_name'] = 'Specimen 1 updated';
        $updatePayload['max_force'] = 150;

        $this->putJson("/api/compressive-data/{$compressiveId}", $updatePayload)
            ->assertOk()
            ->assertJsonFragment(['specimen_name' => 'Specimen 1 updated', 'max_force' => 150]);

        $this->deleteJson("/api/compressive-data/{$compressiveId}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Deleted']);

        $this->assertDatabaseMissing('compressive_data', ['compressive_id' => $compressiveId]);
    }

    public function test_shear_and_flexure_endpoints_return_filtered_results(): void
    {
        $shear = ShearData::create([
            'specimen_name' => 'Shear Specimen',
            'test_type' => 'shear',
            'base' => 5,
            'height' => 6,
            'length' => 7,
            'area' => 8,
            'pressure_bar' => 2,
            'max_force' => 90,
            'stress' => 12,
            'moisture_content' => 4,
        ]);

        $flexure = FlexureData::create([
            'specimen_name' => 'Flexure Specimen',
            'test_type' => 'flexure',
            'base' => 11,
            'height' => 12,
            'length' => 13,
            'area' => 14,
            'pressure_bar' => 3,
            'max_force' => 80,
            'stress' => 10,
            'moisture_content' => 5,
        ]);

        $this->getJson('/api/shear-data/type/shear?paginate=false')
            ->assertOk()
            ->assertJsonFragment(['shear_id' => $shear->shear_id]);

        $this->getJson('/api/flexure-data/type/flexure?paginate=false')
            ->assertOk()
            ->assertJsonFragment(['flexure_id' => $flexure->flexure_id]);
    }

    public function test_reference_values_sorting_is_whitelisted_and_hard_delete(): void
    {
        $first = ReferenceValue::create([
            'strength_group' => 'high',
            'common_name' => 'Aardvark',
            'botanical_name' => 'Botanical A',
            'compression_parallel' => 10,
            'compression_perpendicular' => 5,
            'shear_parallel' => 4,
            'bending_tension_parallel' => 9,
        ]);

        $second = ReferenceValue::create([
            'strength_group' => 'medium',
            'common_name' => 'Zebrawood',
            'botanical_name' => 'Botanical Z',
            'compression_parallel' => 12,
            'compression_perpendicular' => 6,
            'shear_parallel' => 5,
            'bending_tension_parallel' => 10,
        ]);

        $sorted = $this->getJson('/api/reference-values?sort_by=drop-table&sort_order=desc&paginate=false');
        $sorted->assertOk();
        $this->assertSame('Aardvark', $sorted->json('0.common_name'));

        $this->deleteJson("/api/reference-values/{$second->id}")
            ->assertOk()
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseMissing('reference_values', ['id' => $second->id]);
        $this->assertDatabaseHas('reference_values', ['id' => $first->id]);
    }
}
