@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/currencies') }}">Currency</a> :
@endsection
@section("contentheader_description", $currency->$view_col)
@section("section", "Currencies")
@section("section_url", url(config('laraadmin.adminRoute') . '/currencies'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Currencies Edit : ".$currency->$view_col)

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
				{!! Form::model($currency, ['route' => [config('laraadmin.adminRoute') . '.currencies.update', $currency->id ], 'method'=>'PUT', 'id' => 'currency-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'name')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/currencies') }}">Cancel</a></button>
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
	$("#currency-edit-form").validate({
		
	});
});
</script>
@endpush
