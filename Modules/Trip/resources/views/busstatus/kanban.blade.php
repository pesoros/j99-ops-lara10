@extends('layouts.main', ['title' => $title ])

@section('content')

<div class="row">
  <div class="col-6">
    <div class="card card-row card-success">
      <div class="card-header">
        <h3 class="card-title">
          Ready
        </h3>
      </div>
      <div class="card-body" id="readyWrap"></div>
    </div>
  </div>
  <div class="col-6">
    <div class="card card-row card-warning">
      <div class="card-header">
        <h3 class="card-title">
          Service
        </h3>
      </div>
      <div class="card-body" id="maintenanceWrap"></div>
    </div>
  </div>
</div>

@endsection

@push('extra-scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    $(function () {

      function fetchItem() {
        axios.get(`/api/busstatus`)
          .then((response) => {
            addElementReadyWrap(response.data.busReady)
            addElementMaintenanceWrap(response.data.busMaintenance)
          }, (error) => {
            console.log(error);
          });
      }

      function addElementReadyWrap(data) {
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += elementGenerate(data[index])
        }
        $('#readyWrap').append(html);
      }
      
      function addElementMaintenanceWrap(data) {
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += elementGenerate(data[index])
        }
        $('#maintenanceWrap').append(html);
      }

      function elementGenerate(data) {
        let el = '';
        el += '<div class="card card-danger card-outline">'
        el += '  <div class="card-header">'
        el += '    <h5 class="card-title">Gavan</h5>'
        el += '    <div class="card-tools">'
        el += '      <a href="#" class="btn btn-tool btn-link">-</a>'
        el += '    </div>'
        el += '  </div>'
        el += '</div>'

        return el;
      }

      fetchItem('')
    });
</script>
@endpush