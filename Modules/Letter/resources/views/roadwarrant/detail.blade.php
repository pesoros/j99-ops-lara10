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

              @if (intval($roadwarrant->status) == 0 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'operational'))
                  <a href="{{ url('letter/roadwarrant/status/waitingmarker/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin?')" class="btn bg-gradient-secondary float-right">Set Waiting to Marker</a>
                @endif
                @if (intval($roadwarrant->status) == 1 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'accounting'))
                  @if ($isMarkerReady == false)
                  <button 
                    type="button" 
                    class="btn bg-gradient-danger float-right" 
                    data-toggle="modal" 
                    data-target="#numberidModal"
                  >
                    Marker Block
                  </button>
                  @else
                    <button type="button" class="btn bg-gradient-info float-right" data-toggle="modal" data-target="#markerPaymentModal">Set Marker</button>
                  @endif
                @endif
                @if (intval($roadwarrant->status) == 2 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'operational'))
                  <a href="{{ url('letter/roadwarrant/status/active/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin?')" class="btn bg-gradient-primary float-right">Set Aktif</a>
                @endif
                @if (intval($roadwarrant->status) == 3 && ($roleInfo->role_slug == 'super-user' || $roleInfo->role_slug == 'accounting'))
                  <a href="{{ url('letter/roadwarrant/withdraw/2/'.$roadwarrant->uuid) }}" class="btn bg-gradient-success float-right">Transfer uang perjalanan</a>
                @endif
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
                  <th width="250">Status SPJ:</th>
                  <td>
                      @if (intval($roadwarrant->status) === 0)
                        <span class="badge badge-light">Draft</span>   
                      @elseif (intval($roadwarrant->status) === 1)
                        <span class="badge badge-secondary">Waiting to Marker</span>                                  
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
                  </td>
                </tr>

                @if (intval($roadwarrant->status) >= 4)
                    <tr>
                      <th width="250">Bukti transfer uang jalan :</th>
                      <td>
                        <a href="{{ url('letter/roadwarrant/withdraw/2/'.$roadwarrant->uuid) }}" class="btn btn-sm btn-secondary" style="margin-left: 4px;">Check</a>
                      </td>
                    </tr>
                @endif

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
                  <th>Tanggal kembali :</th>
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

                @if (isset($bookPayments) && count($bookPayments) > 0)
                  @foreach ($bookPayments as $payment)
                    <tr>
                      <th width="250">Pembayaran :</th>
                      <td>{{ formatAmount($payment->amount) }}</td>
                    </tr>
                    <tr>
                      <th width="250">Referensi Pembayaran :</th>
                      <td>{{ $payment->description }}</td>
                    </tr>
                  @endforeach
                @endif
                
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
                  <td>
                    {{ $roadwarrant->km_start ? 'Km '.$roadwarrant->km_start : '-' }}
                    @if (in_array(intval($roadwarrant->status), [5, 6]))
                      <button type="button" class="btn btn-xs btn-warning ml-2 no-print" data-toggle="modal" data-target="#kmEditModal">Edit KM</button>
                    @endif
                  </td>
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
            <div class="mb-2 no-print">
              <button id="bulkTerimaBtn" class="btn btn-sm btn-success" disabled><i class="fas fa-check"></i> Terima Tercentang</button>
              <button id="bulkTolakBtn" class="btn btn-sm btn-danger" disabled><i class="fas fa-times"></i> Tolak Tercentang</button>
              <button id="exportExcelBtn" class="btn btn-sm btn-info"><i class="fas fa-file-excel"></i> Export Excel</button>
            </div>
            <table id="expense-table" class="table table-striped">
              <thead>
              <tr>
                <th width="3" class="no-print"><input type="checkbox" id="checkAll"></th>
                <th width="3">No</th>
                <th class="no-print">Aksi</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th class="no-print">Koordinat (lat, long)</th>
                <th>Status</th>
                <th>File</th>
                <th>Jenis</th>
                <th width="150">Nominal</th>
              </tr>
              </thead>
              <tbody>
                @php $summary = 0; $unconfirmedSum = 0; @endphp
                @foreach ($expensesList as $key => $expense)
                  <tr>
                    <td class="no-print"><input type="checkbox" class="expense-check" value="{{ $expense->id }}" data-uuid="{{ $roadwarrant->uuid }}"></td>
                    <td>{{ $key + 1 }}</td>
                    <td class="no-print">
                      <div class="btn-group" role="group">
                        <a
                          href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/2') }}"
                          class="btn btn-xs btn-success"
                          onclick="return confirm('Terima transaksi ini?')"
                          title="Terima"
                        ><i class="fas fa-check"></i></a>
                        <a
                          href="{{ url('letter/roadwarrant/expense/statusupdate/2/'.$roadwarrant->uuid.'/'.$expense->id.'/0') }}"
                          class="btn btn-xs btn-danger"
                          onclick="return confirm('Tolak transaksi ini?')"
                          title="Tolak"
                        ><i class="fas fa-times"></i></a>
                        <a
                          href="{{ url('letter/roadwarrant/expense/edit/'.$expense->id) }}"
                          class="btn btn-xs btn-warning"
                          title="Edit"
                        ><i class="fas fa-pencil-alt"></i></a>
                      </div>
                    </td>
                    <td>{{ $expense->category_name ?? '-' }}<br><small class="text-muted">{{ $expense->description }}</small></td>
                    <td>{{ $expense->created_at }}</td>
                    <td class="no-print">
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
                    <td class="text-right">{{ formatAmount($expense->nominal) }}</td>
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
                  @if ($expense->status == 1 || $expense->status == 0)
                      @php $unconfirmedSum = $unconfirmedSum + $expense->nominal; @endphp
                  @endif
                @endforeach
              </tbody>
            </table>
            <table class="table table-striped w-100">
              <tr>
                <td class="text-right text-warning">Belum terkonfirmasi :</td>
                <td class="text-right text-warning" width="200"><b>{{ formatAmount($unconfirmedSum) }}</b></td>
              </tr>
              <tr>
                <td class="text-right"><b>Sisa uang :</b></td>
                <td class="text-right"><b>{{ formatAmount($summary) }}</b></td>
              </tr>
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

<!-- Modal -->
<!-- KM Edit Modal -->
<div class="modal fade" id="kmEditModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Kilometer</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ url('letter/roadwarrant/km/'.$roadwarrant->uuid) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label>Kilometer Awal</label>
            <input type="number" class="form-control" name="km_start" value="{{ $roadwarrant->km_start }}" placeholder="Masukkan KM awal">
          </div>
          <div class="form-group">
            <label>Kilometer Akhir</label>
            <input type="number" class="form-control" name="km_end" value="{{ $roadwarrant->km_end }}" placeholder="Masukkan KM akhir">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="markerPaymentModal" tabindex="-1" role="dialog" aria-labelledby="markerPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="markerPaymentModalLabel">Input Book Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ url('letter/roadwarrant/marker-payment/'.$roadwarrant->category.'/'.$roadwarrant->uuid) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" name="amount" placeholder="Enter amount" required>
          </div>
          <div class="form-group">
            <label for="description">Referensi</label>
            <input type="text" class="form-control" name="description" placeholder="Enter reference" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit & Set Marker</button>
        </div>
      </form>
    </div>
  </div>
</div>
 
@endsection
@push('extra-scripts')
<script type="text/javascript">
    $(function () {
      var expenseTable = $("#expense-table").DataTable({
        "responsive": false,
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": false,
        "buttons": ["excel"]
      });

      $("#exportExcelBtn").on('click', function () {
        expenseTable.button('.buttons-excel').trigger();
      });

      $('a.printPage').click(function(){
           window.print();
           return false;
      });

      // Checkbox bulk select
      $('#checkAll').on('change', function () {
        $('.expense-check').prop('checked', this.checked);
        toggleBulkButtons();
      });

      $(document).on('change', '.expense-check', function () {
        if (!this.checked) $('#checkAll').prop('checked', false);
        toggleBulkButtons();
      });

      function toggleBulkButtons() {
        const hasChecked = $('.expense-check:checked').length > 0;
        $('#bulkTerimaBtn, #bulkTolakBtn').prop('disabled', !hasChecked);
      }

      async function bulkStatusUpdate(status) {
        const checked = $('.expense-check:checked');
        const label = status == 2 ? 'terima' : 'tolak';
        if (!confirm(`${label.charAt(0).toUpperCase() + label.slice(1)} ${checked.length} transaksi tercentang?`)) return;

        for (const el of checked.toArray()) {
          const id = $(el).val();
          const uuid = $(el).data('uuid');
          try {
            await $.get(`/letter/roadwarrant/expense/statusupdate/2/${uuid}/${id}/${status}`);
          } catch (e) {
            console.error(`Failed to update expense ${id}`, e);
          }
        }
        location.reload();
      }

      $('#bulkTerimaBtn').click(() => bulkStatusUpdate(2));
      $('#bulkTolakBtn').click(() => bulkStatusUpdate(0));
    });
</script>
@endpush