@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <div class="card-header no-print">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body no-print">
      <div class="form-group">
        <label for="email">Nama</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan email" value="{{ $email }}">
      </div>
      <button type="button" onclick="return onSearch()" class="btn btn-primary">Cari</button>
    </div>
    <!-- /.card-body -->
  </div>

    <div class="row">
      <div class="col-12">
        @if (isset($userdata->first_name))
            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <img src="{{url('assets/images/logo/j99-logo-wide.png')}}" alt="J99 Logo" height="38" style="opacity: .8">
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
                        <th>Email :</th>
                        <td>{{ $userdata->email }}</td>
                      </tr>
                      <tr>
                        <th>Nama user :</th>
                        <td>{{ $userdata->first_name }} {{ $userdata->last_name }}</td>
                      </tr>
                      <tr>
                        <th>Point :</th>
                        <td>{{ formatAmountNoRp($userdata->point) }}</td>
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
                  <p class="lead">Riwayat Point</p>
                    <table class="table">
                      <thead>
                        <tr>
                          <th width="3">No</th>
                          <th>Mutasi</th>
                          <th>Point</th>
                          <th>Booking Code</th>
                          <th>Catatan</th>
                          <th>Dibuat tanggal</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($userpointhistory as $key => $userpoint)
                          <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                              @if ($userpoint->is_debit)
                                <span class="badge badge-warning">Debit</span>
                              @else 
                                <span class="badge badge-success">Kredit</span>
                              @endif
                            </td>
                            <td>{{ formatAmountNoRp($userpoint->point) }}</td>
                            <td>{{ $userpoint->booking_code }}</td>
                            <td>{{ $userpoint->note ?? '-' }}</td>
                            <td>{{ dateTimeFormat($userpoint->created_at) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
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
        @else
          <h4 class="text-center">Mohon masukkan email dengan benar</h4>
        @endif
      </div><!-- /.row -->
    </div>

 
@endsection

@push('extra-scripts')
<script type="text/javascript">

  function onSearch() {
    const emailform = $("#email").val();
    window.location.replace(`?email=${emailform}`);
  }

  $('a.printPage').click(function(){
        window.print();
        return false;
  });

</script>
@endpush