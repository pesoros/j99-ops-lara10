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
            <p class="lead">Detail Bus</p>
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
            @if (permissionCheck('add'))
              <div class="float-right">
                @if (isset($workorder->numberid))
                  <p class="btn bg-gradient-primary btn-sm">{{ $workorder->numberid }}</p>
                @else
                  <p><a href="{{ url('letter/complaint/add/createworkorder/'.$bus->uuid) }}" onclick="return confirm('Anda yakin membuat SPK berdasarkan keluhan ini?')" class="btn bg-gradient-primary btn-sm">Buat SPK berdasarkan keluhan ini</a></p>
                @endif
              </div>
            @endif
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
        <div class="row">
          <div class="col-12 table-responsive">
            <p class="lead">Kerusakan</p>
            @if (COUNT($damages) > 0)
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Bagian</th>
                  <th>Deskripsi</th>
                  <th>Dibuat oleh</th>
                  <th>Tanggal lapor</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($damages as $key => $damage)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $damage->areacode }}-{{ $damage->scopecode }} | {{ $damage->scopename }}</td>
                      <td>{{ $damage->description }}</td>
                      <td>{{ $damage->creator }}</td>
                      <td>{{ dateFormat($damage->created_at) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="text-center"><p>Belum ada laporan kerusakan</p></div>
            @endif
            </div>
          </div>
      </div>
      @if (!isset($workorder->numberid))
        <div class="card card-primary">
          <form action="{{ url('letter/complaint/add') }}" method="post">
            @csrf
            <div class="card-body row">
              <input type="hidden" class="form-control" id="bus_uuid" name="bus_uuid" value="{{ $bus->uuid }}">
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="bus">Tambah laporan kerusakan</label>
                  <div class="row">
                    <div class="col-sm-3">
                      <select class="form-control select2bs4" name="damage_scope" style="width: 100%;">
                        @foreach ($partsscope as $partsscopeItem)
                            <option value="{{ $partsscopeItem->uuid }}" @selected(old('partsscope_uuid') == $partsscopeItem->uuid)>
                                {{ $partsscopeItem->scope_name }} | {{ $partsscopeItem->name }} | {{ $partsscopeItem->scope_code }}-{{ $partsscopeItem->code }}
                            </option>
                        @endForeach
                      </select>
                    </div>
                    <div class="col-sm-7">
                      <div class="input-group mb-3">
                        <textarea class="form-control damage_detail" name="damage_detail" rows="1"  placeholder="Masukkan detail kerusakan" required></textarea>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <button type="submit" class="btn btn-success">Tambah</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      @endif
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
