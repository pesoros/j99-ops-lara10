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
          <div class="col-sm-12 invoice-col">
            <p class="lead">Detail perjalanan</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th width="250">Status SPJ:</th>
                  <td>
                    @if (STRVAL($roadwarrant->status) === '1')
                      <span class="badge badge-warning">Aktif</span>                                        
                    @elseif (STRVAL($roadwarrant->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
                    @endif
                  </td>
                </tr>
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
                <tr>
                  <th>Kelas</th>
                  <td>
                    @foreach ($busclass as $item)
                      <div class="table-responsive">
                        <p class="lead">{{ $item->name }}</p>
                        <table class="table">
                          <tr>
                            <th>Jumlah kursi :</th>
                            <td>{{ $item->seat }}</td>
                          </tr>
                          <tr>
                            <th width="250">Layout :</th>
                            <td>{{ $item->layout }}</td>
                          </tr>
                        </table>
                      </div>
                    @endforeach
                  </td>
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
                  <th>Co-driver :</th>
                  <td>{{ $roadwarrant->codriver_name }}</td>
                </tr>
                <tr>
                  <th>Uang Saku :</th>
                  <td>{{ formatAmount($roadwarrant->trip_allowance) }}</td>
                </tr>
                <tr>
                  <th>Uang Bensin :</th>
                  <td>{{ formatAmount($roadwarrant->fuel_allowance) }}</td>
                </tr>
                <tr>
                  <th>Uang Premi Driver 1 :</th>
                  <td>{{ formatAmount($roadwarrant->driver_allowance_1) }}</td>
                </tr>
                <tr>
                  <th>Uang Premi Driver 2 :</th>
                  <td>{{ formatAmount($roadwarrant->driver_allowance_2) }}</td>
                </tr>
                <tr>
                  <th>Uang Premi Co-driver :</th>
                  <td>{{ formatAmount($roadwarrant->codriver_allowance) }}</td>
                </tr>
                <tr>
                  <th>Uang Makan Crew :</th>
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
                @php $summary = 0; @endphp
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
                        href="{{ url('letter/roadwarrantold/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/2') }}"
                        class="btn btn-xs btn-success"
                      >Terima</a>
                      <a 
                        href="{{ url('letter/roadwarrantold/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/0') }}"
                        class="btn btn-xs btn-danger"
                      >Tolak</a>
                      <a 
                        href="{{ url('letter/roadwarrantold/expense/edit/'.$expense->id) }}"
                        class="btn btn-xs btn-warning"
                      >Edit</a>
                    </td>
                  </tr>
                  @if ($expense->action == 'spend') 
                      @if ($expense->status == 2)
                        @php $summary = $summary - $expense->nominal; @endphp
                      @endif
                  @else
                      @if ($expense->status == 2)
                        @php $summary = $summary + $expense->nominal; @endphp
                      @endif
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      <div class="row">
        <div class="col-12">
          <p><strong>SISA UANG: Rp. {{$summary}}</strong></p>
        </div>
      </div>
      <div class="row no-print">
        <div class="col-12">
          <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
        </div>
      </div>
      <br>
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