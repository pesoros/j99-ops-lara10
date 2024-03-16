@extends('layouts.main', ['title' => $title ])
 
@section('content')

<div class="row mb-2">
    <div class="col-sm-12">
        <h2>Welcome: {{ auth()->user()->name }} | {{ auth()->user()->email }} | {{ Session('role_info_session')->title }}</h2>
    </div>
</div>
 
@endsection

@push('extra-scripts')
<script type="text/javascript">
    $(function () {
        const role = {!! json_encode($roleData) !!}
        console.log(role.title.toUpperCase().replace(' ','_'));
    });
</script>
@endpush
