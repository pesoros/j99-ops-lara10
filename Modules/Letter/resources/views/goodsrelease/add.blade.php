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
          <button type="button" class="btn btn-secondary btn-sm" id="addRow">
            Tambah barang
          </button>
        </div>
        <div class="form-group">
          <div id="partForm"></div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('letter/goodsrelease') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>

<div class="modal fade" id="item-modal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pilih item barang untuk</h4>
        <input type="hidden" id="damage-row" value="">
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
            <th>Item No</th>
            <th>Nama Item</th>
            <th>Stok</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody id="table-item"></tbody>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="modal-close" class="btn btn-success" data-dismiss="modal">Kembali</button>
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
      let rowState = 0;
      let rowCount = 0;

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
          html += '<td>'+ data[index].no +'</td>'
          html += '<td>'+ data[index].name +'</td>'
          html += '<td>'+ data[index].quantity +' Pcs</td>'
          html += '<td><div class="btn-group btn-block">'
          html += '<a type="button" id="'+ data[index].no + '-sprt-' + data[index].name +'" class="btn btn-warning getItem">Pilih</a>';
          html += '</div></td>'
          html += '</tr>'
        }
        $('#table-item').append(html);
        $('.getItem').click(function(){
          addItem(this.id);
          $("#modal-close").click()
        });
      }

      function addItem(data) {
        const item = data.split("-sprt-")
        const partname = document.getElementById("part_id_" + rowState);
        partname.value = item[0];
        const partid = document.getElementById("part_name_" + rowState);
        partid.value = item[1];
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
      
      $('#addRow').click(function(){
        let html = '';
        rowCount++;
        html += '<div class="row" id="damage_'+ rowCount +'">'
        html += '  <div class="col-sm-4">'
        html += '    <div class="input-group mb-3">'
        html += '      <input type="text" class="form-control" id="part_name_'+ rowCount +'" name="part_name[]" value="" placeholder="Klik untuk pilih barang" data-drow="'+ rowCount +'" data-toggle="modal" data-target="#item-modal" readonly>'
        html += '      <input type="hidden" id="part_id_'+ rowCount +'" name="part_id[]" value="">'
        html += '    </div>'
        html += '  </div>'
        html += '  <div class="col-sm-2">'
        html += '    <div class="input-group mb-3">'
        html += '      <div class="input-group-prepend"><span class="input-group-text">Qty</span></div><input type="number" class="form-control" id="part_qty'+ rowCount +'" name="part_qty[]" value="1" required>'
        html += '    </div>'
        html += '  </div>'
        html += '  <div class="col-sm-1">'
        html += '    <a type="button" id="'+ rowCount +'" class="btn btn-danger removeRow">Hapus</a>'
        html += '  </div>'
        html += '</div>'
        $('#partForm').append(html);
        $('.select2bs4:last').select2({
          theme: 'bootstrap4'
        });
        $('.removeRow').click(function(){
          const id = this.id; 
          $('#damage_'+id+'').remove();
        });
        $('#part_name_'+ rowCount).click(function () {
          let damagerow = '';
          if (typeof $(this).data('drow') !== 'undefined') {
            damagerow = $(this).data('drow');
          }
          rowState = damagerow;
        })
      });
    });
</script>
@endpush
