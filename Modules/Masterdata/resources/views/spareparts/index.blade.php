@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List {{ $title }}</h3>
        <div class="float-right">
          @if (permissionCheck('add'))
            <a href="{{ url('api/accurate/refreshtoken') }}" target="_blank" class="btn btn-secondary btn-sm">
              Refresh token accurate 
            </a>
          @endif
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="form-group">
        <label for="keyword">Masukkan kata kunci</label>
        <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Masukkan kata kunci">
      </div>
      <table class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Item No</th>
          <th>Nama Item</th>
          <th>Stok</th>
        </tr>
        </thead>
        <tbody id="table-item"></tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    $(function () {      
      let rowState = 0;
      let rowCount = 0;

      $("#keyword").keyup(function(e){
        fetchItem(e.target.value)
      });

      const fetchItem = debounce(value => {
        $('#table-item').html('');
        axios.get(`/api/spareparts?keyword=${value}`)
          .then((response) => {
            addElementToSelect(response.data);
          }, (error) => {
            console.log(error);
          });
      }, 1000)

      function addElementToSelect(data) {
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += '<tr>'
          html += '<td width="20" class="text-center">'+ (index + 1) +'</td>'
          html += '<td>'+ data[index].no +'</td>'
          html += '<td>'+ data[index].name +'</td>'
          html += '<td>'+ data[index].quantity +' Pcs</td>'
          html += '</tr>'
        }
        $('#table-item').append(html);
      }

      function debounce(cb, delay = 250) {
        let timeout

        return (...args) => {
          clearTimeout(timeout)
          timeout = setTimeout(() => {
            cb(...args)
          }, delay)
        }
      }

      fetchItem('')
    });
</script>
@endpush