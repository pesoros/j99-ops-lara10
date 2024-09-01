@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List {{ $title }}</h3>
        <div class="float-right">
          @if (permissionCheck('add'))
            <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#item-modal">
              Upload sync data
            </button>
          @endif
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="datatable-def-notitle" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>Nama Barang</th>
          <th>Kode</th>
          <th>Unit</th>
          <th>Kuantitas</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>

  <div class="modal fade" id="item-modal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload file</h4>
          <input type="hidden" id="damage-row" value="">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="input-group">
            <input type="file" name="syncfile" id="syncfile" class="form-control" value="" accept=".csv">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-primary" onclick="return submitfile()">Upload</button>
          <button type="button" id="modal-close" class="btn btn-success" data-dismiss="modal">Kembali</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
 
@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">

  function submitfile() {
    var formData = new FormData();
    var fileupload = document.querySelector('#syncfile');
    formData.append("file", fileupload.files[0]);

    const headers = { 
      'Content-Type': 'multipart/form-data'
    }

    axios.post('/api/accurate/syncdata', formData, {headers})
    .then((response) => {
      $(document).Toasts('create', {
        class: 'bg-success',
        title: 'Berhasil',
        body: 'Upload Sukses',
      })
      setTimeout(function() { 
        location.reload();
      }, 2000);
    }, (error) => {
      $(document).Toasts('create', {
        class: 'bg-danger',
        title: 'Terjadi kesalahan',
        body: 'Upload Gagal ',
      })
    });
  }

</script>
@endpush