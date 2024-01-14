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
          <label>Nomor SPK</label>
          <select class="form-control select2bs4" name="workorder_uuid" style="width: 100%;">
            @foreach ($workorder as $workorderItem)
                <option value="{{ $workorderItem->uuid }}" @selected(old('workorder_uuid') == $workorderItem->uuid)>
                    {{ $workorderItem->numberid }}
                </option>
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea class="form-control" name="description" rows="3" placeholder="Masukkan deskripsi">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
          <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#item-modal">
            Tambah item barang
          </button>
        </div>
        <div class="form-group">
          <div id="partForm"></div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('letter/goodsrequest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>

<div class="modal fade" id="item-modal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pilih item barang</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="keyword">Masukkan kata kunci</label>
          <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Masukkan kata kunci">
        </div>
        <table class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>No</th>
            <th>ID Item</th>
            <th>Nama Item</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody id="table-item"></tbody>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-success" data-dismiss="modal">Batal</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
 
@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    $(function () {      
      let count = 0;

      $("#keyword").keyup(function(e){
        fetchItem(e.target.value)
      });

      const fetchItem = debounce(value => {
        $('#table-item').html('');
        axios.get(`/api/spareparts?keyword=${value}`)
          .then((response) => {
            addElementToSelect(response.data);
          }, (error) => {
            console.log(error);
          });
      }, 1000)

      function addElementToSelect(data) {
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += '<tr>'
          html += '<td width="20" class="text-center">'+ (index + 1) +'</td>'
          html += '<td>'+ data[index].id +'</td>'
          html += '<td>'+ data[index].name +'</td>'
          html += '<td><div class="btn-group btn-block">'
          html += '<a type="button" id="'+ data[index].id + '-sprt-' + data[index].name +'" class="btn btn-warning getItem">Pilih</a>';
          html += '</div></td>'
          html += '</tr>'
        }
        $('#table-item').append(html);
        $('.getItem').click(function(){
          addItem(this.id);
        });
      }

      function addItem(data) {
        count++
        const item = data.split("-sprt-")
        let html = ''
        html += '<div class="row" id="part_'+ count +'">'
        html += '  <div class="col-sm-2">'
        html += '    <div class="input-group mb-3">'
        html += '      <input type="text" class="form-control" id="part_id'+ count +'" name="part_id[]" value="'+ item[0] +'" readonly>'
        html += '    </div>'
        html += '  </div>'
        html += '  <div class="col-sm-5">'
        html += '    <div class="input-group mb-3">'
        html += '      <input type="text" class="form-control" id="part_name'+ count +'" name="part_name[]" value="'+ item[1] +'" readonly>'
        html += '    </div>'
        html += '  </div>'
        html += '  <div class="col-sm-2">'
        html += '    <div class="input-group mb-3">'
        html += '      <div class="input-group-prepend"><span class="input-group-text">Qty</span></div><input type="number" class="form-control" id="part_qty'+ count +'" name="part_qty[]" value="1" required>'
        html += '    </div>'
        html += '  </div>'
        html += '  <div class="col-sm-2">'
        html += '    <a type="button" id="'+ count +'" class="btn btn-danger removeRow">Hapus</a>'
        html += '  </div>'
        html += '</div>'

        $('#partForm').append(html);
        $('.removeRow').click(function(){
          const id = this.id; 
          $('#part_'+id+'').remove();
        });
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
