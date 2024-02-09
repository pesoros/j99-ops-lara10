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
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Qty</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($parts as $key => $part)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $part->part_id }}</td>
                    <td>{{ $part->part_name }}</td>
                    <td>{{ $part->qty }} pcs</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <p class="lead">Persetujuan</p>
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Role</th>
                <th>Status persetujuan</th>
                <th>Catatan</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($approval as $key => $app)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $app->title }}</td>
                    <td>
                      @if (!isset($app->status))
                        <span class="badge badge-warning">Belum ada persetujuan</span>                                        
                      @endif
                      @if (STRVAL($app->status) === '1')
                        <span class="badge badge-success">Disetujui</span>                                        
                      @endif
                      @if (STRVAL($app->status) === '2')
                        <span class="badge badge-danger">Menolak</span>                                        
                      @endif
                    </td>
                    <td>{{ $app->note ? $app->note : '-' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <form action="{{ url('letter/purchaserequest/update/approval/'.$detailPurchaseRequest->uuid) }}" method="post">
              @csrf
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="bus">Masukkan persetujuan</label>
                  <div id="damageForm">
                    <div class="row">
                      <div class="col-sm-3">
                        <select class="form-control select2bs4" name="approval_status" style="width: 100%;">
                          <option value="1" @selected(old('approval_status') == '1')>Setuju</option>
                          <option value="2" @selected(old('approval_status') == '2')>Tolak</option>
                        </select>
                      </div>
                      <div class="col-sm-7">
                        <div class="input-group mb-3">
                          <textarea class="form-control" name="approval_note" rows="1"  placeholder="Masukkan catatan" required></textarea>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <button type="submit" id="" class="btn btn-primary">Simpan</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <div class="card-footer">
              <a href="{{ url('letter/purchaserequest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
            </div>
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
