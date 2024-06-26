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
        <!-- title row -->
        <div class="row">
          <div class="col-12">
            <h4>
              <img src="{{url('assets/images/logo/j99-logo-wide.png')}}" alt="J99 Logo" height="38" style="opacity: .8">
              @if (STRVAL($detailWorkorder->status) === '1')
                <a href="{{ url('letter/workorder/update/close/'.$detailWorkorder->uuid) }}" onclick="return confirm('Anda yakin menyelesaikan SPK ini?')" class="btn bg-gradient-primary float-right no-print">Selesaikan SPK ini</a>
              @endif
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-12 invoice-col">
            <p class="lead">Detail complaint</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama bus :</th>
                  <td>{{ $detailWorkorder->busname }}</td>
                </tr>
                <tr>
                  <th>Nomor SPK :</th>
                  <td>{{ $detailWorkorder->numberid }}</td>
                </tr>
                <tr>
                  <th>Status :</th>
                  <td>
                    @if (STRVAL($detailWorkorder->status) === '0')
                      <span class="badge badge-danger">Belum dikerjakan</span>                                        
                    @endif
                    @if (STRVAL($detailWorkorder->status) === '1')
                      <span class="badge badge-warning">Sedang dikerjakan</span>                                        
                    @endif
                    @if (STRVAL($detailWorkorder->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Dibuat tanggal :</th>
                  <td>{{ dateFormat($detailWorkorder->created_at) }}</td>
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
            <form action="{{ url('letter/workorder/update/damagesaction/'.$detailWorkorder->uuid) }}" method="post">
              @csrf
              <table class="table">
                <thead>
                  <tr>
                    <th width="3">No</th>
                    <th>Bagian</th>
                    <th>Deskripsi</th>
                    @if (STRVAL($detailWorkorder->status) !== '0')
                      <th>Penanganan</th>
                      <th>Detail penanganan</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @foreach ($damages as $key => $damage)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $damage->areacode }}-{{ $damage->scopecode }} | {{ $damage->scopename }}</td>
                      <td>{{ $damage->description }}</td>
                      @if (STRVAL($detailWorkorder->status) !== '0')
                        <td>
                          <input type="hidden" id="damage_uuid" name="damage_uuid[]" value={{ $damage->uuid }}>
                          @if (STRVAL($detailWorkorder->status) !== '1')
                            {{ $damage->action_name }}
                          @else
                            <select class="form-control select2bs4" name="action_status[]" style="width: 100%;">
                              @foreach ($actionlist as $actionlistItem)
                                  <option value="{{ $actionlistItem->id }}" @selected($damage->action_status == $actionlistItem->id)>
                                      {{ $actionlistItem->name }}
                                  </option>
                              @endForeach
                            </select>
                          @endif
                        </td>
                        <td>
                          @if (STRVAL($detailWorkorder->status) !== '1')
                            {{ $damage->action_description }}
                          @else
                            <textarea class="form-control" name="action_description[]" rows="1" placeholder="Masukkan detail penanganan">{{ $damage->action_description }}</textarea>
                          @endif
                        </td>
                      @endif
                    </tr>
                    @if (COUNT($damage->parts_request) > 0)
                      @foreach ($damage->parts_request as $part)
                        <tr>
                          <td></td>
                          <td colspan="5">{{ $part->qty }} pcs | {{ $part->part_id }} - {{ $part->part_name }}</td>
                        </tr>
                      @endforeach
                    @endif
                  @endforeach
                </tbody>
              </table>
              <div class="card-footer no-print">
                @if (permissionCheck('edit'))
                  @if (STRVAL($detailWorkorder->status) === '0')
                    <a href="{{ url('letter/workorder/update/progress/'.$detailWorkorder->uuid) }}" onclick="return confirm('Anda yakin memulai SPK ini?')" class="btn bg-gradient-primary">Mulai kerjakan SPK ini</a>
                  @endif
                  @if (STRVAL($detailWorkorder->status) === '1')
                    <button type="submit" class="btn btn-warning">Update penanganan</button>
                  @endif
                @endif
                <a href="{{ url('letter/workorder') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
                @if (permissionCheck('add','goodsrequest') && (STRVAL($detailWorkorder->status) === '1')) <a href="{{ url('letter/goodsrequest/add?workorder_uuid='.$detailWorkorder->uuid) }}" class="btn btn-secondary float-right">Buat SPB (Surat Permintaan Barang)</a> @endif
              </div>
            </form>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div>
          
        </div>
      </div>
      <!-- /.invoice -->
      <div class="row no-print">
        <div class="col-12">
          <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
        </div>
      </div>
      <br>
    </div><!-- /.col -->
  </div><!-- /.row -->
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