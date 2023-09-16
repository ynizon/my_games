@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default globalpanel">
                <div class="panel-heading">{{ __("messages.Teams")}}</div>

                <div class="panel-body">
					<form id="formgame" method="get" action="/brainstorm" style="margin:auto;text-align:center;max-width:300px;">
						<select class="form-control" name="nbteams">
							@for ($i=2;$i<=4;$i++)
								<option value="{{ $i}}">{{ $i}}</option>
							@endfor
						</select>
						<br/>
						<span style="font-size: 21px;">{{ __("messages.Cards to find")}}</span>
						<br/>
						<select class="form-control" name="nbcards">
							@for ($i=2;$i<=6;$i++)
								<option @if ($i==3) "selected" @endif value="{{ $i}}">{{ $i}}</option>
							@endfor
						</select>
                        <br/>
                        <div id="arrondiplay">
                            <div class="btn">
                                <i class="fa fa-play"  ></i>
                            </div>
                        </div>
					</form>
                </div>
            </div>

			<div id="footer">
				<a href='/'>{{ __("messages.back_to_homepage")}}</a>
			</div>
        </div>
    </div>
</div>
@endsection
