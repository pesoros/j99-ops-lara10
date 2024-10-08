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
  <form action="{{ url()->current() }}" method="post" autocomplete="off">
    @csrf
    <div class="card-body row">
        <div class="col-sm-12">
          <div class="form-group">
            <label>Tanggal :</label>
              <div class="input-group date" id="datepicker" data-target-input="nearest">
                <div class="input-group-prepend" data-target="#datepicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
                <input type="text" name="date" class="form-control datetimepicker-input" data-target="#datepicker" required />
              </div>
          </div>
          <div class="form-group">
            <label>Pilih bus</label>
            <select class="form-control select2bs4" name="bus_uuid" id="bus-select" style="width: 100%;" required>
              <option value="">Pilih</option>
              @foreach ($bus as $busItem)
                <option value="{{ $busItem->busuuid }}">
                    {{ $busItem->busname }} | {{ $busItem->registration_number }}
                </option>
              @endForeach
            </select>
          </div>
          <div class="form-group">
            <label for="km_start">Kilometer awal</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Km</span>
              </div>
              <input type="number" class="form-control" name="km_start" placeholder="0" value="" required>
            </div>
          </div>
          <div class="form-group">
            <label>Trip assign</label>
            <select class="form-control select2bs4" name="trip_assign" id="tras-item" style="width: 100%;" required>
              <option value="" @selected(old('trip_assign') == '')>Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Driver 1</label>
            <select class="form-control select2bs4" id="driver1" name="driver_1" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Driver 2</label>
            <select class="form-control select2bs4" id="driver2" name="driver_2" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Co driver</label>
            <select class="form-control select2bs4" id="codriver" name="codriver" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            <label for="crew_meal_allowance">Uang makan kru per hari</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="crew_meal_allowance" placeholder="0" required>
            </div>
          </div>
          <!-- <div class="form-group">
            <label for="fuel_allowance">Uang solar</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="fuel_allowance" placeholder="0" required>
            </div>
          </div> -->
          <div class="form-group">
            <label for="trip_allowance">Uang jalan</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="trip_allowance" placeholder="0" required>
            </div>
          </div>
          <div class="form-group">
            <label for="driver_allowance_1">Uang premi driver 1</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="driver_allowance_1" placeholder="0" required>
            </div>
          </div>
          <div class="form-group">
            <label for="driver_allowance_2">Uang premi driver 2</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="driver_allowance_2" placeholder="0" required>
            </div>
          </div>
          <div class="form-group">
            <label for="codriver_allowance">Uang premi co driver</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control moneyform" name="codriver_allowance" placeholder="0" required>
            </div>
          </div>
        </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('letter/complaint') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
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

  $("#datepicker").on("change.datetimepicker", ({date}) => {
    const dateConv = dayjs(date._d).format('YYYY-MM-DD')
    fetchEmployee(dateConv)
  })

  function fetchEmployee(value) {
    $('#driver1').html('');
    $('#driver2').html('');
    $('#codriver').html('');
    axios.get(`/api/employeeready?date=${value}`)
      .then((response) => {
        addElementToSelectEmployee(response.data);
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelectEmployee(data) {
    let html = '';
    html += '<option value="">Pilih</option>'
    for (let index = 0; index < data.length; index++) {
      if (data[index].assignee.length === 0 && data[index].assignee_akap.length === 0) {
        html += '<option value="'+ data[index].id +'">'+ data[index].first_name + ' ' + data[index].second_name +'</option>'
      }
    }
    $('#driver1').append(html);
    $('#driver2').append(html);
    $('#codriver').append(html);
  }
</script>
@endpush
