@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List {{ $title }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
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
          <th>Stok</th>
        </tr>
        </thead>
        <tbody id="table-item"></tbody>
      </table>
    </div>
    <!-- /.card-body -->
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
          html += '<td>'+ data[index].id +'</td>'
          html += '<td>'+ data[index].name +'</td>'
          html += '<td>'+ data[index].quantity +' Pcs</td>'
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
        html += '  <div class="col-sm-5">'
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