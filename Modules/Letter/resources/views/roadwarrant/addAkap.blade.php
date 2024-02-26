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
              <option value="">Pilih bus</option>
              @foreach ($bus as $busItem)
                <option value="{{ $busItem->busuuid }}">
                    {{ $busItem->busname }}
                </option>
              @endForeach
            </select>
          </div>
          <div class="form-group">
            <label>Trip assign</label>
            <select class="form-control select2bs4" name="trip_assign" id="tras-item" style="width: 100%;" required>
              <option value="" @selected(old('trip_assign') == '')>Pilih trip assign</option>
              {{-- @foreach ($tripAssign as $tripAssignItem)
                <option value="{{ $tripAssignItem->trasid }}" @selected(old('trip_assign') == $tripAssignItem->trasid)>
                    {{ $tripAssignItem->trasid }} | {{ $tripAssignItem->trip_title }}
                </option>
              @endForeach --}}
            </select>
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
        console.log(response.data)
        addElementToSelect(response.data);
      }, (error) => {
        console.log(error);
      });
  }

  function addElementToSelect(data) {
    let html = '';
    html += '<option value="">Pilih bus</option>'
    for (let index = 0; index < data.length; index++) {
      html += '<option value="'+ data[index].trasid +'">'+ data[index].trip_title +'</option>'
    }
    $('#tras-item').append(html);
  }
</script>
@endpush
