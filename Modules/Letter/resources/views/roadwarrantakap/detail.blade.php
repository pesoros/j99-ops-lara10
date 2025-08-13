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

              @if (intval($roadwarrant->status) < 3)
                @if (permissionCheck('edit')) <a href="{{ url('letter/roadwarrant/edit/1/'.$roadwarrant->uuid) }}" class="btn btn-secondary float-right" style="margin-left: 4px;">Edit</a> @endif
              @endif

              @if ($roadwarrant->manifest_uuid == NULL)
                @if (intval($roadwarrant->status) == 1 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'accounting'))
                  <a href="{{ url('letter/roadwarrant/status/marker/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin?')" class="btn bg-gradient-info float-right">Set Marker</a>
                @endif
                @if (intval($roadwarrant->status) == 2 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'operational'))
                  <a href="{{ url('letter/roadwarrant/status/active/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin?')" class="btn bg-gradient-primary float-right">Set Aktif</a>
                @endif
                @if (intval($roadwarrant->status) == 3 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'accounting'))
                  <a href="{{ url('letter/roadwarrant/withdraw/1/'.$roadwarrant->uuid) }}" class="btn bg-gradient-success float-right">Transfer uang perjalanan</a>
                @endif
                @if (intval($roadwarrant->status) == 5 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'operational' || $roleInfo->role_slug == 'accounting'))
                  <a href="{{ url('letter/roadwarrant/accurate/lpj/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin?')" class="btn bg-gradient-warning float-right">Lapor LPJ perjalanan</a>
                @endif
              @endif
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
                  <th>Tujuan transfer :</th>
                  <td>
                    {{ $roadwarrant->bank_account }} | {{ $roadwarrant->bank_name }} - {{ $roadwarrant->bank_number }}
                    @if (intval($roadwarrant->transferto_changed) === 1 && intval($roadwarrant->status) <= 3)
                      <span class="badge badge-warning">Terdapat perubahan</span>                                  
                    @endif
                  </td>
                </tr>
                <tr>
                  <th width="250">Status SPJ: </th>
                  <td>
                    @if ($roadwarrant->manifest_uuid == NULL)
                      @if (intval($roadwarrant->status) === 1)
                        <span class="badge badge-secondary">Draft</span>                                  
                      @elseif (intval($roadwarrant->status) === 2)
                        <span class="badge badge-info">Marker</span>
                      @elseif (intval($roadwarrant->status) === 3)
                        <span class="badge badge-primary">Aktif</span>
                      @elseif (intval($roadwarrant->status) === 4)
                        <span class="badge badge-success">Sudah di transfer</span>
                      @elseif (intval($roadwarrant->status) === 5)
                        <span class="badge badge-danger">Perjalanan selesai</span>
                      @elseif (intval($roadwarrant->status) === 6)
                        <span class="badge bg-orange">SPJ Selesai</span>
                      @endif
                    @else
                      @if (intval($roadwarrant->status) === 1)
                        <span class="badge badge-warning">Aktif</span>
                      @elseif (intval($roadwarrant->status) === 2)
                        <span class="badge badge-success">Selesai</span>
                      @endif
                    @endif
                  </td>
                </tr>
                @if (intval($roadwarrant->status) >= 4)
                    <tr>
                      <th width="250">Bukti transfer uang jalan :</th>
                      <td>
                        <a href="{{ url('letter/roadwarrant/withdraw/1/'.$roadwarrant->uuid) }}" class="btn btn-sm btn-secondary" style="margin-left: 4px;">Check</a>
                      </td>
                    </tr>
                @endif
                <tr>
                  <th width="250">Nomor SPJ :</th>
                  <td>{{ $roadwarrant->numberid }}</td>
                </tr>
                <tr>
                  <th>Tanggal perjalanan :</th>
                  <td>
                    @foreach ($manifest as $key => $item)
                      @if ($key > 0) <br> @endif
                      {{ dateFormat($item->trip_date) }} | {{ $item->trip_title }}
                    @endforeach
                  </td>
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
                  <td>{{ $bus->busname }} {{ $roadwarrant->is_replacement_bus ? '(Bus Pengganti)' : '' }}</td>
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
                @if ($crewCount > 2)
                  <tr>
                    <th>Driver 2 :</th>
                    <td>{{ $roadwarrant->driver_2_name }}</td>
                  </tr>
                @endif
                <tr>
                  <th>Co Driver :</th>
                  <td>{{ $roadwarrant->codriver_name }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Tabel uang jalan SPJ</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th width="100">No</th>
                          <th>Judul</th>
                          <th width="190" class="text-right">Nominal</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>Uang makan crew (<span id="crewcount">{{ $crewCount }}</span> orang)</td>
                          <td class="text-right crew_meal_allowance">{{ formatAmount($roadwarrant->crew_meal_allowance) }}</td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>Uang premi driver <span id="drivercount">{{ intval($crewCount) > 2 ? '1 dan 2' : '1' }}</span></td>
                          <td class="text-right driver_allowance">{{ formatAmount($roadwarrant->driver_allowance_1) }}</td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>Uang premi co driver</td>
                          <td class="text-right codriver_allowance">{{ formatAmount($roadwarrant->codriver_allowance) }}</td>
                        </tr>
                        <tr>
                          <td>4</td>
                          <td>Uang saku</td>
                          <td class="text-right trip_allowance">
                            {{ formatAmount($roadwarrant->trip_allowance) }}
                          </td>
                        </tr>
                        <tr>
                          <td>5</td>
                          <td>Uang BBM</td>
                          <td class="text-right fuel_allowance">{{ formatAmount($roadwarrant->fuel_allowance) }}</td>
                        </tr>
                        <tr>
                          <td>6</td>
                          <td>Uang E-Toll</td>
                          <td class="text-right etoll_allowance">{{ formatAmount($roadwarrant->etoll_allowance) }}</td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td class="text-right" colspan="2">Total biaya :</td>
                          <td class="text-right totalsum">{{ formatAmount($roadwarrant->total_allowance) }}</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="crew_meal_allowance" id="crew_meal_allowance">
              <input type="hidden" class="form-control" name="driver_allowance" id="driver_allowance">
              <input type="hidden" class="form-control" name="codriver_allowance" id="codriver_allowance" >
              <input type="hidden" class="form-control" name="fuel_allowance" id="fuel_allowance">
              <input type="hidden" class="form-control" name="etoll_allowance" id="etoll_allowance">
              <input type="hidden" class="form-control" name="totalsum" id="totalsum">
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
                <th class="no-print">Aksi</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Trip</th>
                <th>Koordinat (lat, long)</th>
                <th>Status</th>
                <th>File</th>
                <th>Kategori</th>
                <th>Nominal</th>
              </tr>
              </thead>
              <tbody>
                @php $summary = 0; @endphp
                @foreach ($expensesList as $key => $expense)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="no-print">
                      <a 
                        href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/2') }}"
                        class="btn btn-xs btn-success"
                      >Terima</a>
                      <a 
                        href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/0') }}"
                        class="btn btn-xs btn-danger"
                      >Tolak</a>
                      <a 
                        href="{{ url('letter/roadwarrant/expense/edit/'.$expense->id) }}"
                        class="btn btn-xs btn-warning"
                      >Edit</a>
                    </td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->created_at }}</td>
                    <td>{{ $expense->trip_title }}</td>
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
                    <td>
                      @if ($expense->action == 'income')
                          Pemasukan
                      @endif
                      @if ($expense->action == 'spend')
                          Pengeluaran
                      @endif
                    </td>
                    <td>{{ formatAmount($expense->nominal) }}</td>
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
              <tfoot>
                <tr>
                  <td class="text-right" colspan="9">Total Pemasukan :</td>
                  <td class="text-right totalsum">{{ formatAmount($incomeSum) }}</td>
                </tr>
                <tr>
                  <td class="text-right" colspan="9">Total Pengeluaran :</td>
                  <td class="text-right totalsum">{{ formatAmount($spendSum) }}</td>
                </tr>
                <tr>
                  <td class="text-right" colspan="9">Total Pemakaian :</td>
                  <td class="text-right totalsum">{{ formatAmount($totalSum) }}</td>
                </tr>
                <tr>
                  <td class="text-right" colspan="9">Uang Jalan :</td>
                  <td class="text-right totalsum">{{ formatAmount($roadwarrant->total_allowance) }}</td>
                </tr>
                <tr>
                  <td class="text-right" colspan="9">Sisah uang :</td>
                  <td class="text-right totalsum"><b>{{ formatAmount($restMoney) }}</b></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      {{-- <div class="row">
        <div class="col-12">
          <p><strong>SISA UANG: Rp. {{$summary}}</strong></p>
        </div>
      </div> --}}
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