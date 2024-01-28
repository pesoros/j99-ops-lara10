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
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail complaint</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama bus :</th>
                  <td>{{ $bus->name }}</td>
                </tr>
                <tr>
                  <th>Nomor registrasi :</th>
                  <td>{{ $bus->registration_number }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-6 invoice-col">
            <p class="lead">&nbsp;</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Merk :</th>
                  <td>{{ $bus->brand }}</td>
                </tr>
                <tr>
                  <th>Model :</th>
                  <td>{{ $bus->model }}</td>
                </tr>
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        @if (COUNT($damages) > 0)
          <div class="row">
            <div class="col-12 table-responsive">
              <p class="lead">Kerusakan</p>
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Bagian</th>
                  <th>Deskripsi</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($damages as $key => $damage)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $damage->areacode }}-{{ $damage->scopecode }} | {{ $damage->scopename }}</td>
                      <td>{{ $damage->description }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.col -->
          </div>
        @endif
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      <div>
        @if (permissionCheck('add'))
          <a href="{{ url('letter/complaint/add/createworkorder/'.$bus->uuid) }}" onclick="return confirm('Anda yakin membuat SPK berdasarkan keluhan ini?')" class="btn bg-gradient-primary btn-sm">Buat SPK berdasarkan keluhan ini</a>
        @endif
      </div>
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
