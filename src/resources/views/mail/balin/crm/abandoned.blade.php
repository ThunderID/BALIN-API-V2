@extends('mail.balin.layout')

@section('content')
<table style="width:100%">
	<tr class="row">
		<td class="col-sm-2" style="width:20%">
			<img src="{{ $data['balin']['logo'] }}" style="max-width:200px; text-align:left;">
		</td>
		<td class="col-sm-10" valign="top" style="text-align:right;width:40%">
			<h3>Tas Belanja Anda</h3>
		</td>
	</tr>
</table>
<hr/>
<br/>

<table style="width:100%;">
	<tr>
		<td class="col-sm-12" style="width:100%; height:50px;text-align:left">
			<p>Dear @if($data['cart']['user']['gender']=='male') Mr. @else Mrs. @endif <strong>{{$data['cart']['user']['name']}},</strong> </p>
			
			<p>Terima kasih telah mengunjungi toko kami. Tas Belanja Anda tanggal <span style="font-weight:bold">@thunder_mail_date_indo($data['cart']['transact_at'])</span> menunggu untuk checkout. Silakan login ke laman <a href="{{$data['balin']['url']}}"> <strong>BALIN.ID</strong></a> untuk checkout keranjang belanja Anda.</p>
		</td>
	</tr>
</table>
<br/>
<table style="width:100%; font-size:11px;">
	<thead>
		<tr>
			<th class="col-md-1 text-center" style="text-align:center;background-color:black;color:white;padding:10px;">No</th>
			<!-- <th>Item#</th> -->
			<th class="text-center col-md-4" style="text-align:left;background-color:black;color:white;padding:10px;">Item</th>
			<th class="text-center col-md-1" style="text-align:center;background-color:black;color:white;padding:10px;">Qty</th>
			<th class="text-right col-md-3" style="text-align:right;background-color:black;color:white;padding:10px;">Harga @</th>
			<th class="text-right col-md-3" style="text-align:right;background-color:black;color:white;padding:10px;">Diskon</th>
		</tr>
	</thead>
	<tbody>
		<?php $amount = 0;?>
		@forelse($data['cart']['transactiondetails'] as $key => $value)
			<?php $amount = $amount + (($value['price'] - $value['discount']) * $value['quantity']);?>
			<tr>
				<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;">{!!($key+1)!!}</td>
				<td style="text-align:left;background-color:#C6C6C6;padding:5px;"> {{$value['varian']['product']['name']}} {{$value['varian']['size']}}</td>
				<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;"> {{$value['quantity']}} </td>
				<td class="text-right" style="text-align:right;background-color:#C6C6C6;padding:5px;"> @thunder_mail_money_indo($value['price']) </td>
				<td class="text-right" style="text-align:right;background-color:#C6C6C6;padding:5px;"> @thunder_mail_money_indo($value['discount']) </td>
			</tr>
		@empty
			<tr>
				<td colspan="5"> Tidak ada data </td>
			</tr>
		@endforelse
	</tbody>
</table>
<br/>
<br/>
<table style="width:100%;">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			Jika anda ada kesulitan saat checkout silahkan menghubungi layanan pelanggan kami.
		</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>: {{$data['balin']['email']}}</td>
	</tr>
</table>
@stop
