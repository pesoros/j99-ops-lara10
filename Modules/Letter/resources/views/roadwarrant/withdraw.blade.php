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
    <input type="hidden" id="category" name="category" value='2'>
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
              <th>Jumlah hari :</th>
              <td>{{ $diffDays }} Hari</td>
            </tr>
            <tr>
              <th>Uang Makan Kru :</th>
              <td>{{ formatAmount($roadwarrant->crew_meal_allowance) }} x {{ $diffDays }} = {{ formatAmount($totalCrewMeal) }}</td>
            </tr>
            <tr>
              <th>Premi Driver 1 :</th>
              <td>{{ formatAmount($roadwarrant->driver_allowance_1) }}</td>
            </tr>
            <tr>
              <th>Premi Driver 2 :</th>
              <td>{{ formatAmount($roadwarrant->driver_allowance_2) }}</td>
            </tr>
            <tr>
              <th>Premi Co Driver :</th>
              <td>{{ formatAmount($roadwarrant->codriver_allowance) }}</td>
            </tr>
            <tr>
              <th>Total biaya SPJ :</th>
              <td><b>{{ formatAmount($totalAllowance) }}</b></td>
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
            <input type="text" class="form-control moneyform" name="amount" id="amount" placeholder="0" value="{{ isset($withdraw->amount) ? $withdraw->amount : 0 }}" @readonly(isset($withdraw->amount)) required >
          </div>
        </div>
        <div class="form-group">
          <label for="transaction_id">Nomor transaksi</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="transaction_id" id="transaction_id" placeholder="masukkan nomor transaksi" value="{{ isset($withdraw->transaction_id) ? $withdraw->transaction_id : '' }}" @readonly(isset($withdraw->transaction_id)) >
          </div>
        </div>
        <div class="form-group">
          <label for="image">Bukti transfer</label>
          <div class="input-group">
          @if (!isset($withdraw->uuid))
              <input type="file" name="image" class="form-control" value="" accept=".png, .jpg, .jpeg">
            @else
              <img 
              src="{{ url($withdraw->image_file) }}"
              class="img" 
              height="600"
              >
            @endif
          </div>
        </div>
      </div>
    </div>
    @if (!isset($withdraw->uuid))
      <div class="card-footer">
        <button type="submit" class="btn btn-primary" onclick="return confirm('Anda yakin data Transfer yg diisi sudah benar?')">Submit</button>
        <a href="{{ url('letter/roadwarrant/show/detail/1/'.$roadwarrant->uuid) }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
      </div>
    @endif
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
