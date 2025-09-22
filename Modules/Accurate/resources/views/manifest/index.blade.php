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
        <th>Id Manifest</th>
        <th>Nomor SPJ</th>
        <th>Judul Trip</th>
        <th>Bus</th>
        <th>Keberangkatan</th>
        <th>Aksi</th>
      </tr>
      </thead>
      <tbody>
        @foreach ($lists as $key => $value)
          <tr>
            <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
            <td>{{ $value->manifestId }}</td>
            <td>{{ $value->numberid }}</td>
            <td>{{ $value->trip_title }}</td>
            <td>{{ $value->busname }}</td>
            <td>{{ $value->trip_date }}</td>
            <td>
              <div class="btn-group btn-block">
                @if (permissionCheck('show') && intval($value->isSynced) == 0)
                    <a href="{{ url('accurate/accmanifest/sync/'.$value->manifestUuid) }}" class="btn btn-warning btn-sm loadingscreen">Sync</a> 
                @else 
                  <p>
                    Synced
                  </p>
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
        var bookLists = @json($lists);
        const maxData = 100;
        const bookToSend = bookLists.slice(0, maxData);
        const backendUrl = "{{ $beUrl }}";

        $('.syncBulkClientSide').click(async function () {
            $.LoadingOverlay("show", {
                text: "Mengirim data ke Accurate. Mohon tunggu..."
            });

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };

            try {
                for (const val of bookToSend) {
                    const payload = { manifestUuid: val.manifestUuid };
                    const response = await axios.post(backendUrl + '/accurate/manifest', payload, { headers });
                    console.log('Success:', val.manifestUuid, response.data);
                }

                location.reload();

            } catch (error) {
                console.error('Failed:', error);
                alert('Pengiriman gagal pada salah satu data. Proses dihentikan.');
                $.LoadingOverlay("hide");
            }
        });

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
