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

				<p>Selamat datang, Anda telah terdaftar di <a href="{{$data['balin']['url']}}">Balin.id</a></p>

				<p>Dengan menjadi bagian dari Balin.id, anda juga dapat menikmati fasilitas yang kami berikan berupa Kode Referal dan Balin Point. Dengan kode Referal, 
				anda dapat mengajak teman atau kerabat anda untuk mendaftar ke situs Balin.id dan berikan kode referal anda. Dengan menggunakan kode referal anda, 
				teman anda akan mendapatkan Balin Point sebesar IDR. 50.000 dan anda akan mendapatkan Balin Point sebesar IDR. 10.000. Kode referal anda, pada mulanya 
				hanya dapat anda berikan kepada 10 orang teman anda. Apabila teman yang menggunakan kode referal anda melakukan pembelian, anda akan mendapatkan 
				tambahan kuota tersebut menjadi 11 dan anda akan mendapatkan Balin Point sebesar IDR. 10.000, dan demikian seterusnya tanpa ada batasnya. Balin Point
				tersebut dapat anda gunakan untuk berbelanja di Balin.id, namun tidak dapat diuangkan.
				</p>

				<p>Semakin banyak teman yang menggunakan referal anda dan semakin sering teman yang anda referensikan melakukan pembelian, semakin besar voucher yang anda dapatkan.</p>

				<p>Untuk memulai, Balin telah menyediakan Balin Point untuk anda. Klaim Balin Point tersebut dengan klik tombol di bawah ini.</p>
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