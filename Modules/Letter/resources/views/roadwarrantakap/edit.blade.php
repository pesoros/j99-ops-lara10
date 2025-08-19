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
  <form action="{{ url('letter/roadwarrant/edit_akap/'.$roadwarrant->uuid) }}" method="post" autocomplete="off">
    @csrf
    <div class="card-body row">
        <div class="col-sm-12">
          <div class="form-group">
            <label>Nomor SPJ</label>
            {{ $roadwarrant->numberid }}
          </div>
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
            <label>Keterangan</label>
            <textarea class="form-control" name="description" rows="3" placeholder="Masukkan Keterangan">{{ $roadwarrant->description }}</textarea>
          </div>
          <div class="form-group">
            <label>Pilih bus</label>
            <select class="form-control select2bs4" name="bus_uuid" id="bus-select" style="width: 100%;" required>
              <option value="">Pilih</option>
              @foreach ($bus as $busItem)
                <option value="{{ $busItem->busuuid }}" @selected($busItem->busuuid == $roadwarrant->bus_uuid)>
                    {{ $busItem->busname }} | {{ $busItem->registration_number }}
                </option>
              @endForeach
            </select>
            {{-- <div class="m-2" ></div>
            <div class="icheck-success d-inline m-2">
              <input 
                type="checkbox" 
                id="is_replacement_bus" 
                name="is_replacement_bus" 
                value="1"
                @checked(strval($roadwarrant->is_replacement_bus) == '1')
              >
              <label for="is_replacement_bus">
                Bus Pengganti
              </label>&nbsp;&nbsp;&nbsp;&nbsp;
            </div> --}}
          </div>
          <div class="form-group">
            <label>Driver 1</label>
            <select class="form-control select2bs4" id="driver1" name="driver_1" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Driver 2 (Pilih Jika menggunakan drive ke 2 atau abaikan jika tidak)</label>
            <select class="form-control select2bs4" id="driver2" name="driver_2" style="width: 100%;">
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="form-group">
            <label>Co driver</label>
            <select class="form-control select2bs4" id="codriver" name="codriver" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div> 
          <div class="form-group">
            <label>Rekening uang jalan</label>
            <select class="form-control select2bs4" name="transferto" id="transferto" style="width: 100%;" required>
              <option value="">Pilih</option>
            </select>
          </div>
          <div class="row">
            <div class="col-sm-5">
              <div class="form-group">
                <label>Jumlah Trip</label>
                <select class="form-control select2" id="numberoftrip" name="numberoftrip" style="width: 100%;" required>
                  <option value="1" @selected(intval($roadwarrant->number_of_trip) == 1)>1</option>
                  <option value="2" @selected(intval($roadwarrant->number_of_trip) == 2)>2</option>
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
        </div>

        <div class="col-sm-1"></div>

        <div class="col-sm-5" id="trip2wrapper">
          <div class="form-group">
            <label>Trip assign 2</label>
            <select class="form-control select2bs4" name="trip_assign_return" id="tras-item-return" style="width: 100%;">
              <option value="" @selected(old('trip_assign_return') == '')>Pilih</option>
            </select>
          </div>
        </div>

        <div class="col-sm-1"></div>

        <div class="col-sm-12">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Tabel uang jalan SPJ</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap">
                    <thead>
                      <tr>
                        <th width="100">No</th>
                        <th>Judul</th>
                        <th width="190" class="text-right">Nominal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>UANG MAKAN CREW (<span id="crewcount">2</span> orang)</td>
                        <td class="text-right crew_meal_allowance">Rp0</td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>PREMI DRIVER <span id="drivercount">1</span></td>
                        <td class="text-right driver_allowance">Rp0</td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>PREMI CO-DRIVER</td>
                        <td class="text-right codriver_allowance">Rp0</td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td>Uang saku</td>
                        <td class="text-right trip_allowance">
                          <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control moneyform text-right" name="trip_allowance" id="trip_allowance" placeholder="0" value="{{ $roadwarrant->trip_allowance }}" required>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>5</td>
                        <td>BBM</td>
                        <td class="text-right fuel_allowance">Rp0</td>
                      </tr>
                      <tr>
                        <td>6</td>
                        <td>ETOLL</td>
                        <td class="text-right etoll_allowance">Rp0</td>
                      </tr>
                    </tbody>
                    <tfood>
                      <tr>
                        <td class="text-right" colspan="2">Total biaya :</td>
                        <td class="text-right totalsum">Rp0</td>
                      </tr>
                    </thead>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
          </div>
          <div class="form-group">
            <input type="hidden" class="form-control" name="old_transferto" id="old_transferto" value="{{ $roadwarrant->transferto }}">
            <input type="hidden" class="form-control" name="crew_meal_allowance" id="crew_meal_allowance">
            <input type="hidden" class="form-control" name="driver_allowance" id="driver_allowance">
            <input type="hidden" class="form-control" name="codriver_allowance" id="codriver_allowance" >
            <input type="hidden" class="form-control" name="fuel_allowance" id="fuel_allowance">
            <input type="hidden" class="form-control" name="etoll_allowance" id="etoll_allowance">
            <input type="hidden" class="form-control" name="totalsum" id="totalsum">
          </div>
        </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" id="submitbutton">Submit</button>
      <a href="{{ url('letter/roadwarrant') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
  let numberOfTrip = 1;
  let tripAllowance = 0;
  let driverCount = 1;
  let crewCount = 2;
  let employee = [];
  let employeePicked = [];
  let dalow1 = [];
  let dalow2 = [];

  let tripAmount = {
    crew_meal_allowance: 0,
    driver_allowance: 0,
    codriver_allowance: 0,
    etoll_allowance: 0,
    fuel_allowance: 0,
  };

  let tripAmountReturn = {
    crew_meal_allowance: 0,
    driver_allowance: 0,
    codriver_allowance: 0,
    etoll_allowance: 0,
    fuel_allowance: 0,
  };

  const manifest = {!! json_encode($manifest) !!};
  const manifestReturn = {!! json_encode($manifest_return) !!};
  const roadwarrant = {!! json_encode($roadwarrant) !!};

  $(document).ready( function () {
    tripAllowance = roadwarrant.trip_allowance
    const manifestDate = dayjs(manifest.trip_date).format('DD/MM/YYYY')
    setBus(roadwarrant.bus_uuid, 1)
    fetchEmployee(manifestDate, 1)
    setNumbertrip(roadwarrant.number_of_trip)
    if (roadwarrant.number_of_trip > 1) {
      const tripdate2 = new Date(manifestReturn.trip_date);
      datepicker1Change(roadwarrant.trip_date, tripdate2)
    }
    setTimeout(function () {
      setTripAssign(dalow1, 1)
      if (roadwarrant.number_of_trip > 1) {
        setTripAssign(dalow2, 2)
        setCrewCount('1')
      }
    }, 2000);
  });

  $("#trip2wrapper").hide()
  $("#date2wrapper").hide()
  let maxDate = dayjs().add(7, 'day').format('YYYY-MM-DD')
  let pickedBusUuid = "";

  const tripdate1 = new Date(manifest.trip_date);
  
  $('#datepicker').datetimepicker({
    format: 'DD/MM/YYYY',
    minDate: 'now',
    maxDate: maxDate,
    date: tripdate1,
  });

  $('#datepicker_return').datetimepicker({
    format: 'DD/MM/YYYY',
    minDate: 'now',
    maxDate: maxDate,
  });

  $("#bus-select").change(function(e){
    setBus(e.target.value)
  });

  $("#driver1").change(function(e){
    pickBankList()
  });

  $("#codriver").change(function(e){
    pickBankList()
  });

  $("#driver2").change(function(e){
    setCrewCount(e.target.value)
    pickBankList()
    tripSummary()
  });

  $("#tras-item").change(function(e){
    setTripAssign(e.target.options[e.target.selectedIndex].dataset.allowance.split("|"), 1)
  });

  $("#tras-item-return").change(function(e){
    setTripAssign(e.target.options[e.target.selectedIndex].dataset.allowance.split("|"), 2)
  });

  $("#numberoftrip").change(function(e){
    setNumbertrip(e.target.value)
  });

  $("#datepicker").on("change.datetimepicker", ({date}) => {
    datepicker1Change(date._d)
  })

  $("#trip_allowance").on("input", function() {
    const trVal = $(this).val().trim() !== '' ? $(this).val().replaceAll(".", "") : 0; 
    tripAllowance = parseInt(trVal);
    tripSummary()
  });

  $("#submitbutton").on("click", function() {
    let driver1Val = $('#driver1').val()
    let driver2Val = $('#driver2').val()
    let trip_assign = $('#tras-item').val()
    let trip_assign_return = $('#tras-item-return').val()

    if (driver1Val === driver2Val) {
      alert('Driver 1 dan 2 tidak boleh sama');
      return false;
    } else if (numberOfTrip === 2 && parseInt(trip_assign) === parseInt(trip_assign_return)) {
      alert('Trip assign 1 dan 2 tidak boleh sama');
      return false;
    }
  })

  function setCrewCount(value) {
    const driveri = value === '' ? 1 : 2; 
    const crewi = value === '' ? 2 : 3;

    driverCount = driveri
    crewCount  = crewi

    $('#drivercount').html(driveri === 1 ? '1' : '1 dan 2');
    $('#crewcount').html(crewi);
  }

  function setTripAssign(allowancedata, tripNumberDrop) {
    if (tripNumberDrop === 1) {
      tripAmount.crew_meal_allowance = parseInt(allowancedata[1]) ?? 0
      tripAmount.driver_allowance = parseInt(allowancedata[2]) ?? 0
      tripAmount.codriver_allowance = parseInt(allowancedata[3]) ?? 0
      tripAmount.etoll_allowance = parseInt(allowancedata[4]) ?? 0
    } else {
      tripAmountReturn.crew_meal_allowance = parseInt(allowancedata[1]) ?? 0
      tripAmountReturn.driver_allowance = parseInt(allowancedata[2]) ?? 0
      tripAmountReturn.codriver_allowance = parseInt(allowancedata[3]) ?? 0
      tripAmountReturn.etoll_allowance = parseInt(allowancedata[4]) ?? 0
    }

    fetchFuelAllowance(pickedBusUuid, parseInt(allowancedata[0]) ?? "", tripNumberDrop);
  }

  function setBus(value, isFirst = 0) {
    pickedBusUuid = value;
    fetchItem(value, isFirst);
  }

  function setNumbertrip(value) {
    if (parseInt(value) === 1) {
      numberOfTrip = 1
      $("#trip2wrapper").hide()
      $("#date2wrapper").hide()
    } else {
      numberOfTrip = 2
      $("#trip2wrapper").show()
      $("#date2wrapper").show()
    };
    tripSummary()
  }

  function pickBankList() {
    $('#transferto').html('');
    employeePicked = []
    let driver1Val = $('#driver1').val()
    let driver2Val = $('#driver2').val()
    let codriverVal = $('#codriver').val()

    for (let index = 0; index < employee.length; index++) {
      const element = employee[index];
      if (parseInt(driver1Val) === parseInt(element.id)) {
        employeePicked.push(element)
      }

      if (parseInt(driver2Val) === parseInt(element.id)) {
        employeePicked.push(element)
      }

      if (parseInt(codriverVal) === parseInt(element.id)) {
        employeePicked.push(element)
      }
    }

    console.log(employeePicked);
    
    
    let transfertoElement = '';
    transfertoElement += '<option value="">Pilih</option>'
    for (let index = 0; index < employeePicked.length; index++) {
      let selectedTrt = ""
      if (parseInt(roadwarrant.transferto) === parseInt(employeePicked[index].id)) {
        selectedTrt = 'selected'
      }
      transfertoElement += '<option value="'+ employeePicked[index].id +'" '+ selectedTrt +'>'+ employeePicked[index].first_name + ' | ' + employeePicked[index].bank_name + ' ' + employeePicked[index].bank_number +'</option>'
    }
    $('#transferto').append(transfertoElement);
  }

  function fetchItem(value, isFirst) {
    $('#tras-item').html('');
    $('#tras-item-return').html('');
    axios.get(`/api/trasbus?busuuid=${value}`)
      .then((response) => {
        addElementToSelect(response.data.filtered, isFirst);
      }, (error) => {
        console.log(error);
      });
  }

  function fetchFuelAllowance(busUuid, routeId, tripNumber) {
    axios.get(`/api/fuelallowance/${busUuid}/${routeId}`)
      .then((response) => {
        if (parseInt(tripNumber) == 1) {
          tripAmount.fuel_allowance = response.data.allowance ?? 0
        } else if (parseInt(tripNumber) == 2) {
          tripAmountReturn.fuel_allowance = response.data.allowance ?? 0
        }
        tripSummary()
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelect(data, isFirst) {
    let html = '';
    let html2 = '';

    html += '<option value="">Pilih</option>'
    html2 += '<option value="">Pilih</option>'

    for (let index = 0; index < data.length; index++) {
      let selectedTripAssign = ""
      let selectedTripAssign2 = ""
      
      if (isFirst === 1) {
        if (parseInt(manifest.trip_assign) === parseInt(data[index].trasid)) {
          selectedTripAssign = 'selected'
          dalow1 = [data[index].route, data[index].crew_meal, data[index].premi_driver, data[index].premi_codriver, data[index].etoll];
        }
        if (roadwarrant.number_of_trip > 1) {
          if (parseInt(manifestReturn.trip_assign) === parseInt(data[index].trasid)) {
            selectedTripAssign2 = 'selected'
            dalow2 = [data[index].route, data[index].crew_meal, data[index].premi_driver, data[index].premi_codriver, data[index].etoll];
          }
        }
      }

      const defaultAllowance = data[index].route + '|' + data[index].crew_meal + '|' + data[index].premi_driver + '|' + data[index].premi_codriver + '|' + data[index].etoll;
      html += '<option data-allowance="'+ defaultAllowance +'" value="'+ data[index].trasid +'" '+ selectedTripAssign +'>'+ data[index].trasid + ' | ' + data[index].trip_title +'</option>'
      html2 += '<option data-allowance="'+ defaultAllowance +'" value="'+ data[index].trasid +'" '+ selectedTripAssign2 +'>'+ data[index].trasid + ' | ' + data[index].trip_title +'</option>'
    }
    $('#tras-item').append(html);
    $('#tras-item-return').append(html2);
  }

  function fetchEmployee(value, isFirst) {
    $('#driver1').html('');
    $('#driver2').html('');
    $('#codriver').html('');
    axios.get(`/api/employeeready?date=${value}`)
      .then((response) => {
        employee = response.data;
        addElementToSelectEmployee(response.data, isFirst);
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelectEmployee(data, isFirst = 0) {
    let htmlDriver = '';
    let htmlDriver2 = '';
    let htmlCoDriver = '';

    htmlDriver += '<option value="">Pilih</option>'
    htmlDriver2 += '<option value="">Pilih</option>'
    htmlCoDriver += '<option value="">Pilih</option>'
    for (let index = 0; index < data.length; index++) {
      if (data[index].assignee.length === 0 && data[index].assignee_akap.length === 0) {
        if (data[index].position == 'Driver') {
          let selected = ""
          let selected2 = ""
          if (isFirst === 1) {
            if (parseInt(roadwarrant.driver_1) === parseInt(data[index].id)) {
              selected = 'selected'
            }
            if (parseInt(roadwarrant.driver_2) === parseInt(data[index].id)) {
              selected2 = 'selected'
            }
          }
          htmlDriver += '<option value="'+ data[index].id +'"'+ selected +'>'+ data[index].first_name + ' ' + data[index].second_name + ' | ' + data[index].position +'</option>'
          htmlDriver2 += '<option value="'+ data[index].id +'"'+ selected2 +'>'+ data[index].first_name + ' ' + data[index].second_name + ' | ' + data[index].position +'</option>'
        } else {
          let selectedCo = ""
          if (isFirst === 1) {
            if (parseInt(roadwarrant.codriver) === parseInt(data[index].id)) {
              selectedCo = 'selected'
            }
          }
          htmlCoDriver += '<option value="'+ data[index].id +'"'+ selectedCo +'>'+ data[index].first_name + ' ' + data[index].second_name + ' | ' + data[index].position +'</option>'
        }
      }
    }
    $('#driver1').append(htmlDriver);
    $('#driver2').append(htmlDriver2);
    $('#codriver').append(htmlCoDriver);
    pickBankList()
  }

  function tripSummary() {
    const crewMealDefault = (parseInt(tripAmount.crew_meal_allowance) + (parseInt(numberOfTrip) === 2 ? parseInt(tripAmountReturn.crew_meal_allowance) : 0)) * crewCount;
    const premiDriverDefault = (parseInt(tripAmount.driver_allowance) + (parseInt(numberOfTrip) === 2 ? parseInt(tripAmountReturn.driver_allowance) : 0)) * driverCount;
    const premiCoDriverDefault = parseInt(tripAmount.codriver_allowance) + (parseInt(numberOfTrip) === 2 ? parseInt(tripAmountReturn.codriver_allowance) : 0);
    const etollDefault = parseInt(tripAmount.etoll_allowance) + (parseInt(numberOfTrip) === 2 ? parseInt(tripAmountReturn.etoll_allowance) : 0);
    const fuelDefault = parseInt(tripAmount.fuel_allowance) + (parseInt(numberOfTrip) === 2 ? parseInt(tripAmountReturn.fuel_allowance) : 0);
    const totalAmount = crewMealDefault + premiDriverDefault + premiCoDriverDefault + etollDefault + fuelDefault + tripAllowance;

    $('.crew_meal_allowance').html('Rp' + crewMealDefault.toLocaleString('id-ID'));
    $('.driver_allowance').html('Rp' + premiDriverDefault.toLocaleString('id-ID'));
    $('.codriver_allowance').html('Rp' + premiCoDriverDefault.toLocaleString('id-ID'));
    $('.etoll_allowance').html('Rp' + etollDefault.toLocaleString('id-ID'));
    $('.fuel_allowance').html('Rp' + fuelDefault.toLocaleString('id-ID'));
    $('.totalsum').html('Rp' + totalAmount.toLocaleString('id-ID'));

    $('#crew_meal_allowance').val(crewMealDefault);
    $('#driver_allowance').val(premiDriverDefault);
    $('#codriver_allowance').val(premiCoDriverDefault);
    $('#etoll_allowance').val(etollDefault);
    $('#fuel_allowance').val(fuelDefault);
    $('#totalsum').val(totalAmount);
  }

  function datepicker1Change(value, dateSet = '') {
    const dateConv = dayjs(value).format('YYYY-MM-DD')
    const newMinDate = dayjs(value).add(1, 'day').format('YYYY-MM-DD')
    const newMaxDate = dayjs(newMinDate).add(3, 'day').format('YYYY-MM-DD')
    
    $("#datepicker_return").datetimepicker("destroy");
    if (dateSet === '') {
      $('#datepicker_return').datetimepicker({
        format: 'DD/MM/YYYY',
        minDate: newMinDate,
        maxDate: newMaxDate,
      }); 
    } else {
      $('#datepicker_return').datetimepicker({
        format: 'DD/MM/YYYY',
        minDate: newMinDate,
        maxDate: newMaxDate,
        date: dateSet
      });
    }

    fetchEmployee(dateConv)
  }
</script>
@endpush
