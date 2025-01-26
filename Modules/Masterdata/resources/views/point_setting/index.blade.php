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
          <th>Type</th>
          <th>persentase</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td>{{ $value->type }}</td>
              <td>{{ $value->percentage ? $value->percentage : 0 }} %</td>
              <td>
                <div class="btn-group btn-block">
                  @if (permissionCheck('edit')) <a href="{{ url('masterdata/pointsetting/edit/'.$value->type_id) }}" class="btn btn-success btn-sm">Edit</a> @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
@endsection
