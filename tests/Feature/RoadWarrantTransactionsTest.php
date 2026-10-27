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
}
