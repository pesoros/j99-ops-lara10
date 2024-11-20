@extends('layouts.main', ['title' => $title ])

@section('content')
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
  <div class="row">
    <div class="col-12">
      <!-- Main content -->
      <div class="invoice p-3 mb-3">
        <div class="row">
          <div class="col-12">
            <h4>
              <img src="{{url('assets/images/logo/j99-logo-wide.png')}}" alt="J99 Logo" height="38" style="opacity: .8">
              @if (permissionCheck('add'))
              <div class="float-right no-print">
                <p data-toggle="tooltip" title="{{ COUNT($damages) > 0 ? '' : 'Belum ada complaint' }}">
                  <p
                    class="btn bg-gradient-primary btn-sm {{ COUNT($damages) > 0 ? '' : 'disabled' }}"
                    data-toggle="modal" data-target="#item-modal" 
                  >
                    Buat SPK berdasarkan keluhan ini
                  </a>
                </p>
              </div>
            @endif
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail Bus</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama bus :</th>
                  <td>{{ $bus->name }}</td>
                </tr>
                <tr>
                  <th>Nomor registrasi :</th>
                  <td>{{ $bus->registration_number }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-6 invoice-col">
            <p class="lead">&nbsp;</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Merk :</th>
                  <td>{{ $bus->brand }}</td>
                </tr>
                <tr>
                  <th>Model :</th>
                  <td>{{ $bus->model }}</td>
                </tr>
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
          <div class="col-12 table-responsive">
            <p class="lead">Kerusakan</p>
            @if (COUNT($damages) > 0)
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Bagian</th>
                  <th>Deskripsi</th>
                  <th>Tanggal lapor</th>
                  <th>SPK</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($damages as $key => $damage)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $damage->areaname }} | {{ $damage->scopename }} | {{ $damage->areacode }}-{{ $damage->scopecode }}</td>
                      <td>{{ $damage->description }}</td>
                      <td>{{ dateFormat($damage->created_at) }}</td>
                      <td>{{ $damage->numberid ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="text-center"><p>Belum ada laporan kerusakan</p></div>
            @endif
            </div>
          </div>
        </div>
        
        <div class="card card-primary no-print">
          <form action="{{ url('letter/complaint/add') }}" method="post">
            @csrf
            <div class="card-body row">
              <input type="hidden" class="form-control" id="bus_uuid" name="bus_uuid" value="{{ $bus->uuid }}">
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="bus">Tambah laporan kerusakan</label>
                  <div class="row">
                    <div class="col-sm-3">
                      <select class="form-control select2bs4" name="damage_scope" style="width: 100%;">
                        @foreach ($partsscope as $partsscopeItem)
                            <option value="{{ $partsscopeItem->uuid }}" @selected(old('partsscope_uuid') == $partsscopeItem->uuid)>
                                {{ $partsscopeItem->scope_name }} | {{ $partsscopeItem->name }} | {{ $partsscopeItem->scope_code }}-{{ $partsscopeItem->code }}
                            </option>
                        @endForeach
                      </select>
                    </div>
                    <div class="col-sm-7">
                      <div class="input-group mb-3">
                        <textarea class="form-control damage_detail" name="damage_detail" rows="1"  placeholder="Masukkan detail kerusakan" required></textarea>
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <button type="submit" class="btn btn-success">Tambah</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="row invoice p-3 mb-3">
          <div class="col-12 table-responsive">
            <p class="lead">SPK aktif terkait </p>
            @if (COUNT($workorders) > 0)
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Nomor SPK</th>
                  <th class="no-print">Aksi</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($workorders as $key => $workorder)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $workorder->numberid }}</td>
                      <td class="no-print">
                        <div class="btn-group btn-block">
                          @if (permissionCheck('show')) <a href="{{ url('letter/workorder/show/detail/'.$workorder->uuid) }}" class="btn btn-warning btn-sm">Detail</a> @endif
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="text-center"><p>Belum ada laporan kerusakan</p></div>
            @endif
            </div>
          </div>
        </div>
        
        <div class="row no-print mb-3">
          <div class="col-12">
            <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
          </div>
        </div>
      </div>
    </div><!-- /.col -->
  </div><!-- /.row -->

  <div class="modal fade" id="item-modal">
    <div class="modal-dialog modal-xl">
      <form action="{{ url('letter/complaint/add/createworkorder/'.$bus->uuid) }}" method="post">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Buat SPK</h4>
            <input type="hidden" id="damage-row" value="">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <div class="col-12">
                <div class="form-group">
                  <label>Pilih Kerusakan</label>
                  <div class="select2-purple">
                    <select name="damages_select[]" class="select2" multiple="multiple" data-placeholder="Pilih kerusakan" data-dropdown-css-class="select2-purple" style="width: 100%;">
                      @foreach ($damages as $key => $damage)
                        @if (!$damage->numberid)
                          <option value="{{ $damage->uuid }}">{{ $damage->areacode }}-{{ $damage->scopecode }} | {{ $damage->scopename }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>
                <!-- /.form-group -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="modal-close" class="btn btn-success" data-dismiss="modal">Kembali</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
</div>
 
@endsection
@push('extra-scripts')
<script type="text/javascript">
    $(function () {
      $('a.printPage').click(function(){
           window.print();
           return false;
      });
    });
</script>
@endpush