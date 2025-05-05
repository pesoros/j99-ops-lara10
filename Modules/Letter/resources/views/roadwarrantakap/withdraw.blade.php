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

<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Form {{ $title }}</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form action="{{ url()->current() }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="card-body row">
      <div class="col-sm-12 invoice-col">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>Nomor SPJ :</th>
              <td>{{ $roadwarrant->numberid }}</td>
            </tr>
            <tr>
              <th>Tujuan transfer :</th>
              <td>{{ $roadwarrant->bank_account }} | {{ $roadwarrant->bank_name }} - {{ $roadwarrant->bank_number }}</td>
            </tr>
            <tr>
              <th>Total biaya SPJ :</th>
              <td>{{ formatAmount($roadwarrant->total_allowance) }}</td>
            </tr>
          </table>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <label for="amount">Jumlah transfer</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="amount" id="amount" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="transaction_id">Nomor transaksi</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="masukkan nomor transaksi" required>
          </div>
        </div>
        <div class="form-group">
          <label for="image">Bukti transfer</label>
          <div class="input-group">
            <input type="file" name="image" class="form-control" value="" accept=".png, .jpg, .jpeg" required>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" onclick="return confirm('Anda yakin data Transfer yg diisi sudah benar?')">Submit</button>
      <a href="{{ url('letter/roadwarrant/show/detail/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
  let maxDate = dayjs().add(7, 'day').format('YYYY-MM-DD')
  $('#datepicker').datetimepicker({
      format: 'DD/MM/YYYY',
      minDate : 'now',
      maxDate : maxDate,
  });

  $("#bus-select").change(function(e){
    fetchItem(e.target.value)
  });

  function fetchItem(value) {
    $('#tras-item').html('');
    axios.get(`/api/trasbus?busuuid=${value}`)
      .then((response) => {
        console.log(response.data)
        addElementToSelect(response.data);
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelect(data) {
    let html = '';
    html += '<option value="">Pilih</option>'
    for (let index = 0; index < data.length; index++) {
      html += '<option value="'+ data[index].trasid +'">'+ data[index].trasid + ' | ' + data[index].trip_title +'</option>'
    }
    $('#tras-item').append(html);
  }
</script>
@endpush
