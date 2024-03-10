@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Filter</h3>
  </div>
  <div class="card-body">
    <div class="col-sm-12 row">
      <div class="col-sm-2">
        <div class="form-group">
          <label>Tahun</label>
          <select class="form-control select2" name="bus_uuid" id="years-select" style="width: 100%;" required></select>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="form-group">
          <label>Bulan</label>
          <select class="form-control select2" name="bus_uuid" id="months-select" style="width: 100%;" required></select>
        </div>
      </div>
      <div class="col-sm-1">
        <div class="form-group">
          <label>&nbsp;</label>
          <button type="button" class="btn btn-primary form-control" id="filter-button">Cari</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
    <div class="card-header">
      <h3 class="card-title">List {{ $title }} <span id="date-state"></span></h3>
    </div>
    <div class="card-body">
      <table id="table-wrap" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Tanggal pembelian</th>
          <th>Titik jemput</th>
          <th>Titik turun</th>
          <th>Agen</th>
        </tr>
        </thead>
        <tbody id="table-list"></tbody>
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
      const apiUrl = '{!! env('BACKEND_URL'); !!}'
      const yearNow = dayjs().year()
      const monthNow = dayjs().month() + 1
      
      function fetchItem(year, month) {
        const monthPrepend = month < 10 ? '0' + month : month
        $('#date-state').html(`(${dayjs(year+'-'+monthPrepend+'-01').format('MMMM YYYY')})`)
        const payload = {
          startDate: `${year}-${monthPrepend}-01 00:00:00.000`,
          endDate: `${year}-${monthPrepend}-31 23:59:59.000`
        }
        const headers = { 
          'Content-Type': 'application/x-www-form-urlencoded'
        }

        axios.post(`${apiUrl}report/allakapsales`, payload,{headers})
        .then((response) => {
          addElementToSelect(response.data.data)
        }, (error) => {
          console.log(error)
        });
      }

      function addElementToSelect(data) {
        let saleData = [];
        for (let index = 0; index < data.length; index++) {
          saleData[index] = [
            index + 1,
            dayjs(data[index].booking_date).format('DD MMM YYYY - hh:mm A'),
            data[index].pickup_trip_location,
            data[index].drop_trip_location,
            data[index].agent
          ]
        }
        $("#table-wrap").DataTable({
          "responsive": true, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
          data: saleData
        }).buttons().container().appendTo('#datatable-def_wrapper .col-md-6:eq(0)')
      }

      function generateYears() {
        let optionData = []
        for (let index = 0; index < 10; index++) {
          $('#years-select').append(new Option(yearNow - index, yearNow - index, false, false)).trigger('change')
        }
      }

      function generateMonths() {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const optionData = months.map(((val, index) => {
          const id = index + 1;
          return new Option(val, id, false, monthNow === id);
        }))
        $('#months-select').append(optionData).trigger('change'); 
      }

      $("#filter-button").click(function(){
        $('#table-wrap').DataTable().clear().destroy();
        const yearPick = $('#years-select').val();
        const monthPick = $('#months-select').val();
        fetchItem(yearPick, monthPick)
      });

      generateYears()
      generateMonths()
      fetchItem(yearNow, monthNow)
    });
</script>
@endpush