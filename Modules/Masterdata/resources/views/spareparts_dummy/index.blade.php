@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">List {{ $title }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="datatable-def-notitle" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>Nama Barang</th>
          <th>Kode</th>
          <th>Unit</th>
          <th>Kuantitas</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->name }}</td>
              <td>{{ $value->code }}</td>
              <td>{{ $value->unit }}</td>
              <td>{{ $value->qty }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
@endsection