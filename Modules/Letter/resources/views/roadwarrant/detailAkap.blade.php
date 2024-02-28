@extends('layouts.main', ['title' => $title ])

@section('content')
@if (session('success'))
  <div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
  {{ session('success') }}
  </div>
@endif

@if (session('failed'))
  <div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
  {{ session('failed') }}
  </div>
@endif
  <div class="row">
    <div class="col-12">
      <!-- Main content -->
      <div class="invoice p-3 mb-3">
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-12 invoice-col">
            <p class="lead">Detail perjalanan</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th width="250">Nomor SPJ :</th>
                  <td>{{ $roadwarrant->numberid }}</td>
                </tr>
                <tr>
                  <th>Tanggal keberangkatan :</th>
                  <td>{{ dateFormat($manifest->trip_date) }}</td>
                </tr>
                <tr>
                  <th>Nama trip :</th>
                  <td>{{ $tras->trip_title }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-12 invoice-col">
            <p class="lead">Detail armada</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th width="250">Nama bus :</th>
                  <td>{{ $bus->busname }}</td>
                </tr>
                {{-- <tr>
                  <th>Driver 1 :</th>
                  <td>{{ $roadwarrant->driver_1_name }}</td>
                </tr>
                <tr>
                  <th>Driver 2 :</th>
                  <td>{{ $roadwarrant->driver_2_name }}</td>
                </tr>
                <tr>
                  <th>Co driver :</th>
                  <td>{{ $roadwarrant->codriver_name }}</td>
                </tr> --}}
                <tr>
                  <th>Uang saku :</th>
                  <td>{{ formatAmount($tras->allowance) }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      {{-- <div>
        @if (permissionCheck('add'))
          <a href="{{ url('letter/complaint/add/createworkorder/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin membuat SPK berdasarkan keluhan ini?')" class="btn bg-gradient-primary btn-sm">Buat SPK berdasarkan keluhan ini</a>
        @endif
      </div> --}}
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
