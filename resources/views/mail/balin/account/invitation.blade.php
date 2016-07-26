@extends('mail.balin.layout')

@section('content')
	<table style="width:100%">
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<img src="{{ $message->embed($data['balin']['logo']) }}" style="max-width:200px; text-align:left;">
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></br></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>Dear Friend,</p>

				<p>
					Ada tawaran menarik dari BALIN.ID! Ayo ikut <a href="{{$data['balin']['action']}}"> <strong>daftar</strong></a> dan nikmati hadiah serta bonus sebanyak banyaknya.
				</p>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></td>
		</tr>

		<tr>
			<td></br></br></td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>
					Kind Regards, 
					</br>
					{{$data['user']['name']}}
				</p>
			</td>
			<td width="10%"></td>
		</tr>

	</table>
	</br>
	</br>
	</br>
@stop