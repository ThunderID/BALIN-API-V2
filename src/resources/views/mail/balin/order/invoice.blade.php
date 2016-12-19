@extends('mail.balin.layout')

@section('content')
<table style="width:100%">
	<tr class="row">
		<td class="col-sm-2" style="width:20%">
			<img src="{{ $data['balin']['logo'] }}" style="max-width:200px; text-align:left;">
		</td>
		<td class="col-sm-10" valign="top" style="text-align:right;width:40%">
			<h3>Invoice <strong>#{!!$data['invoice']['ref_number']!!}</strong></h3>
		</td>
	</tr>
</table>
<hr/>
<br/>
<table style="width:100%">
	<tr class="row">
		<td style="width:10%">&nbsp;</td>
		<td style="width:3%; text-align:center;">
			<div style="width:100px; margin: 0 auto; text-align:center;">
				<div style="padding: 15px;background-color: #000; color:#fff; width: 40px;margin: 0 auto;font-size: 30px">1</div>
				<p style="margin-bottom:0; text-transform:uppercase;">Checkout</p>
			</div>
		</td>
		<td style="width:3%;">&nbsp;</td>
		<td style="width:3%; text-align:center;">
			<div style="width:100px; margin: 0 auto; text-align:center">
				<div style="padding: 15px;background-color: #ddd;width: 40px;margin: 0 auto;font-size: 30px">2</div>
				<p style="margin-bottom:0; text-transform:uppercase; color: #999;">Paid</p>
			</div>
		</td>
		<td style="width:3%;">&nbsp;</td>
		<td style="width:3%; text-align:center;">
			<div style="width:100px; margin: 0 auto; text-align:center">
				<div style="padding: 15px;background-color: #ddd;width: 40px;margin: 0 auto;font-size: 30px">3</div>
				<p style="margin-bottom:0; text-transform:uppercase; color: #999;">Shipping</p>
			</div>
		</td>
		<td style="width:3%;">&nbsp;</td>
		<td style="width:3%; text-align:center;">
			<div style="width:100px; margin: 0 auto; text-align:center;">
				<div style="padding: 15px;background-color: #ddd;width: 40px;margin: 0 auto;font-size: 30px">4</div>
				<p style="margin-bottom:0; text-transform:uppercase; color: #999;">Delivered</p>
			</div>
		</td>
		<td class="col-sm-1" style="width:10%">&nbsp;</td>
	</tr>
</table>
<br><br>
<table style="width:100%;">
	<tr>
		<td class="col-sm-3">&nbsp;</td>
		<td class="col-sm-2" style="width:25%; 
									height:50px;
									border:2px solid;
									border-radius:0px; 
									background-color:white; 
									color:black;
									text-align:center;
									padding:15px;">
			Tanggal Invoice
			<br/>
			<br/>
			@thunder_mail_date_indo($data['invoice']['transact_at'])
		</td>
		<td class="col-sm-2" style="width:25%; 
									height:50px;
									border-radius:0px; 
									background-color:black; 
									color:white;
									text-align:center;
									padding:15px;">
			Jumlah Tagihan
			<br/>
			<br/>
			@if($data['invoice']['bills'] < 0)
				@thunder_mail_money_indo(0)
			@else
				@thunder_mail_money_indo($data['invoice']['bills'])
			@endif
		</td>
		<td class="col-sm-2" style="width:25%; 
									height:50px;
									border:2px solid;
									border-radius:0px; 
									background-color:white; 
									color:black;
									text-align:center;
									padding:15px;">
			Batas Waktu
			<br/>
			<br/>
			@thunder_mail_date_indo($data['invoice']['transact_at'].' '.str_replace('-', '+' , $data['balin']['expired_paid']))
		</td>
		<td class="col-sm-3">&nbsp;</td>
	</tr>
</table>
<br><br>
<table style="width:100%;">
	<tr>
		<td class="col-sm-12" style="width:100%; height:50px;text-align:left">
			<p>Dear @if($data['invoice']['user']['gender']=='male') Mr. @else Mrs. @endif <strong>{{$data['invoice']['user']['name']}},</strong> </p>
			<p>Terima kasih telah memesan. Pesanan Anda <span style="font-weight:bold">#{!!$data['invoice']['ref_number']!!}</span> menunggu pembayaran. Silakan melakukan pembayaran ke rekening bank yang berada dibawah sebelum <span style="font-weight:bold">@thunder_mail_date_indo($data['invoice']['transact_at'].' '.str_replace('-', '+' , $data['balin']['expired_paid']))</span> atau pesanan Anda akan dibatalkan. Berikut rincian tagihan pesanan Anda.</p>
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
			<th class="text-right col-md-2" style="text-align:right;background-color:black;color:white;padding:10px;">Harga @</th>
			<th class="text-right col-md-2" style="text-align:right;background-color:black;color:white;padding:10px;">Diskon</th>
			<th class="text-right col-md-2" style="text-align:right;background-color:black;color:white;padding:10px;">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php $amount = 0;?>
		@forelse($data['invoice']['transactiondetails'] as $key => $value)
			<?php $amount = $amount + (($value['price'] - $value['discount']) * $value['quantity']);?>
			<tr>
				<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;">{!!($key+1)!!}</td>
				<td style="text-align:left;background-color:#C6C6C6;padding:5px;"> {{$value['varian']['product']['name']}} {{$value['varian']['size']}}</td>
				<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;"> {{$value['quantity']}} </td>
				<td class="text-right" style="text-align:right;background-color:#C6C6C6;padding:5px;"> @thunder_mail_money_indo($value['price']) </td>
				<td class="text-right" style="text-align:right;background-color:#C6C6C6;padding:5px;"> @thunder_mail_money_indo($value['discount']) </td>
				<td class="text-right" style="text-align:right;background-color:#C6C6C6;padding:5px;"> @thunder_mail_money_indo((($value['price'] - $value['discount']) * $value['quantity'])) </td>
			</tr>
		@empty
			<tr>
				<td colspan="6"> Tidak ada data </td>
			</tr>
		@endforelse
		<tr>
			<td colspan="6">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Sub Total</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR($amount)</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Ongkos Kirim</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR($data['invoice']['shipping_cost'])</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Diskon Voucher</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR(($data['invoice']['voucher_discount'] ? $data['invoice']['voucher_discount'] : 0))</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Balin Point yang digunakan</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR( $data['invoice']['point_discount'])</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Potongan Transfer</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR($data['invoice']['unique_number'])</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">
				Biaya Tambahan
				@foreach($data['invoice']['transactionextensions'] as $key => $value)
					<br/> {{$value['productextension']['name']}}
				@endforeach
			</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR( $data['invoice']['extend_cost'])</td>
		</tr>
		@if(isset($data['invoice']['payment']) && count($data['invoice']['payment']))
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">{{$data['invoice']['payment']['method']}}</td>
			<td style="text-align:right;">IDR</td>
			<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR( $data['invoice']['payment']['amount'])</td>
		</tr>
		@endif
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" style="text-align:left;">Total Yang Harus Dibayarkan</td>
			<td style="text-align:right;">IDR</td>
			@if($data['invoice']['bills'] < 0)
				<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR(0)</td>
			@else
				<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR($data['invoice']['bills'])</td>
			@endif
		</tr>
	</tbody>
</table>
<br/>
<br/>
<table style="width:100%; background-color:#ddd; padding: 10px;"> 
	<tr class="col-sm-8">
		<td>
			<strong>Pembayaran dilakukan melalui </strong> {!!$data['balin']['bank_information']!!}
		</td>
	</tr>
</table>
<table style="width:100%; background-color:#ddd; padding: 10px;"> 
	<tr>
		<td style="width:30%;text-align:left;">
			<small>
			<i><strong>Syarat dan Ketentuan </strong> : harap membayar selambat lambatnya {{str_replace('-', '', str_replace('day', '', str_replace('s', '', $data['balin']['expired_paid'])))}} hari setelah pemesanan </i>
			</small>
		</td>
	</tr>
</table>
<table style="width:100%;">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan="2">
			Jika anda ada kesulitan saat memesan silahkan menghubungi layanan pelanggan kami.
		</td>
	</tr>
	<tr>
		<td>Email</td>
		<td>: {{$data['balin']['email']}}</td>
	</tr>
</table>
@stop
