<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoadWarrant extends Model
{
    public function scopeGetRoadWarrantList($query, $isNeedDraft = true)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select(
                'roadwarrant.*',
                'bus.name AS busname',
                'book.start_date as pariwisata_start_date',
                'tfto.first_name as tfName',
                'tfto.second_name as tfSecondName',
                'tfto.bank_name as tfBank',
                'tfto.bank_number as tfNumber',
            )
            ->join("v2_bus AS bus", "bus.uuid", "=", "roadwarrant.bus_uuid")
            ->leftJoin("v2_book AS book", "book.uuid", "=", "roadwarrant.manifest_uuid")
            ->leftJoin("employee_history AS tfto", "tfto.id", "=", "roadwarrant.transferto")
            ->orderBy('roadwarrant.id', 'DESC')
            ->take(1000);

        if ($isNeedDraft === false) {
            $query->where('roadwarrant.status', '!=', 0);
        }

        return $query->get();
    }

    public function scopeGetRoadWarrant($query, $uuid)
    {
        $sql = "
            SELECT 
                book.*,
                roadwarrant.*,
                bus.name AS busname,
                class.name AS classname,
                class.seat,
                customer.name AS customer_name,
                customer.phone AS customer_phone,
                city_from.name AS city_from,
                city_to.name AS city_to,
                driver_1.id AS driver_1_id,
                driver_2.id AS driver_2_id,
                codriver.id AS codriver_id,
                CONCAT(driver_1.first_name, ' ', driver_1.second_name) AS driver_1_name,
                CONCAT(driver_2.first_name, ' ', driver_2.second_name) AS driver_2_name,
                CONCAT(codriver.first_name, ' ', codriver.second_name) AS codriver_name
            FROM ops_roadwarrant AS roadwarrant
            INNER JOIN v2_bus AS bus 
                ON bus.uuid = roadwarrant.bus_uuid
            INNER JOIN v2_book AS book 
                ON book.uuid = roadwarrant.manifest_uuid
            INNER JOIN v2_customer AS customer 
                ON customer.uuid = book.customer_uuid
            INNER JOIN v2_class AS class 
                ON class.uuid = bus.class_uuid
            LEFT JOIN v2_area_city AS city_from 
                ON city_from.uuid = book.departure_city_uuid
            LEFT JOIN v2_area_city AS city_to 
                ON city_to.uuid = book.destination_city_uuid
            LEFT JOIN employee_history AS driver_1 
                ON driver_1.id = roadwarrant.driver_1
            LEFT JOIN employee_history AS driver_2 
                ON driver_2.id = roadwarrant.driver_2
            LEFT JOIN employee_history AS codriver 
                ON codriver.id = roadwarrant.codriver
            WHERE roadwarrant.uuid = ?
            ORDER BY roadwarrant.created_at DESC
            LIMIT 1
        ";

        return DB::selectOne($sql, [$uuid]);
    }

    public function scopeGetRoadWarrantAkap($query, $uuid)
    {
        $sql = "
            SELECT 
                roadwarrant.*,
                driver_1.id AS driver_1_id,
                driver_2.id AS driver_2_id,
                codriver.id AS codriver_id,
                CONCAT(driver_1.first_name, ' ', driver_1.second_name) AS driver_1_name,
                CONCAT(driver_2.first_name, ' ', driver_2.second_name) AS driver_2_name,
                CONCAT(codriver.first_name, ' ', codriver.second_name) AS codriver_name,
                transferto.first_name AS bank_account,
                transferto.bank_name,
                transferto.bank_number
            FROM ops_roadwarrant AS roadwarrant
            LEFT JOIN employee_history AS driver_1 
                ON driver_1.id = roadwarrant.driver_1
            LEFT JOIN employee_history AS driver_2 
                ON driver_2.id = roadwarrant.driver_2
            LEFT JOIN employee_history AS codriver 
                ON codriver.id = roadwarrant.codriver
            LEFT JOIN employee_history AS transferto 
                ON transferto.id = roadwarrant.transferto
            WHERE roadwarrant.uuid = ?
            LIMIT 1
        ";

        return DB::selectOne($sql, [$uuid]);
    }

    public function scopeGetEmployee($query)
    {
        $query = DB::table("employee_history AS employee")
            ->select('employee.*')
            ->orderBy('employee.first_name', 'ASC')
            ->get();

        return $query;
    }

    public function scopeGetAssignee($query, $date, $driverid)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.uuid')
            ->join("manifest", "manifest.uuid", "=", "roadwarrant.manifest_uuid")
            ->where('manifest.trip_date', $date)
            ->where(function ($query) use ($driverid) {
                $query->where('roadwarrant.driver_1', '=', $driverid)
                    ->orWhere('roadwarrant.driver_2', '=', $driverid)
                    ->orWhere('roadwarrant.codriver', '=', $driverid);
            })
            ->orderBy('roadwarrant.created_at', 'ASC')
            ->get();

        return $query;
    }

    public function scopeGetAssigneeAkap($query, $date, $driverid)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.uuid')
            ->join("v2_book AS book", "book.uuid", "=", "roadwarrant.manifest_uuid")
            ->where('book.start_date', '<=', $date)
            ->where('book.finish_date', '>=', $date)
            ->where(function ($query) use ($driverid) {
                $query->where('roadwarrant.driver_1', '=', $driverid)
                    ->orWhere('roadwarrant.driver_2', '=', $driverid)
                    ->orWhere('roadwarrant.codriver', '=', $driverid);
            })
            ->orderBy('roadwarrant.created_at', 'ASC')
            ->get();

        return $query;
    }

    public function scopeGetBookAvailable($query)
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        $query = DB::select("
            SELECT
                book.uuid,
                book.booking_code,
                book.start_date,
                book.finish_date,
                customer.name AS customer_name
            FROM v2_book AS book
            LEFT JOIN v2_customer AS customer ON customer.uuid = book.customer_uuid
            WHERE book.status = 0
            AND book.start_date BETWEEN ? AND ?
            ORDER BY book.start_date ASC
        ", [strval($startDate), strval($endDate)]);

        return $query;
    }

    public function scopeGetBook($query, $uuid)
    {
        $query = DB::table("v2_book AS book")
            ->select(
                'book.created_at',
                'book.uuid',
                'book.booking_code',
                'book.start_date',
                'book.finish_date',
                'book.pickup_address',
                'book.notes',
                'book.price',
                'book.discount',
                'book.tax',
                'book.total_price',
                'book.notes',
                'customer.name AS customer_name',
                'customer.email AS customer_email',
                'customer.phone AS customer_phone',
                'customer.address AS customer_address',
                'city_from.name as city_from',
                'city_to.name as city_to'
            )
            ->leftJoin("v2_customer AS customer", "customer.uuid", "=", "book.customer_uuid")
            ->leftJoin("v2_area_city AS city_from", "city_from.uuid", "=", "book.departure_city_uuid")
            ->leftJoin("v2_area_city AS city_to", "city_to.uuid", "=", "book.destination_city_uuid")
            ->where('book.uuid', $uuid)
            ->orderBy('book.created_at')
            ->first();

        return $query;
    }

    public function scopeGetBookBus($query, $book_uuid)
    {
        $query = DB::table("v2_book_bus AS bookbus")
            ->select('bus.name', 'bookbus.price', 'bookbus.bus_uuid', 'class.name as classname', 'class.seat')
            ->join('v2_bus AS bus', 'bus.uuid', '=', 'bookbus.bus_uuid')
            ->join("v2_class AS class", "class.uuid", "=", "bus.class_uuid")
            ->where('bookbus.book_uuid', $book_uuid)
            ->get();

        return $query;
    }

    public function scopeGetRoadWarrantCount($query)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderby('roadwarrant.count', 'DESC')
            ->first();

        return $query;
    }

    public function scopeSaveRoadWarrant($query, $data)
    {
        $query = DB::table("ops_roadwarrant")->insert($data);

        return $query;
    }

    public function scopeSaveManifest($query, $data)
    {
        $query = DB::table("manifest")->insert($data);

        return $query;
    }

    public function scopeUpdateBook($query, $uuid, $data)
    {
        $query = DB::table("v2_book")
            ->where('uuid', $uuid)
            ->update($data);

        return $query;
    }

    function scopeGetBusAkap($query)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid as busuuid', 'bus.name as busname', 'bus.registration_number')
            ->where('bus.category', 'AKAP')
            ->orderBy('busname', 'ASC')
            ->get();

        return $query;
    }

    function scopeGetBus($query, $bus_uuid)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid as busuuid', 'bus.name as busname', 'bus.registration_number')
            ->where('bus.uuid', $bus_uuid)
            ->orderBy('busname', 'ASC')
            ->first();

        return $query;
    }

    function scopeGetManifest($query, $manifest_uuid)
    {
        $query = DB::table("manifest")
            ->where('uuid', $manifest_uuid)
            ->first();

        return $query;
    }

    function scopeGetManifestTrip($query, $roadwarrant_uuid)
    {
        $query = DB::table("manifest")
            ->select(
                'manifest.uuid',
                'manifest.status',
                'manifest.trip_date',
                'manifest.trip_assign',
                'trip.trip_title',
            )
            ->leftJoin("trip_assign AS tras", "tras.id", "=", "manifest.trip_assign")
            ->leftJoin("trip", "trip.trip_id", "=", "tras.trip")
            ->where('manifest.roadwarrant_uuid', $roadwarrant_uuid)
            ->get();

        return $query;
    }

    public function scopeGetManifestByTras($query, $tras, $date)
    {
        $sql = "
            SELECT manifest.id
            FROM manifest
            WHERE manifest.trip_assign = ?
            AND manifest.trip_date = ?
            AND manifest.roadwarrant_uuid IS NOT NULL
            ORDER BY manifest.id DESC
        ";

        return DB::select($sql, [$tras, $date]);
    }

    public function scopeGetManifestByTrasBefore($query, $tras, $date, $busUuid)
    {
        $sql = "
            SELECT 
                manifest.id,
                manifest.roadwarrant_uuid,
                road.numberid,
                manifest.trip_date
            FROM manifest
            INNER JOIN ops_roadwarrant AS road 
                ON road.uuid = manifest.roadwarrant_uuid
            WHERE road.status IN (1,2,3,4)
            AND manifest.roadwarrant_uuid IS NOT NULL
            AND manifest.roadwarrant_uuid <> ''
            AND manifest.trip_assign = ?
            AND manifest.trip_date < ?
            AND road.bus_uuid = ?
            ORDER BY manifest.trip_date ASC
        ";

        return DB::select($sql, [$tras, $date, $busUuid]);
    }

    function scopeGetTripAssign($query, $trasid)
    {
        $query = DB::table("trip_assign AS tras")
            ->select(
                'tras.*',
                'trip.trip_title',
            )
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            // ->where('tras.status','1')
            ->where('tras.id', $trasid)
            ->first();

        return $query;
    }

    function scopeGetBusClass($query, $bus_uuid)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('class.name', 'class.seat', 'class.layout')
            ->join("v2_bus_class as busclass", "busclass.bus_uuid", "=", "bus.uuid")
            ->join("v2_class as class", "class.uuid", "=", "busclass.class_uuid")
            ->where('bus.uuid', $bus_uuid)
            ->get();

        return $query;
    }

    public function scopeGetExpensesList($query, $id)
    {
        $query = DB::table('trip_expenses as expense')
            ->select(
                'expense.*',
            )
            ->where('expense.roadwarrant_uuid', $id)
            ->orderBy('expense.id', 'ASC')
            ->get();

        return $query;
    }

    public function scopeGetExpensesListAkap($query, $id)
    {
        $query = DB::table('trip_expenses as expense')
            ->select(
                'expense.*',
                'trip.trip_title',
            )
            ->where('expense.roadwarrant_uuid', $id)
            ->join("manifest", "manifest.uuid", "=", "expense.manifest_uuid")
            ->join("trip_assign as tras", "tras.id", "=", "manifest.trip_assign")
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->orderBy('expense.id', 'ASC')
            ->get();

        return $query;
    }

    public function scopeGetExpense($query, $id)
    {
        $query = DB::table("trip_expenses")
            ->where('id', $id)
            ->first();

        return $query;
    }

    public function scopeUpdateExpense($query, $id, $data)
    {
        $query = DB::table("trip_expenses")
            ->where('id', $id)
            ->update($data);

        return $query;
    }

    public function scopeUpdateRoadWarrant($query, $uuid, $data)
    {
        $query = DB::table("ops_roadwarrant")
            ->where('uuid', $uuid)
            ->update($data);

        return $query;
    }

    public function scopeSaveBoardingPuloGebang($query, $data)
    {
        $query = DB::table("boarding_pulogebang")->insert($data);

        return $query;
    }

    public function scopeSaveWithdraw($query, $data)
    {
        $query = DB::table("withdraw")->insert($data);

        return $query;
    }

    public function scopeGetWithdraw($query, $uuid)
    {
        $query = DB::table("withdraw")
            ->where('parent_uuid', $uuid)
            ->first();

        return $query;
    }

    public function scopeRemoveManifest($query, $uuid)
    {
        $query = DB::table("manifest")
            ->where('roadwarrant_uuid',$uuid)
            ->delete();

        return $query;
    }

    public function scopeDeleteRoadWarrant($query, $uuid)
    {
        return DB::table("ops_roadwarrant")
            ->where('uuid', $uuid)
            ->delete();
    }

    public function scopeDeleteRoadWarrantsByManifest($query, $manifest_uuid)
    {
        return DB::table("ops_roadwarrant")
            ->where('manifest_uuid', $manifest_uuid)
            ->delete();
    }

    public function scopeSaveBookPayment($query, $data)
    {
        return DB::table('v2_book_payment')->insert($data);
    }

    public function scopeGetBookPayments($query, $book_uuid)
    {
        return DB::table('v2_book_payment')
            ->where('book_uuid', $book_uuid)
            ->get();
    }
}
