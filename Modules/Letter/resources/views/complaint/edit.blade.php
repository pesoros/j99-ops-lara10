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
          <label>Bus</label>
          <select class="form-control select2bs4" name="bus_uuid" style="width: 100%;">
            @foreach ($bus as $busItem)
                <option value="{{ $busItem->uuid }}" @selected($detailComplaint->bus_uuid == $busItem->uuid)>
                    {{ $busItem->name }} | {{ $busItem->registration_number }}
                </option>
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea class="form-control" name="description" rows="3" placeholder="Masukkan deskripsi keluhan">{{ $detailComplaint->description }}</textarea>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <label for="bus">Kerusakan</label>
          <div id="damageForm">
            @foreach ($damages as $key => $item)
              <div class="row">
                <div class="col-sm-3">
                  <select class="form-control select2bs4" name="damage_scope[]" style="width: 100%;">
                    @foreach ($partsscope as $partsscopeItem)
                        <option value="{{ $partsscopeItem->uuid }}" @selected($item->scope_uuid == $partsscopeItem->uuid)>
                            {{ $partsscopeItem->scope_name }} | {{ $partsscopeItem->name }} | {{ $partsscopeItem->scope_code }}-{{ $partsscopeItem->code }}
                        </option>
                    @endForeach
                  </select>
                </div>
                <div class="col-sm-7">
                  <div class="input-group mb-3">
                    <textarea class="form-control {{'damage_detail_'.$key + 1}}" name="damage_detail[]" rows="1"  placeholder="Masukkan detail kerusakan" required>{{ $item->description }}</textarea>
                  </div>
                </div>
                <div class="col-sm-2">
                  <a type="button" id="addRow" class="btn btn-success">Tambah</a>
                </div>
              </div>
            @endforeach
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
<script type="text/javascript">
    $(function () {
      const partsScopeData = {!!json_encode($partsscope)!!};
      let rowCount = {!! COUNT($damages) !!};
      
      $('#addRow').click(function(){
        let html = '';
        rowCount++;
        html += '<div class="row" id="damage_'+ rowCount +'"><div class="col-sm-3"><select class="form-control select2bs4" name="damage_scope[]" style="width: 100%;">';
        for (let index = 0; index < partsScopeData.length; index++) {
          html += '<option value="' + partsScopeData[index].uuid + '">' + partsScopeData[index].scope_name + ' | ' + partsScopeData[index].name + ' | ' + partsScopeData[index].scope_code + '-' + partsScopeData[index].code + '</option>';
        }
        html += '</select></div><div class="col-sm-7"><div class="input-group mb-3"><div class="input-group-prepend">';
        html += '</div><textarea class="form-control damage_detail_'+ rowCount +'" name="damage_detail[]" rows="1" placeholder="Masukkan detail kerusakan" required></textarea></div></div><div class="col-sm-1">';
        html += '<a type="button" id="'+ rowCount +'" class="btn btn-danger removeRow">Hapus</a>';
        $('#damageForm').append(html);
        $('.select2bs4:last').select2({
          theme: 'bootstrap4'
        });
        $('.removeRow').click(function(){
          const id = this.id; 
          $('#damage_'+id+'').remove();
        });
      });
    });
</script>
@endpush
