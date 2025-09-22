@extends('layouts.main', ['title' => $title ])

@section('content')

@if ($errors->any())
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fas fa-ban"></i> Gagal Validasi!</h5>
  @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
</div>
@endif

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
 
<div class="card">
  <div class="card-header">
      <h3 class="card-title">List {{ $title }}</h3>
      <div class="float-right">
        @if (permissionCheck('add'))
          {{-- <a href="{{ url('accurate/sales/syncbulk') }}" class="btn btn-warning btn-sm loadingscreen">
            Sync Data
          </a> --}}
          <a href="#" class="btn btn-warning btn-sm syncBulkClientSide">
            Sync Data
          </a>
        @endif
      </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="datatable-accurate" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>No</th>
        <th>Kode Booking</th>
        <th>Id SO</th>
        <th>Aksi</th>
      </tr>
      </thead>
      <tbody>
        @foreach ($lists as $key => $value)
          <tr>
            <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
            <td>{{ $value->booking_code }}</td>
            <td>
                @if ($value->accurate_soid != 0)
                    {{ $value->accurate_soid }}
                @else 
                    Belum sinkron
                @endif
            </td>
            <td>
              <div class="btn-group btn-block">
                @if (permissionCheck('show') && intval($value->accurate_soid) == 0)
                  @if ($value->tkt_booking_id_no != null)
                    @if (intval($value->ref_soid) != 0)
                      <a href="{{ url('accurate/sales/sync/'.$value->booking_code) }}" class="btn btn-warning btn-sm loadingscreen">Sync</a> 
                    @endif
                  @else 
                      <a href="{{ url('accurate/sales/sync/'.$value->booking_code) }}" class="btn btn-warning btn-sm loadingscreen">Sync</a> 
                  @endif
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>
 
@endsection

@push('extra-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qs/dist/qs.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>

<script type="text/javascript">
    $(function () {
        var bookLists = @json($lists).reverse();
        const maxData = 100;
        const bookToSend = bookLists.filter(item => item.accurate_soid === '0').slice(0, maxData);
        const backendUrl = "{{ $beUrl }}";

        $('.syncBulkClientSide').click(async function () {
            $.LoadingOverlay("show", {
                text: "Mengirim data ke Accurate. Mohon tunggu..."
            });

            try {
                for (const val of bookToSend) {
                  if (val.tkt_booking_id_no !== null) {
                    if (val.ref_soid !== 0) {
                      await salesFetch(val.booking_code);
                    } else {
                      console.log('Resc Skipped:', val.booking_code);
                    }
                  } else {
                    await salesFetch(val.booking_code);
                  }
                }

                location.reload();

            } catch (error) {
                console.error('Failed:', error);
                alert('Pengiriman gagal pada salah satu data. Proses dihentikan.');
                $.LoadingOverlay("hide");
            }
        });

        async function salesFetch(bookingCode) {
          const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          };

          const payload = { booking_code: bookingCode };
          const response = await axios.post(backendUrl + '/accurate/sales', payload, { headers });
          console.log('Success:', bookingCode, response.data);
          return response.data; // biar hasilnya bisa dipakai setelah await
        }


        $('.loadingscreen').click(function () {
            $.LoadingOverlay("show", {
                text: "Mohon tidak keluar dari halaman"
            });
        });

        $("#datatable-accurate").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 100,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#datatable-accurate_wrapper .col-md-6:eq(0)');
    });
</script>

@endpush
