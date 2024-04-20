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
              @if (STRVAL($detailGoodsRequest->status) === '1')
                <a href="{{ url('letter/goodsrequest/update/close/'.$detailGoodsRequest->uuid) }}" onclick="return confirm('Anda yakin menyelesaikan SPB ini?')" class="btn bg-gradient-primary float-right no-print">Selesaikan SPB ini</a>
              @endif
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail permintaan barang</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nomor SPB :</th>
                  <td>{{ $detailGoodsRequest->numberid }}</td>
                </tr>
                <tr>
                  <th>Status :</th>
                  <td>
                    @if (STRVAL($detailGoodsRequest->status) === '0')
                      <span class="badge badge-secondary">Menunggu</span>                                        
                    @endif
                    @if (STRVAL($detailGoodsRequest->status) === '1')
                      <span class="badge badge-warning">Sedang diproses</span>                                        
                    @endif
                    @if (STRVAL($detailGoodsRequest->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
                    @endif
                    @if (STRVAL($detailGoodsRequest->status) === '3')
                      <span class="badge badge-danger">Ditolak</span>                                        
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col-sm-6 invoice-col">
            <p class="lead">&nbsp;</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nomor SPK terkait:</th>
                  <td>{{ $detailGoodsRequest->workorder_numberid }}</td>
                </tr>
                <tr>
                  <th>Dibuat oleh:</th>
                  <td>{{ $creator->name }}</td>
                </tr>
                <tr>
                  <th>Dibuat tanggal:</th>
                  <td>{{ dateFormat($detailGoodsRequest->created_at) }}</td>
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
            <p class="lead">Item barang</p>
            <form action="{{ url('letter/goodsrequest/update/partsaction/'.$detailGoodsRequest->uuid) }}" method="post">
              @csrf
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Bagian terkait</th>
                  <th>Item ID</th>
                  <th>Item Name</th>
                  <th>Qty</th>
                    <th>Penanganan</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($parts as $key => $part)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $part->areacode }}-{{ $part->scopecode }} | {{ $part->scopename }}</td>
                      <td>{{ $part->part_id }}</td>
                      <td>{{ $part->part_name }}</td>
                      <td>{{ $part->qty }} pcs</td>
                      <td>
                        @if (STRVAL($detailGoodsRequest->status) === '1')
                          <input type="hidden" id="parts_uuid" name="parts_uuid[]" value={{ $part->uuid }}>
                          <select class="form-control select2bs4" name="parts_status[]" style="width: 100%;">
                            <option value="0" @selected($part->status == 0)>Pilih status penanganan</option>
                            <option value="1" @selected($part->status == 1)>Direalisasikan</option>
                            <option value="2" @selected($part->status == 2)>Batal</option>
                          </select>
                        @else
                          {{ $part->status === 1 ? 'Direalisasikan' : '' }}
                          {{ $part->status === 2 ? 'Batal' : '' }}
                          {{ $part->status === 3 ? 'Dipasang' : '' }}
                          {{-- status 3 diisi oleh crew --}}
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="card-footer no-print">
                @if (permissionCheck('edit'))
                  @if (STRVAL($detailGoodsRequest->status) === '0')
                    <a href="{{ url('letter/goodsrequest/update/progress/'.$detailGoodsRequest->uuid) }}" onclick="return confirm('Anda yakin memulai SPB ini?')" class="btn bg-gradient-primary">Mulai kerjakan SPB ini</a>
                  @endif
                  @if (STRVAL($detailGoodsRequest->status) === '1')
                    <button type="submit" class="btn btn-warning">Update penanganan</button>
                  @endif
                @endif
                <a href="{{ url('letter/goodsrequest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
              </div>
            </form>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div>
          
        </div>
      </div>
      <div class="row no-print">
        <div class="col-12">
          <a href="#" rel="noopener" target="_blank" class="btn btn-default printPage"><i class="fas fa-print"></i> Print</a>
        </div>
      </div>
      <br>
      <!-- /.invoice -->
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