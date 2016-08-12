@extends('mail.balin.layout')

@section('content')
	<table style="width:100%">
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<img src="{{ $data['balin']['logo'] }}" style="max-width:200px; text-align:left;">
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td><br/><br/></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>Dear Friend,</p>

				<p>
					Ada tawaran menarik dari BALIN.ID! Ayo ikut <a href="{{$data['balin']['action']}}"> <strong>daftar</strong></a> dan nikmati hadiah serta bonus sebanyak banyaknya.
				</p>

				@if(count($data['product']))
				<h4>Koleksi Populer dari Balin</h4>
				<p>
					<table>
						<tr>
							@foreach($data['product'] as $key => $value)
								<td>
									<a href="{{$data['balin']['action'].'/'.$value['slug']}}">
										<img src="{{ $value['thumbnail'] }}" style="max-width:150px; text-align:left;">
									</a>
								</td>
							@endforeach
						</tr>
					</table>
				</p>
				<h4>ATAU</h4>
				<br/>
				<br/>
				<a href="{{$data['balin']['url']}}" class='btn'>KUNJUNGI WEB BALIN</a>
				@endif
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td><br/></td>
		</tr>

		<tr>
			<td><br/><br/></td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>
					Kind Regards, 
				</p>
				<p>
					{{$data['user']['name']}}
				</p>
			</td>
			<td width="10%"></td>
		</tr>

	</table>
	<br/>
	<br/>
	<br/>
@stop