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
				<p>Dear Bpk/Ibu <strong>{{$data['point']['user']['name']}},</strong></p>

				<p>
					Anda Memiliki BALIN Point sebesar @thunder_mail_money_indo($data['point']['amount']) dari total point Anda yang akan expire tanggal {{date('d-m-Y H:i', strtotime($data['point']['expired_at']))}}.
					Ayo, gunakan point Anda sebelum expire!
				</p>

				<h4>Anda Mungkin Suka</h4>
				<p>
					<table>
						<tr>
							@foreach($data['product'] as $key => $value)
								@if($key%2==0 && $key!=0)
									</tr>
									<tr>
								@endif
									<td>
										<a href="{{$data['balin']['action'].'/'.$value['slug']}}">
											<img src="{{ $message->embed($value['thumbnail']) }}" style="max-width:150px; text-align:left;">
										</a>
									</td>
							@endforeach
						</tr>
					</table>
				</p>
				<h4>ATAU</h4>
			</td>
			<td width="10%"></td>
		</tr>

		<tr>
			<td></br></td>
		</tr>

		<tr>
			<td width="10%"></td>
			<td style="width:90%; text-align:center;">
				<a href="{{$data['balin']['url']}}" class='btn'>LIHAT PENAWARAN KAMI</a>
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