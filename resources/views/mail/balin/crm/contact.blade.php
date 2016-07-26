@extends('mail.balin.layout')

@section('content')
	<table class="row">
		<tr>
			<td class="wrapper last">
				<table class="twelve columns">
					<tr>
						<td>
							<h3>Email dari customer</h3>
							<p> Nama : {{$data['customer']['name']}}</p>
							<p> Email : {{$data['customer']['email']}}</p>
							<p> Isi Pesan : </p>
							<p>
								{{$data['customer']['message']}}
							</p>
						</td>
						<td class="expander"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
@stop
