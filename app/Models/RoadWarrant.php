<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoadWarrant extends Model
{
    public function scopeGetRoadWarrantList($query)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.*','bus.name AS busname')
            ->join("v2_bus AS bus", "bus.uuid", "=", "roadwarrant.bus_uuid")
            ->orderBy('roadwarrant.numberid','DESC')
            ->get();

        return $query;
    }

    public function scopeGetRoadWarrant($query, $uuid)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select(
                'book.*',
                'roadwarrant.*',
                'bus.name AS busname',
                'class.name as classname',
                'class.seat',
                'customer.name AS customer_name',
                'customer.phone AS customer_phone',
                'city_from.name as city_from',
                'city_to.name as city_to',
                DB::raw("CONCAT(driver_1.first_name,' ',driver_1.second_name) as driver_1_name"),
                DB::raw("CONCAT(driver_2.first_name,' ',driver_2.second_name) as driver_2_name"),
                DB::raw("CONCAT(codriver.first_name,' ',codriver.second_name) as codriver_name"),
            )
            ->join("v2_bus AS bus", "bus.uuid", "=", "roadwarrant.bus_uuid")
            ->join("v2_book AS book", "book.uuid", "=", "roadwarrant.manifest_uuid")
            ->join("v2_customer AS customer", "customer.uuid", "=", "book.customer_uuid")
            ->join("v2_class AS class", "class.uuid", "=", "bus.class_uuid")
            ->leftJoin("v2_area_city AS city_from", "city_from.uuid", "=", "book.departure_city_uuid")
            ->leftJoin("v2_area_city AS city_to", "city_to.uuid", "=", "book.destination_city_uuid")
            ->leftJoin("employee_history AS driver_1", "driver_1.id", "=", "roadwarrant.driver_1")
            ->leftJoin("employee_history AS driver_2", "driver_2.id", "=", "roadwarrant.driver_2")
            ->leftJoin("employee_history AS codriver", "codriver.id", "=", "roadwarrant.codriver")
            ->where('roadwarrant.uuid', $uuid)
            ->orderBy('roadwarrant.created_at','DESC')
            ->first();

        return $query;
    }

    public function scopeGetEmployee($query)
    {
        $query = DB::table("employee_history AS employee")
            ->select('employee.*')
            ->orderBy('employee.first_name','ASC')
            ->get();

        return $query;
    }

    public function scopeGetBookAvailable($query)
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(90);

        $query = DB::table("v2_book AS book")
            ->select(
                'book.uuid',
                'book.booking_code',
                'book.start_date',
                'book.finish_date',
                'customer.name as customer_name',
                'city_from.name as city_from',
                'city_to.name as city_to'
            )
            ->leftJoin("v2_customer AS customer", "customer.uuid", "=", "book.customer_uuid")
            ->leftJoin("v2_area_city AS city_from", "city_from.uuid", "=", "book.departure_city_uuid")
            ->leftJoin("v2_area_city AS city_to", "city_to.uuid", "=", "book.destination_city_uuid")
            ->where('book.status',0)
            ->whereBetween('book.start_date', [$startDate, $endDate])
            ->orderBy('book.start_date')
            ->get();

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
            ->where('book.uuid',$uuid)
            ->orderBy('book.created_at')
            ->first();

        return $query;
    }

    public function scopeGetBookBus($query, $book_uuid)
    {
        $query = DB::table("v2_book_bus AS bookbus")
            ->select('bus.name','bookbus.price', 'bookbus.bus_uuid', 'class.name as classname','class.seat')
            ->join('v2_bus AS bus', 'bus.uuid', '=', 'bookbus.bus_uuid')
            ->join("v2_class AS class", "class.uuid", "=", "bus.class_uuid")
            ->where('bookbus.book_uuid',$book_uuid)
            ->get();

        return $query;
    }

    public function scopeGetRoadWarrantCount($query)
    {
        $query = DB::table("ops_roadwarrant AS roadwarrant")
            ->select('roadwarrant.count')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->orderby('roadwarrant.count','DESC')
            ->first();

        return $query;
    }

    public function scopeSaveRoadWarrant($query, $data)
    {
        $query = DB::table("ops_roadWarrant")->insert($data);

        return $query;
    }

    public function scopeUpdateBook($query, $uuid, $data)
    {
        $query = DB::table("v2_book")
            ->where('uuid',$uuid)
            ->update($data);

        return $query;
    }

    function scopeGetTripAssign()
    {
        $query = DB::table("trip_assign AS tras")
            ->select('tras.id as trasid','tras.trip as trip','trip.trip_title')
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->orderBy('trip.trip_title','ASC')
            ->get();

        return $query;
    }
}
