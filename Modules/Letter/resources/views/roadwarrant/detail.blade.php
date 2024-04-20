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
        <div class="row">
          <div class="col-12">
            <h4>
              <img src="{{url('assets/images/logo/j99-logo-wide.png')}}" alt="J99 Logo" height="38" style="opacity: .8">
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail reservasi</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th width="250">Nomor SPJ :</th>
                  <td>{{ $roadwarrant->numberid }}</td>
                </tr>
                <tr>
                  <th width="250">Kode booking :</th>
                  <td>{{ $roadwarrant->booking_code }}</td>
                </tr>
                <tr>
                  <th>Nama customer :</th>
                  <td>{{ $roadwarrant->customer_name }}</td>
                </tr>
                <tr>
                  <th>Telephone customer :</th>
                  <td>{{ numberSpacer($roadwarrant->customer_phone) }}</td>
                </tr>
                <tr>
                  <th>Tanggal berangkat :</th>
                  <td>{{ dateTimeFormat($roadwarrant->start_date) }}</td>
                </tr>
                <tr>
                  <th>tanggal kembali :</th>
                  <td>{{ dateTimeFormat($roadwarrant->finish_date) }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-6 invoice-col">
            <p class="lead">&nbsp;</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Tanggal pemesanan :</th>
                  <td>{{ dateTimeFormat($roadwarrant->created_at) }}</td>
                </tr>
                <tr>
                  <th>Alamat penjemputan :</th>
                  <td>{{ $roadwarrant->pickup_address }}</td>
                </tr>
                <tr>
                  <th>Kota penjemputan :</th>
                  <td>{{ $roadwarrant->city_from }}</td>
                </tr>
                <tr>
                  <th>Kota tujuan :</th>
                  <td>{{ $roadwarrant->city_to }}</td>
                </tr>
                <tr>
                  <th>Catatan :</th>
                  <td>{{ $roadwarrant->notes }}</td>
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
                  <td>{{ $roadwarrant->busname }}</td>
                </tr>
                <tr>
                  <th>Kelas :</th>
                  <td>{{ $roadwarrant->classname }}</td>
                </tr>
                <tr>
                  <th>Jumlah kursi :</th>
                  <td>{{ $roadwarrant->seat }} Kursi</td>
                </tr>
                <tr>
                  <th width="250">Kilometer awal :</th>
                  <td>{{ $roadwarrant->km_start ? 'Km '.$roadwarrant->km_start : '-' }}</td>
                </tr>
                <tr>
                  <th width="250">Kilometer akhir :</th>
                  <td>{{ $roadwarrant->km_end ? 'Km '.$roadwarrant->km_end : '-' }}</td>
                </tr>
                <tr>
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
                </tr>
                <tr>
                  <th>Uang saku :</th>
                  <td>{{ formatAmount($roadwarrant->trip_allowance) }}</td>
                </tr>
                <tr>
                  <th>Uang premi driver 1 :</th>
                  <td>{{ formatAmount($roadwarrant->driver_allowance_1) }}</td>
                </tr>
                <tr>
                  <th>Uang premi Driver 2 :</th>
                  <td>{{ formatAmount($roadwarrant->driver_allowance_2) }}</td>
                </tr>
                <tr>
                  <th>Uang premi Co driver :</th>
                  <td>{{ formatAmount($roadwarrant->codriver_allowance) }}</td>
                </tr>
                <tr>
                  <th>Uang makan kru :</th>
                  <td>{{ formatAmount($roadwarrant->crew_meal_allowance) }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <div class="invoice p-3 mb-3">
        <!-- info row -->
        <div class="row invoice-info">
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
                <th>Koordinat (lat, long)</th>
                <th>Status</th>
                <th>File</th>
                <th class="no-print">Aksi</th>
              </tr>
              </thead>
              <tbody>

                <td></td>
                <td>Saldo awal</td>
                <td></td>
                <td></td>
                <td>{{ formatAmount($roadwarrant->trip_allowance) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="no-print"></td>

                @foreach ($expensesList as $key => $expense)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->created_at }}</td>
                    <td>{{ $expense->action }}</td>
                    <td>{{ formatAmount($expense->nominal) }}</td>
                    <td> 
                      @if ($expense->location_lat)
                      <a href="https://www.google.com/maps/search/{{ $expense->location_lat }},{{ $expense->location_long }}?sa=X&ved=1t:242&ictx=111" target="_blank">{{ $expense->location_lat }}, {{ $expense->location_long }}</a>
                      @else
                      -
                      @endif 
                    </td>
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
                    <td class="no-print">
                      <a 
                        href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/2') }}"
                        class="btn btn-xs btn-success"
                      >Terima</a>
                      <a 
                        href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/0') }}"
                        class="btn btn-xs btn-danger"
                      >Tolak</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      <div class="row no-print">
        <div class="col-12">
          <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
        </div>
      </div>
      <br>
      {{-- <div>
        @if (permissionCheck('add'))
          <a href="{{ url('letter/complaint/add/createworkorder/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin membuat SPK berdasarkan keluhan ini?')" class="btn bg-gradient-primary btn-sm">Buat SPK berdasarkan keluhan ini</a>
        @endif
      </div> --}}
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