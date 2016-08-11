@extends('mail.balin.layout')

@section('content')
	<table style="width:100%;">
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<img src="{{ $message->embed($data['balin']['logo']) }}" style="max-width:200px; text-align:left;">
			</td>
			<td width="10%"></td>
		</tr>
		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>Dear Bpk/Ibu <strong>{{$data['user']['name']}},</strong></p>

				<p>Selamat, Anda telah terdaftar di <a href="{{$data['balin']['url']}}">Balin.id</a></p>

				<br>
				<p>Kode Referral Anda: {{$data['user']['referral_code']}}</p>
				<p>Balin point Anda: IDR {{$data['user']['total_point']}}</p>
				<br>
				<p>Ajaklah teman untuk mendaftar di Balin.id dengan memasukkan kode referral Anda. Teman anda akan mendapatkan Balin Point sebesar IDR. 50.000 dan anda akan mendapatkan Balin Point sebesar IDR. 10.000.</p>
				<br>
				<p>Kode referal pada mulanya hanya dapat Anda berikan kepada 10 orang teman anda. Namun, apabila teman yang menggunakan kode referal anda melakukan pembelian, anda akan mendapatkan tambahan kuota tersebut menjadi 11 dan anda akan mendapatkan Balin Point sebesar IDR. 10.000, dan demikian seterusnya tanpa ada batasnya.</p>
				<br>
				<p>Balin Point dapat Anda gunakan untuk berbelanja di balin.id. Namun tidak dapat diuangkan. Semakin banyak teman yang menggunakan referal anda dan semakin sering teman yang anda referensikan melakukan pembelian, semakin besar poin yang anda dapatkan.</p>
				<br>
				<p>Klaim balin point Anda dengan klik tombol di bawah ini.</p>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td style="width:90%; text-align:center;">
				<a href="{{$data['balin']['action']}}" class='btn'>KLAIM BALIN POINT</a>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td><br></td>
		</tr>	

		<tr>
			<td width="10%"></td>
			<td width="80%">
				<p>
					Kind Regards, </br>
					Balin.id
				</p>
			</td>
			<td width="10%"></td>
		</tr>

	</table>
	</br>
	</br>
	</br>
@stop