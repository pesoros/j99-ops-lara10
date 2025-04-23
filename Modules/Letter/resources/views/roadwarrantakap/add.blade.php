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
            <label>Tanggal</label>
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
            <label>Driver 1</label>
            <select class="form-control select2bs4" id="driver1" name="driver_1" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Driver 2</label>
            <select class="form-control select2bs4" id="driver2" name="driver_2" style="width: 100%;">
              <option value="">Pilih jika menggunakan driver ke 2</option>
            </select>
          </div>
          <div class="form-group">
            <label>Co driver</label>
            <select class="form-control select2bs4" id="codriver" name="codriver" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <label>Jumlah Trip</label>
                <select class="form-control select2" id="numberoftrip" name="numberoftrip" style="width: 100%;" required>
                  <option value="1">1</option>
                  <option value="2">2</option>
                </select>
              </div>
            </div>
            <div class="col-sm-1"></div>
            <div class="col-sm-5" id="date2wrapper">
              <div class="form-group">
                <label>Tanggal trip 2</label>
                  <div class="input-group date" id="datepicker_return" data-target-input="nearest">
                    <div class="input-group-prepend" data-target="#datepicker_return" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                    <input type="text" name="date_return" id="date_return" class="form-control datetimepicker-input" data-target="#datepicker_return" />
                  </div>
              </div>
            </div>
          </div>
        </div>

          <div class="col-sm-5">
            <div class="form-group">
              <label>Trip assign 1</label>
              <select class="form-control select2bs4" name="trip_assign" id="tras-item" style="width: 100%;" required>
                <option value="" @selected(old('trip_assign') == '')>Pilih</option>
              </select>
            </div>
            <div class="form-group">
              <label for="crew_meal_allowance">Uang makan crew (per orang)</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="crew_meal_allowance" id="crew_meal_allowance" placeholder="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="driver_allowance_1">Uang premi driver (per orang)</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="driver_allowance" id="driver_allowance" placeholder="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="codriver_allowance">Uang premi co driver</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="codriver_allowance" id="codriver_allowance"  placeholder="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="trip_allowance">Uang jalan</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="trip_allowance" id="trip_allowance" placeholder="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="fuel_allowance">Uang BBM</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="fuel_allowance" id="fuel_allowance" placeholder="0" required>
              </div>
            </div>
            <div class="form-group">
              <label for="etoll_allowance">Uang E-Toll</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="etoll_allowance" id="etoll_allowance" placeholder="0" required readonly>
              </div>
            </div>
          </div>

          <div class="col-sm-1"></div>

          <div class="col-sm-5" id="trip2wrapper">
            <div class="form-group">
              <label>Trip assign 2</label>
              <select class="form-control select2bs4" name="trip_assign_return" id="tras-item-return" style="width: 100%;">
                <option value="" @selected(old('trip_assign_return') == '')>Pilih</option>
              </select>
            </div>
            <div class="form-group">
              <label for="crew_meal_allowance_return">Uang makan crew (per orang)</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="crew_meal_allowance_return" id="crew_meal_allowance_return" placeholder="0">
              </div>
            </div>
            <div class="form-group">
              <label for="driver_allowance_return">Uang premi driver (per orang)</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="driver_allowance_return" id="driver_allowance_return" placeholder="0">
              </div>
            </div>
            <div class="form-group">
              <label for="codriver_allowance_return">Uang premi co driver</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="codriver_allowance_return" id="codriver_allowance_return"  placeholder="0">
              </div>
            </div>
            <div class="form-group">
              <label for="trip_allowance_return">Uang jalan</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="trip_allowance_return" id="trip_allowance_return" placeholder="0">
              </div>
            </div>
            <div class="form-group">
              <label for="fuel_allowance_return">Uang BBM</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="fuel_allowance_return" id="fuel_allowance_return" placeholder="0">
              </div>
            </div>
            <div class="form-group">
              <label for="etoll_allowance_return">Uang E-Toll</label>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control moneyform" name="etoll_allowance_return" id="etoll_allowance_return" placeholder="0" readonly>
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
  $("#trip2wrapper").hide()
  $("#date2wrapper").hide()
  let maxDate = dayjs().add(7, 'day').format('YYYY-MM-DD')
  let pickedBusUuid = "";
  
  $('#datepicker').datetimepicker({
    format: 'DD/MM/YYYY',
    minDate : 'now',
    maxDate : maxDate,
  });

  $('#datepicker_return').datetimepicker({
    format: 'DD/MM/YYYY',
    minDate : 'now',
    maxDate : maxDate,
  });

  $("#bus-select").change(function(e){
    pickedBusUuid = e.target.value;
    fetchItem(e.target.value);
  });

  $("#tras-item").change(function(e){
    const allowancedata = e.target.options[e.target.selectedIndex].dataset.allowance.split("|");
    const routeId = allowancedata[0] ?? "";
    const crewMealDefault = allowancedata[1] ?? 0;
    const premiDriverDefault = allowancedata[2] ?? 0;
    const premiCoDriverDefault = allowancedata[3] ?? 0;
    const etollDefault = allowancedata[4] ?? 0;

    $('#crew_meal_allowance').val(crewMealDefault);
    $('#driver_allowance').val(premiDriverDefault);
    $('#codriver_allowance').val(premiCoDriverDefault);
    $('#etoll_allowance').val(etollDefault);
    fetchFuelAllowance(pickedBusUuid, routeId, 'fuel_allowance');
  });

  $("#tras-item-return").change(function(e){
    const allowancedata = e.target.options[e.target.selectedIndex].dataset.allowance.split("|");
    const routeId = allowancedata[0] ?? "";
    const crewMealDefault = allowancedata[1] ?? 0;
    const premiDriverDefault = allowancedata[2] ?? 0;
    const premiCoDriverDefault = allowancedata[3] ?? 0;
    const etollDefault = allowancedata[4] ?? 0;

    $('#crew_meal_allowance_return').val(crewMealDefault);
    $('#driver_allowance_return').val(premiDriverDefault);
    $('#codriver_allowance_return').val(premiCoDriverDefault);
    $('#etoll_allowance_return').val(etollDefault);
    fetchFuelAllowance(pickedBusUuid, routeId, 'fuel_allowance_return');
  });

  $("#numberoftrip").change(function(e){
    if (e.target.value === '1') {
      $("#trip2wrapper").hide()
      $("#date2wrapper").hide()
    } else {
      $("#trip2wrapper").show()
      $("#date2wrapper").show()
    };
    updateTrip2Required()
  });

  function updateTrip2Required() {
    let isRequired = false;
    if ($('#trip2wrapper').is(':visible')) {
      isRequired = true
    } else {
      isRequired = false
    }
    $('#date-return').prop('required', isRequired);
    $('#tras-item-return').prop('required', isRequired);
    $('#crew_meal_allowance_return').prop('required', isRequired);
    $('#driver_allowance_return').prop('required', isRequired);
    $('#codriver_allowance_return').prop('required', isRequired);
    $('#trip_allowance_return').prop('required', isRequired);
    $('#fuel_allowance_return').prop('required', isRequired);
    $('#etoll_allowance_return').prop('required', isRequired);
  }

  function fetchItem(value) {
    $('#tras-item').html('');
    $('#tras-item-return').html('');
    axios.get(`/api/trasbus?busuuid=${value}`)
      .then((response) => {
        addElementToSelect(response.data);
      }, (error) => {
        console.log(error);
      });
  }

  function fetchFuelAllowance(busUuid, routeId, elementId) {
    axios.get(`/api/fuelallowance/${busUuid}/${routeId}`)
      .then((response) => {
        $(`#${elementId}`).val(response.data.allowance);
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelect(data) {
    let html = '';
    html += '<option value="">Pilih</option>'
    for (let index = 0; index < data.length; index++) {
      const defaultAllowance = data[index].route + '|' + data[index].crew_meal + '|' + data[index].premi_driver + '|' + data[index].premi_codriver + '|' + data[index].etoll;
      html += '<option data-allowance="'+ defaultAllowance +'" value="'+ data[index].trasid +'">'+ data[index].trasid + ' | ' + data[index].trip_title +'</option>'
    }
    $('#tras-item').append(html);
    $('#tras-item-return').append(html);
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
        html += '<option value="'+ data[index].id +'">'+ data[index].first_name + ' ' + data[index].second_name + ' | ' + data[index].position +'</option>'
      }
    }
    $('#driver1').append(html);
    $('#driver2').append(html);
    $('#codriver').append(html);
  }
</script>
@endpush
