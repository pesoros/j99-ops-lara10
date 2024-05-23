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
            @if (STRVAL($detailManifest->status) === '1')
            <a href="{{ url('trip/manifest/close/'.$detailManifest->id) }}" onclick="return confirm('Anda yakin menyelesaikan Manifest ini?')" class="btn bg-gradient-primary float-right no-print">Selesaikan manifest ini</a>
            @else
            <a href="{{ url('trip/manifest/open/'.$detailManifest->id) }}" onclick="return confirm('Anda yakin mermbuks Manifest ini?')" class="btn bg-gradient-danger float-right no-print">Aktifkan kembali manifest ini</a>
            @endif
            <a href="{{ url('trip/manifest/broadcast/'.$detailManifest->id) }}" onclick="return confirm('Anda yakin broadcast WA penumpang pada manifest ini?')" class="btn bg-gradient-success float-right no-print">Selesaikan manifest ini</a>
            <a href="{{ url('trip/manifest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success float-right mr-1 no-print">Kembali</a>
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
            <p class="lead">List penumpang</p>
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Nama</th>
                <th>Kode booking</th>
                <th>Nomor tiket</th>
                <th>Kursi</th>
                <th>Makanan</th>
                <th>Titik jemput</th>
                <th>Titik turun</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($passengerList as $key => $passenger)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $passenger->name }} <br> <i class="fas fa-phone"></i> {{ numberSpacer($passenger->phone) }}</td>
                    <td>{{ $passenger->booking_code }}</td>
                    <td>{{ $passenger->ticket_number }}</td>
                    <td>{{ $passenger->seat_number }} | {{ $passenger->class }}</td>
                    <td>{{ $passenger->food_name }}</td>
                    <td>{{ $passenger->pickup_trip_location }} {{ substr($passenger->dep_time, 0, 5) }}</td>
                    <td>{{ $passenger->drop_trip_location }} {{ substr($passenger->arr_time, 0, 5) }}</td>
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
      <div class="row no-print">
        <div class="col-12">
          <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
        </div>
      </div>
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
@push('extra-scripts')
<script type="text/javascript">
    $(function () {
      $('a.printPage').click(function(){
           window.print();
           return false;
      });
    });
</script>
@endpush