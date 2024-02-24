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
  <form action="{{ url()->current() }}" method="post">
    @csrf
    <div class="card-body row">
      <div class="col-sm-12">
        <div class="form-group">
          <label>Tanggal :</label>
            <div class="input-group date" id="datepicker" data-target-input="nearest">
                <input type="text" name="date" class="form-control datetimepicker-input" data-target="#datepicker"/>
                <div class="input-group-append" data-target="#datepicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
            </div>
        </div>
        <div class="form-group">
          <label>Bus</label>
          <select class="form-control select2bs4" name="bus_uuid" style="width: 100%;">
            @foreach ($tripAssign as $tripAssignItem)
                <option value="{{ $tripAssignItem->trasid }}" @selected(old('tripAssign_uuid') == $tripAssignItem->trasid)>
                    {{ $tripAssignItem->trasid }} | {{ $tripAssignItem->trip_title }}
                </option>
            @endForeach
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
<script type="text/javascript">
  let maxDate = dayjs().add(7, 'day').format('YYYY-MM-DD')
  $('#datepicker').datetimepicker({
      format: 'DD/MM/YYYY',
      minDate : 'now',
      maxDate : maxDate,
  });
</script>
@endpush
