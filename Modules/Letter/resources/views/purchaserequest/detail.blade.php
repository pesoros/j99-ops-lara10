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
              @if (STRVAL($detailPurchaseRequest->status) === '1')
                <a href="{{ url('letter/purchaserequest/update/close/'.$detailPurchaseRequest->uuid) }}" onclick="return confirm('Anda yakin menyelesaikan SPB ini?')" class="btn bg-gradient-primary float-right">Selesaikan SPB ini</a>
              @endif
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail complaint</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nomor surat pengadaan :</th>
                  <td>{{ $detailPurchaseRequest->numberid }}</td>
                </tr>
                <tr>
                  <th>Status :</th>
                  <td>
                    @if (STRVAL($detailPurchaseRequest->status) === '0')
                      <span class="badge badge-secondary">Menunggu</span>                                        
                    @endif
                    @if (STRVAL($detailPurchaseRequest->status) === '1')
                      <span class="badge badge-warning">Sedang diproses</span>                                        
                    @endif
                    @if (STRVAL($detailPurchaseRequest->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
                    @endif
                    @if (STRVAL($detailPurchaseRequest->status) === '3')
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
                  <th>Dibuat oleh:</th>
                  <td>{{ $creator->name }}</td>
                </tr>
                <tr>
                  <th>Dibuat tanggal:</th>
                  <td>{{ dateFormat($detailPurchaseRequest->created_at) }}</td>
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
            <form action="{{ url('letter/purchaserequest/update/partsaction/'.$detailPurchaseRequest->uuid) }}" method="post">
              @csrf
              <table class="table table-striped">
                <thead>
                <tr>
                  <th width="3">No</th>
                  <th>Item ID</th>
                  <th>Item Name</th>
                  <th>Qty</th>
                  @if (STRVAL($detailPurchaseRequest->status) === '1')
                    <th>Penanganan</th>
                    <th>Detail penanganan</th>
                  @endif
                </tr>
                </thead>
                <tbody>
                  @foreach ($parts as $key => $part)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ $part->part_id }}</td>
                      <td>{{ $part->part_name }}</td>
                      <td>{{ $part->qty }} pcs</td>
                      @if (STRVAL($detailPurchaseRequest->status) === '1')
                        <td>
                          <input type="hidden" id="parts_uuid" name="parts_uuid[]" value={{ $part->uuid }}>
                          <select class="form-control select2bs4" name="parts_status[]" style="width: 100%;">
                            <option value="0" @selected($part->status == 0)>Menunggu</option>
                            <option value="1" @selected($part->status == 1)>Direalisasikan</option>
                            <option value="2" @selected($part->status == 2)>Batal</option>
                          </select>
                        </td>
                        <td>
                          <textarea class="form-control" name="parts_description[]" rows="1" placeholder="Masukkan detail penanganan">{{ $part->description }}</textarea>
                        </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
              <div class="card-footer">
                @if (permissionCheck('edit'))
                  @if (STRVAL($detailPurchaseRequest->status) === '0')
                    <a href="{{ url('letter/purchaserequest/update/progress/'.$detailPurchaseRequest->uuid) }}" onclick="return confirm('Anda yakin memulai SPB ini?')" class="btn bg-gradient-primary">Mulai kerjakan SPB ini</a>
                  @endif
                  @if (STRVAL($detailPurchaseRequest->status) === '1')
                    <button type="submit" class="btn btn-warning">Update penanganan</button>
                  @endif
                @endif
                <a href="{{ url('letter/purchaserequest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
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
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
