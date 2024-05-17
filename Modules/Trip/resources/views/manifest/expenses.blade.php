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
        <!-- title row -->
        <div class="row">
          <div class="col-12">
            <h4>
            <img src="{{url('assets/images/logo/j99-logo-wide.png')}}" alt="J99 Logo" height="38" style="opacity: .8">
            <a href="{{ url('trip/manifest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success float-right mr-1">Kembali</a>
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail manifest</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama trip :</th>
                  <td>
                    {{ $detailManifest->trip_title }}
                  </td>
                </tr>
                <tr>
                  <th>Tanggal keberangkatan :</th>
                  <td>
                    {{ dateFormat($detailManifest->trip_date) }}
                  </td>
                </tr>
                <tr>
                  <th>Status :</th>
                  <td>
                    @if (STRVAL($detailManifest->status) === '1')
                      <span class="badge badge-warning">Aktif</span>                                        
                    @endif
                    @if (STRVAL($detailManifest->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-6 invoice-col">
            <p class="lead">&nbsp;</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Armada :</th>
                  <td>
                    {{ $detailManifest->fleetname ?? $detailManifest->busname }}
                  </td>
                </tr>
                <tr>
                  <th>Driver 1:</th>
                  <td>
                    {{ $detailManifest->driver1_name }} {{ $detailManifest->driver1_lastname }}
                  </td>
                </tr>
                <tr>
                  <th>Driver 2 :</th>
                  <td>
                    {{ $detailManifest->driver2_name }} {{ $detailManifest->driver2_lastname }}
                  </td>
                </tr>
                <tr>
                  <th>Co Driver :</th>
                  <td>
                    {{ $detailManifest->codriver_name }} {{ $detailManifest->codriver_lastname }}
                  </td>
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
            <p class="lead">Laporan transaksi</p>
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>File</th>
                <th>Aksi</th>
              </tr>
              </thead>
              <tbody>

                <td></td>
                <td>Saldo awal</td>
                <td></td>
                <td></td>
                <td>{{ formatAmount($detailManifest->allowance) }}</td>
                <td></td>
                <td></td>
                <td></td>

                @foreach ($expensesList as $key => $expense)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->created_at }}</td>
                    <td>{{ $expense->action }}</td>
                    <td>{{ formatAmount($expense->nominal) }}</td>
                    <td>{{ $expense->status == 1 ? "-" : (($expense->status == 0) ? "Ditolak" : "Diterima") }}</td>
                    <td>
                      @if (!empty($expense->file))
                        <img 
                          src="{{ env('BACKEND_URL').'uploads/manifest/expense/'.$expense->file }}"
                          class="img" 
                          width="150" 
                          height="150"
                        >
                      @else
                        <img 
                          src="{{ env('ADMINV1_URL').'assets/img/icons/empty.jpg' }}"
                          class="img"
                          width="150" 
                          height="150" 
                          style="object-fit: cover;"
                        >
                      @endif
                    </td>
                    <td>
                      <a 
                        href="{{ url('trip/manifest/expense/accept/'.$expense->id) }}"
                        class="btn btn-xs btn-success"
                      >Terima</a>
                      <a 
                        href="{{ url('trip/manifest/expense/reject/'.$expense->id) }}"
                        class="btn btn-xs btn-danger"
                      >Tolak</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div>
          
        </div>
      </div>
      <!-- /.invoice -->
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
