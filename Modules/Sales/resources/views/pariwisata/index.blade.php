@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <!-- /.card-header -->
    <div class="card-body">
      <table id="example" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Kode booking</th>
          <th>Tanggal booking</th>
          <th>Tanggal berangkat dan kembali</th>
          <th>Alamat penjemputan</th>
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
      const apiUrl = '{!! env('BACKEND_URL'); !!}'

      const fetchItem = debounce(value => {
        const payload = {
          startDate: '2024-01-01 00:00:00.000',
          endDate: '2024-01-31 23:59:59.000'
        }
        const headers = { 
          'Content-Type': 'application/x-www-form-urlencoded'
        }

        axios.post(`${apiUrl}report/allpariwisatasales`, payload,{headers})
        .then((response) => {
          addElementToSelect(response.data.data)
        }, (error) => {
          console.log(error)
        });
      }, 1000)

      function addElementToSelect(data) {
        let saleData = [];
        for (let index = 0; index < data.length; index++) {
          saleData[index] = [
            index + 1,
            data[index].booking_code,
            dayjs(data[index].created_at).format('DD MMM YYYY'),
            dayjs(data[index].start_date).format('DD MMM YYYY')+ ' - ' +dayjs(data[index].finish_date).format('DD MMM YYYY'),
            data[index].pickup_address
          ]
        }
        $('#example').DataTable( {
            data: saleData
        })
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