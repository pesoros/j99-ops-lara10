<div class="invoice p-3 mb-3 no-print">
  <div class="row">
    <div class="col-12">
      <h5>Persetujuan Rekap Pengeluaran</h5>

      @php
        $approvalStatusLabels = [
          'pending_operational' => ['Menunggu Operational', 'badge-warning'],
          'rejected_operational' => ['Ditolak Operational', 'badge-danger'],
          'pending_accounting' => ['Menunggu Accounting', 'badge-info'],
          'rejected_accounting' => ['Ditolak Accounting', 'badge-danger'],
          'approved' => ['Disetujui', 'badge-success'],
        ];
        $approvalStatus = $approvalStatusLabels[$expenseRecapApproval['status']];
      @endphp

      @if (intval($roadwarrant->status) < 5)
        <div class="alert alert-light mb-0">
          Persetujuan dapat dimulai setelah status perjalanan selesai.
        </div>
      @else
        <p>
          Status:
          <span class="badge {{ $approvalStatus[1] }}">{{ $approvalStatus[0] }}</span>
        </p>

        @if ($expenseRecapApproval['history']->isNotEmpty())
          <div class="table-responsive mb-3">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>Tahap</th>
                  <th>Keputusan</th>
                  <th>Oleh</th>
                  <th>Catatan</th>
                  <th>Waktu</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($expenseRecapApproval['history'] as $approvalItem)
                  <tr>
                    <td>{{ ucfirst($approvalItem->stage) }}</td>
                    <td>
                      @if ($approvalItem->decision === 'approved')
                        <span class="badge badge-success">Disetujui</span>
                      @elseif ($approvalItem->decision === 'rejected')
                        <span class="badge badge-danger">Ditolak</span>
                      @else
                        <span class="badge badge-secondary">Dibatalkan karena perubahan transaksi</span>
                      @endif
                    </td>
                    <td>{{ $approvalItem->decided_by_name ?? '-' }}</td>
                    <td>{{ $approvalItem->note }}</td>
                    <td>{{ $approvalItem->created_at }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        @php
          $canOperationalDecide = $roleInfo->role_slug === 'operational'
            && in_array($expenseRecapApproval['status'], ['pending_operational', 'rejected_operational', 'rejected_accounting']);
          $canAccountingDecide = $roleInfo->role_slug === 'accounting'
            && $expenseRecapApproval['status'] === 'pending_accounting';
        @endphp

        @if ($canOperationalDecide || $canAccountingDecide)
          <form action="{{ url('letter/roadwarrant/expense-recap/approval/'.$roadwarrant->uuid) }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="expense-approval-note">Catatan {{ $canOperationalDecide ? 'Operational' : 'Accounting' }}</label>
              <textarea id="expense-approval-note" name="note" class="form-control" rows="2" maxlength="2000" required>{{ old('note') }}</textarea>
            </div>
            <button type="submit" name="decision" value="approved" class="btn btn-success" onclick="return confirm('Setujui rekap pengeluaran ini?')">Setujui</button>
            <button type="submit" name="decision" value="rejected" class="btn btn-danger" onclick="return confirm('Tolak rekap pengeluaran ini?')">Tolak</button>
          </form>
        @endif
      @endif
    </div>
  </div>
</div>
