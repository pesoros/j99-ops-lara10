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

      function fetchBus() {
        console.log('fetch');
        axios.get(`/api/busstatus`)
          .then((response) => {
            addElementReadyWrap(response.data.busReady)
            addElementMaintenanceWrap(response.data.busMaintenance)
          }, (error) => {
            console.log(error);
          });
      }

      function addElementReadyWrap(data) {
        $('#readyWrap').empty();
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += elementGenerate(data[index])
        }
        $('#readyWrap').append(html);
      }
      
      function addElementMaintenanceWrap(data) {
        $('#maintenanceWrap').empty();
        let html = '';
        for (let index = 0; index < data.length; index++) {
          html += elementGenerate(data[index])
        }
        $('#maintenanceWrap').append(html);
      }

      function elementGenerate(data) {
        let el = ''
        const badgeColor = data.category === "AKAP" ? "info" : "secondary"
        el += '<div class="card card-danger card-outline">'
        el += '  <div class="card-header">'
        el += '    <div>'
        el += '      <h5 class="card-title">'+ data.name +'</h5>'
        el += '    </div>'
        el += '    <div class="card-tools">'
        el += '      <span class="badge badge-'+ badgeColor +'">'+ data.category +'</span>'
        el += '    </div>'
        el += '  </div>'
        if (data.damagesActive.length > 0) {
          el += '  <div class="card-body">'
          data.damagesActive.map(((val, index) => {
            el += '    <p>'+'. '+ val.scopename +' - '+ val.description +' | '+ val.action_description +'</p>'
          }))
          el += '  </div>'
        }
        el += '</div>'

        return el
      }

      window.setInterval(function () {
        fetchBus()
      }, 60000); // 60000 means 5 minutes
      
      fetchBus()
    });
</script>
@endpush