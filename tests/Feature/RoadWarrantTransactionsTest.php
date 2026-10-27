<?php

namespace Tests\Feature;

use App\Models\RoadWarrant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RoadWarrantTransactionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Isolated connection: never migrate or clear the configured application database.
        config(['database.default' => 'spj_test', 'database.connections.spj_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('manifest', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            $table->string('roadwarrant_uuid');
            $table->integer('trip_assign');
            $table->string('trip_date');
            $table->integer('status')->default(1);
        });
        Schema::create('trip_assign', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('trip');
        });
        Schema::create('trip', function (Blueprint $table) {
            $table->integer('trip_id')->primary();
            $table->string('trip_title');
        });
        Schema::create('trip_expenses_category', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
        });
        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('roadwarrant_uuid');
            $table->string('manifest_uuid')->nullable();
            $table->integer('category')->nullable();
            $table->integer('nominal')->default(0);
        });
    }

    private function manifest(string $uuid, int $assign, int $status = 1): array
    {
        return [
            'uuid' => $uuid, 'roadwarrant_uuid' => 'spj', 'trip_assign' => $assign,
            'trip_date' => '2026-10-26', 'status' => $status,
        ];
    }

    public function test_report_keeps_expenses_when_any_trip_relation_is_missing(): void
    {
        DB::table('manifest')->insert([
            $this->manifest('no-assignment', 10),
            $this->manifest('no-trip', 11),
            $this->manifest('complete', 12),
        ]);
        DB::table('trip_assign')->insert([['id' => 11, 'trip' => 999], ['id' => 12, 'trip' => 1]]);
        DB::table('trip')->insert(['trip_id' => 1, 'trip_title' => 'Malang - Bogor']);
        foreach (['deleted', 'no-assignment', 'no-trip', 'complete'] as $manifest) {
            DB::table('trip_expenses')->insert([
                'roadwarrant_uuid' => 'spj', 'manifest_uuid' => $manifest, 'nominal' => 100,
            ]);
        }
        DB::table('trip_expenses')->insert(['roadwarrant_uuid' => 'other', 'manifest_uuid' => 'complete']);

        $expenses = RoadWarrant::getExpensesListAkap('spj');

        $this->assertCount(4, $expenses);
        $this->assertSame(400, (int) $expenses->sum('nominal'));
        $this->assertNull($expenses[0]->trip_title);
        $this->assertNull($expenses[1]->trip_title);
        $this->assertNull($expenses[2]->trip_title);
        $this->assertSame('Malang - Bogor', $expenses[3]->trip_title);
    }

    public function test_edit_preserves_manifest_identity_status_and_expense_links_even_when_reordered(): void
    {
        DB::table('manifest')->insert([$this->manifest('outbound', 58, 3), $this->manifest('return', 59)]);
        DB::table('trip_expenses')->insert(['roadwarrant_uuid' => 'spj', 'manifest_uuid' => 'outbound']);

        DB::transaction(fn () => RoadWarrant::syncManifestTrips('spj', [
            $this->manifest('discard-return', 59),
            array_merge($this->manifest('discard-outbound', 58), ['trip_date' => '2026-10-28']),
        ]));

        $this->assertSame(2, DB::table('manifest')->count());
        $outbound = DB::table('manifest')->where('uuid', 'outbound')->first();
        $this->assertSame(1, (int) $outbound->id);
        $this->assertSame(3, (int) $outbound->status);
        $this->assertSame('2026-10-28', $outbound->trip_date);
        $this->assertSame('outbound', DB::table('trip_expenses')->value('manifest_uuid'));
    }

    public function test_changing_trip_assignment_keeps_existing_uuid(): void
    {
        DB::table('manifest')->insert($this->manifest('outbound', 58));
        DB::transaction(fn () => RoadWarrant::syncManifestTrips('spj', [$this->manifest('discard', 60)]));

        $this->assertSame('outbound', DB::table('manifest')->value('uuid'));
        $this->assertSame(60, (int) DB::table('manifest')->value('trip_assign'));
    }

    public function test_removing_a_trip_with_expenses_is_rejected_without_partial_changes(): void
    {
        DB::table('manifest')->insert([$this->manifest('outbound', 58), $this->manifest('return', 59)]);
        DB::table('trip_expenses')->insert(['roadwarrant_uuid' => 'spj', 'manifest_uuid' => 'return']);

        try {
            DB::transaction(fn () => RoadWarrant::syncManifestTrips('spj', [
                array_merge($this->manifest('discard', 58), ['trip_date' => '2026-10-28']),
            ]));
            $this->fail('Removing a trip with expenses must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('numberoftrip', $exception->errors());
        }

        $this->assertSame(2, DB::table('manifest')->count());
        $this->assertSame('2026-10-26', DB::table('manifest')->where('uuid', 'outbound')->value('trip_date'));
    }

    public function test_adding_and_removing_a_trip_without_expenses(): void
    {
        DB::table('manifest')->insert($this->manifest('outbound', 58));
        DB::transaction(fn () => RoadWarrant::syncManifestTrips('spj', [
            $this->manifest('discard', 58), $this->manifest('new-return', 59),
        ]));
        $this->assertSame(2, DB::table('manifest')->count());
        $this->assertNotEmpty(DB::table('manifest')->where('trip_assign', 59)->value('uuid'));

        DB::transaction(fn () => RoadWarrant::syncManifestTrips('spj', [$this->manifest('discard', 58)]));
        $this->assertSame(1, DB::table('manifest')->count());
        $this->assertSame('outbound', DB::table('manifest')->value('uuid'));
    }

    private function prepareEditRequest(): \Illuminate\Http\Request
    {
        Schema::create('ops_roadwarrant', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->unique();
            foreach (['bus_uuid', 'category', 'driver_1', 'driver_2', 'codriver', 'resto_id',
                'driver_allowance_1', 'codriver_allowance', 'trip_allowance', 'fuel_allowance',
                'etoll_allowance', 'crew_meal_allowance', 'total_allowance', 'description',
                'created_by', 'number_of_trip', 'transferto', 'is_replacement_bus',
                'departure_date', 'transferto_changed'] as $column) {
                $table->string($column)->nullable();
            }
        });
        Schema::create('v2_bus', function (Blueprint $table) {
            $table->string('uuid')->primary();
            $table->string('email');
            $table->timestamp('created_at')->nullable();
        });
        Schema::table('trip_assign', fn (Blueprint $table) => $table->integer('resto_id')->nullable());
        Schema::table('manifest', function (Blueprint $table) {
            $table->string('email_assign')->nullable();
            $table->string('fleet')->nullable();
        });
        DB::table('ops_roadwarrant')->insert(['uuid' => 'spj', 'description' => 'Original']);
        DB::table('v2_bus')->insert(['uuid' => 'bus', 'email' => 'bus@example.test']);
        DB::table('trip')->insert(['trip_id' => 102, 'trip_title' => 'Bandung - Malang']);
        DB::table('trip_assign')->insert(['id' => 93, 'trip' => 102, 'resto_id' => 12]);
        $this->actingAs((new \App\Models\User())->forceFill(['uuid' => 'editor']));

        return \Illuminate\Http\Request::create('/letter/roadwarrant/edit_akap/spj', 'POST', [
            'numberoftrip' => '1', 'driver_1' => '10', 'driver_2' => '11', 'codriver' => '12',
            'date' => '27/10/2026', 'bus_uuid' => 'bus', 'trip_assign' => '93',
            'driver_allowance' => '100', 'codriver_allowance' => '100', 'trip_allowance' => '100',
            'fuel_allowance' => '100', 'etoll_allowance' => '100', 'crew_meal_allowance' => '100',
            'totalsum' => '600', 'description' => 'Edited', 'transferto' => '10', 'old_transferto' => '10',
        ]);
    }

    public function test_confirm_edit_akap_updates_spj_and_keeps_manifest_and_expenses(): void
    {
        $request = $this->prepareEditRequest();
        DB::table('manifest')->insert($this->manifest('outbound', 93, 3));
        DB::table('trip_expenses')->insert(['roadwarrant_uuid' => 'spj', 'manifest_uuid' => 'outbound']);

        $response = (new \Modules\Letter\app\Http\Controllers\LetterRoadWarrantController())
            ->editRoadWarrantAkapStore($request, 'spj');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('Anda berhasil edit data SPJ AKAP', session('success'));
        $this->assertSame('Edited', DB::table('ops_roadwarrant')->value('description'));
        $manifest = DB::table('manifest')->sole();
        $this->assertSame('outbound', $manifest->uuid);
        $this->assertSame(3, (int) $manifest->status);
        $this->assertSame('2026-10-27', $manifest->trip_date);
        $this->assertSame('outbound', DB::table('trip_expenses')->value('manifest_uuid'));
    }

    public function test_confirm_edit_missing_spj_returns_404_without_changing_manifest(): void
    {
        $request = $this->prepareEditRequest();
        DB::table('manifest')->insert($this->manifest('outbound', 93));

        try {
            (new \Modules\Letter\app\Http\Controllers\LetterRoadWarrantController())
                ->editRoadWarrantAkapStore($request, 'missing');
            $this->fail('A missing SPJ must return 404.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(404, $exception->getStatusCode());
        }

        $this->assertSame('Original', DB::table('ops_roadwarrant')->value('description'));
        $this->assertSame('2026-10-26', DB::table('manifest')->value('trip_date'));
    }

    public function test_confirm_edit_rejects_removing_expensed_trip_without_partial_changes(): void
    {
        $request = $this->prepareEditRequest();
        DB::table('manifest')->insert([$this->manifest('outbound', 93), $this->manifest('return', 106)]);
        DB::table('trip_expenses')->insert(['roadwarrant_uuid' => 'spj', 'manifest_uuid' => 'return']);

        try {
            (new \Modules\Letter\app\Http\Controllers\LetterRoadWarrantController())
                ->editRoadWarrantAkapStore($request, 'spj');
            $this->fail('An expensed trip must not be removed.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('numberoftrip', $exception->errors());
        }

        $this->assertSame('Original', DB::table('ops_roadwarrant')->value('description'));
        $this->assertSame(2, DB::table('manifest')->count());
        $this->assertSame('2026-10-26', DB::table('manifest')->where('uuid', 'outbound')->value('trip_date'));
    }

}
