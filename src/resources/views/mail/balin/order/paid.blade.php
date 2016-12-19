@extends('mail.balin.layout')

@section('content')
	<table style="width:100%">
		<tr class="row">
			<td class="col-sm-2" style="width:20%">
				<img src="{{ $data['balin']['logo'] }}" style="max-width:200px; text-align:left;">
			</td>
			<td class="col-sm-10" valign="top" style="text-align:right;width:40%">
				<h3>Validasi Pembayaran</h3>
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
					<div style="padding: 15px;background-color: #ddd;width: 40px;margin: 0 auto;font-size: 30px">1</div>
					<p style="margin-bottom:0; text-transform:uppercase; color: #999">Checkout</p>
				</div>
			</td>
			<td style="width:3%;">&nbsp;</td>
			<td style="width:3%; text-align:center;">
				<div style="width:100px; margin: 0 auto; text-align:center">
					<div style="padding: 15px;background-color: #000; color: #fff;width: 40px;margin: 0 auto;font-size: 30px">2</div>
					<p style="margin-bottom:0; text-transform:uppercase;">Paid</p>
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
	<table class="row">
		<tr>
			<td class="wrapper last">
				<table class="twelve columns">
					<tr>
						<td>
							<br/>
							<?php
								$point 			= 0;
								if(isset($data['paid']['paidpointlogs']))
								{
									foreach ($data['paid']['paidpointlogs'] as $key => $value) 
									{
										$point 		= $point + $value['amount'];
									}
								}
							?>
							<p>Dear @if($data['paid']['user']['gender']=='male') Mr. @else Mrs. @endif <strong>{{$data['paid']['user']['name']}}, </strong></p>
							<p> 
								Pembayaran untuk pesanan <strong>#{{$data['paid']['ref_number']}}</strong> telah kami terima pada tanggal 
								@if(count($data['paid']['payment'])) 
									@thunder_mail_date_indo($data['paid']['payment']['ondate']) 
								@else 
									@thunder_mail_date_indo($data['paid']['updated_at']) 
								@endif
							</p>
							@if($data['paid']['payment'])
								<p>
									Atas nama {{$data['paid']['payment']['account_name']}} melalui rekening {{$data['paid']['payment']['destination']}}
								</p>
							@else
								<p>
									Menggunakan point BALIN sebesar @thunder_mail_money_indo(abs($point))
								</p>
							@endif
							<p>
								Pengiriman akan diproses selambat lambatnya 2 (dua) hari kerja setelah pembayaran di validasi.
							</p>
						</td>
						<td class="expander"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<h3>Rincian Pesanan</h3>
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
						@forelse($data['paid']['transactiondetails'] as $key => $value)
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
							<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR($data['paid']['shipping_cost'])</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">Diskon Voucher</td>
							<td style="text-align:right;">IDR</td>
							<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR(($data['paid']['voucher_discount'] ? $data['paid']['voucher_discount'] : 0))</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">Balin Point yang digunakan</td>
							<td style="text-align:right;">IDR</td>
							<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR( $data['paid']['point_discount'])</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">Potongan Transfer</td>
							<td style="text-align:right;">IDR</td>
							<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR($data['paid']['unique_number'])</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">
								Biaya Tambahan
								@foreach($data['paid']['transactionextensions'] as $key => $value)
									<br/> {{$value['productextension']['name']}}
								@endforeach
							</td>
							<td style="text-align:right;">IDR</td>
							<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR( $data['paid']['extend_cost'])</td>
						</tr>
						@if(count($data['paid']['payment']))
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">{{$data['paid']['payment']['method']}}</td>
							<td style="text-align:right;">IDR</td>
							<td style="text-align:right;padding:5px;color:red;">@thunder_mail_money_indo_without_IDR( $data['paid']['payment']['amount'])</td>
						</tr>
						@endif
						<tr>
							<td colspan="2">&nbsp;</td>
							<td colspan="2" style="text-align:left;">Total</td>
							<td style="text-align:right;">IDR</td>
							@if($data['paid']['bills'] < 0)
								<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR(0)</td>
							@else
								<td style="text-align:right;padding:5px;">@thunder_mail_money_indo_without_IDR($data['paid']['bills'])</td>
							@endif
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				Jika anda ada kesulitan saat memesan silahkan menghubungi layanan pelanggan kami.
				<p>Email : {{$data['balin']['email']}}</p>
			</td>
		</tr>	
	</table>
	<br/>
	<br/>
	<br/>
	<br/>
@stop
