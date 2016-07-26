@extends('mail.balin.layout')

@section('content')
	<table style="width:100%">
		<tr class="row">
			<td style="width:60%">
				<img src="{{ $message->embed($data['balin']['logo']) }}" style="max-width:200px; text-align:left;">
			</td>
			<td valign="top" style="text-align:right;width:40%">
				<h3>Pesanan Anda Sudah Tiba<br>Di Tujuan</h3>
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
					<div style="padding: 15px;background-color: #ddd; width: 40px;margin: 0 auto;font-size: 30px">2</div>
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
					<div style="padding: 15px;background-color: #000; color: #fff;width: 40px;margin: 0 auto;font-size: 30px">4</div>
					<p style="margin-bottom:0; text-transform:uppercase;">Delivered</p>
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
							<p>Dear Bpk/Ibu <strong>{{$data['delivered']['user']['name']}}, </strong></p>
							<p> 
								Menurut pantauan kami pesanan dengan nomor invoice <strong>#{{$data['delivered']['ref_number']}}</strong> dengan nomor resi <strong>{{$data['delivered']['shipment']['receipt_number']}}</strong> sudah sampai dialamat penerima dan <strong>{{$data['delivered']['transactionlogs'][count($data['delivered']['transactionlogs'])-1]['notes']}}.</strong>
							</p>
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
										<th colspan="4" class="text-center col-md-4" style="text-align:left;background-color:black;color:white;padding:10px;">Item</th>
										<th class="text-center col-md-1" style="text-align:center;background-color:black;color:white;padding:10px;">Qty</th>
									</tr>
								</thead>
								<tbody>
									<?php $amount = 0;?>
									@forelse($data['delivered']['transactiondetails'] as $key => $value)
										<?php $amount = $amount + (($value['price'] - $value['discount']) * $value['quantity']);?>
										<tr>
											<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;">{!!($key+1)!!}</td>
											<td colspan="4" style="text-align:left;background-color:#C6C6C6;padding:5px;"> {{$value['varian']['product']['name']}} {{$value['varian']['size']}}</td>
											<td class="text-center" style="text-align:center;background-color:#C6C6C6;padding:5px;"> {{$value['quantity']}} </td>
										</tr>
									@empty
										<tr>
											<td colspan="6"> Tidak ada data </td>
										</tr>
									@endforelse
									<tr>
										<td colspan="6">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<p>
								Terima kasih untuk kepercayaan anda. 
							</p>
						</td>
						<td class="expander"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>
	<br/>
	<br/>
@stop
