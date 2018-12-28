@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/histories') }}">History</a> :
@endsection
@section("contentheader_description", $history->$view_col)
@section("section", "Histories")
@section("section_url", url(config('laraadmin.adminRoute') . '/histories'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Histories Edit : ".$history->$view_col)

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="box">
	<div class="box-header">
		
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($history, ['route' => [config('laraadmin.adminRoute') . '.histories.update', $history->id ], 'method'=>'PUT', 'id' => 'history-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'Currency')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/histories') }}">Cancel</a></button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
	$("#history-edit-form").validate({
		
	});
});
</script>
@endpush
