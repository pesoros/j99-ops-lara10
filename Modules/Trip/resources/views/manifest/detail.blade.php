@extends('layouts.main', ['title' => $title ])

@section('content')
<style>
#loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 1051;
}

.loading-box {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.loader {
    border: 16px solid #f3f3f3;
    border-top: 16px solid #3498db;
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div id="loading-overlay">
    <div class="loading-box">
        <div class="loader"></div>
        <p style="margin-top: 20px; font-size: 18px; color: #333;">Mengirim reminder, Mohon tunggu...</p>
    </div>
</div>

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
            @if (STRVAL($detailManifest->status) === '1')
            <a href="{{ url('trip/manifest/close/'.$detailManifest->id) }}" onclick="return confirm('Anda yakin menyelesaikan Manifest ini?')" class="btn bg-gradient-primary float-right no-print">Selesaikan manifest ini</a>
            @else
            <a href="{{ url('trip/manifest/open/'.$detailManifest->id) }}" onclick="return confirm('Anda yakin mermbuks Manifest ini?')" class="btn bg-gradient-danger float-right no-print">Aktifkan kembali manifest ini</a>
            @endif
            @php
                $passengersToRemind = $passengerList->filter(function($passenger) {
                    return is_null($passenger->reminderSucceed) || $passenger->reminderSucceed == 0;
                });
            @endphp
            <button id="broadcastButton" class="btn bg-gradient-warning float-right mr-1 no-print" @if($passengersToRemind->isEmpty()) disabled @endif>Broadcast Reminder</button>
            <a href="{{ url('trip/manifest') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success float-right mr-1 no-print">Kembali</a>
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-6 invoice-col">
            <p class="lead">Detail manifest</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama trip :</th>
                  <td>
                    {{ $detailManifest->trip_title }}
                  </td>
                </tr>
                <tr>
                  <th>Tanggal keberangkatan :</th>
                  <td>
                    {{ dateFormat($detailManifest->trip_date) }}
                  </td>
                </tr>
                <tr>
                  <th>Status :</th>
                  <td>
                    @if (STRVAL($detailManifest->status) === '1')
                      <span class="badge badge-warning">Aktif</span>                                        
                    @endif
                    @if (STRVAL($detailManifest->status) === '2')
                      <span class="badge badge-success">Selesai</span>                                        
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
                  <th>Armada :</th>
                  <td>
                    {{ $detailManifest->fleetname ?? $detailManifest->busname }}
                  </td>
                </tr>
                <tr>
                  <th>Driver 1:</th>
                  <td>
                    {{ $detailManifest->driver1_name }} {{ $detailManifest->driver1_lastname }}
                  </td>
                </tr>
                <tr>
                  <th>Driver 2 :</th>
                  <td>
                    {{ $detailManifest->driver2_name }} {{ $detailManifest->driver2_lastname }}
                  </td>
                </tr>
                <tr>
                  <th>Co Driver :</th>
                  <td>
                    {{ $detailManifest->codriver_name }} {{ $detailManifest->codriver_lastname }}
                  </td>
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
            <p class="lead">List penumpang</p>
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Nama</th>
                <th>Kode booking</th>
                <th>Nomor tiket</th>
                <th>Kursi</th>
                <th>Makanan</th>
                <th>Titik jemput</th>
                <th>Titik turun</th>
                <th>Aksi</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($passengerList as $key => $passenger)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $passenger->name }} <br> <i class="fas fa-phone"></i> {{ numberSpacer($passenger->phone) }}</td>
                    <td>{{ $passenger->booking_code }}</td>
                    <td>{{ $passenger->ticket_number }}</td>
                    <td>{{ $passenger->seat_number }} | {{ $passenger->class }}</td>
                    <td>{{ $passenger->food_name }}</td>
                    <td>{{ $passenger->pickup_trip_location }} {{ substr($passenger->dep_time, 0, 5) }}</td>
                    <td>{{ $passenger->drop_trip_location }} {{ substr($passenger->arr_time, 0, 5) }}</td>
                    <td>
                        @if(is_null($passenger->reminderSucceed) || $passenger->reminderSucceed == 0)
                            <button class="btn btn-sm btn-info single-reminder-btn" data-ticket-number="{{ $passenger->ticket_number }}">Reminder</button>
                        @endif
                    </td>
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
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
@push('extra-scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    const passengerList = @json($passengerList);
    const passengersToRemind = passengerList.filter(passenger => passenger.reminderSucceed === null || passenger.reminderSucceed == 0);
    const reminderUrl = @json($reminderUrl);
    const manifestId = @json($manifestId);

    function showLoading() {
        $('#loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('#loading-overlay').css('display', 'none');
    }

    function postReminder(ticketNumber) {
        return axios.post(reminderUrl, {
            manifestId: manifestId,
            ticketNumber: ticketNumber
        });
    }

    function sendReminder(index) {
        if (index >= passengersToRemind.length) {
            hideLoading();
            console.log('All reminders sent.');
            alert('All reminders have been sent.');
            $('#broadcastButton').prop('disabled', false).text('Broadcast Reminder');
            location.reload();
            return;
        }

        const passenger = passengersToRemind[index];
        const ticketNumber = passenger.ticket_number;

        console.log(`Sending reminder for ticket: ${ticketNumber}`);

        postReminder(ticketNumber)
            .then(response => {
                console.log(`Successfully sent reminder for ticket: ${ticketNumber}`, response.data);
            })
            .catch(error => {
                console.error(`Failed to send reminder for ticket: ${ticketNumber}`, error);
            })
            .finally(() => {
                setTimeout(() => {
                    sendReminder(index + 1);
                }, 3000);
            });
    }

    function sendSingleReminder(ticketNumber) {
        showLoading();
        console.log(`Sending single reminder for ticket: ${ticketNumber}`);

        postReminder(ticketNumber)
            .then(response => {
                console.log(`Successfully sent reminder for ticket: ${ticketNumber}`, response.data);
                alert(`Reminder sent for ticket: ${ticketNumber}`);
            })
            .catch(error => {
                console.error(`Failed to send reminder for ticket: ${ticketNumber}`, error);
                alert(`Failed to send reminder for ticket: ${ticketNumber}`);
            })
            .finally(() => {
                hideLoading();
                location.reload();
            });
    }

    $(function () {
      $('#broadcastButton').click(function(){
            if (passengersToRemind.length === 0) {
                alert('All passengers have already received a reminder.');
                return;
            }
           if (confirm('Are you sure you want to broadcast reminders to all passengers who have not received one?')) {
               showLoading();
               console.log('Starting to send reminders...');
               $(this).prop('disabled', true).text('Sending...');
               sendReminder(0);
           }
      });

      $('.single-reminder-btn').click(function(){
          const ticketNumber = $(this).data('ticket-number');
          if (confirm(`Are you sure you want to send a reminder for ticket ${ticketNumber}?`)) {
              sendSingleReminder(ticketNumber);
          }
      });

      $('a.printPage').click(function(){
           window.print();
           return false;
      });
    });
</script>
@endpush
