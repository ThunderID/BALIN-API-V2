<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Queue;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

class BroadcastDiscountCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'broadcast:discount';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description 	= 'Running broadcast discount for products.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$id 			= $this->argument()['queueid'];

		$result 		= $this->broadcastdiscount($id);

		return $result;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['queueid', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('queuefunc', null, InputOption::VALUE_OPTIONAL, 'Queue Function', null),
		);
	}

	/**
	 * 1. check product
	 * 2. Price Changed
	 * 2.*. Art of discount1 : if there were discount amount, prices will be reduced by certain amount
	 * 2.*. Art of discount2 : if there were discount amount, prices will be reduced by certain amount
	 * 2.*. Art of discount3 : if there were discount percentage, prices will be reduced by certain percentage
	 * 
	 * @return true
	 * @author 
	 **/
	public function broadcastdiscount($id)
	{
		$queue 					= new Queue;
		$pending 				= $queue->find($id);

		$parameters 			= json_decode($pending->parameter, true);
		$messages 				= json_decode($pending->message, true);

		//1. Check product
		$products 				= new \App\Models\Product;
		$products 				= $products->sellable(true);

		//1a. Only for certain category
		if(isset($parameters['category_ids']))
		{
			$products 			= $products->categoriesid($parameters['category_ids']);
		}
		//1b. Only for certain tag
		elseif(isset($parameters['tag_ids']))
		{
			$products 			= $products->tagsid($parameters['tag_ids']);
		}

		$products 				= $products->get();

		//2. Price Changed
		foreach ($products as $idx => $product) 
		{
			$errors				= new MessageBag;

			//2a. Check price on that end period
			//if there were setup price right after end date, do nothing
			//if there were setup price after end date, but not precisely duplicated latest and expand right after
			//if there were no setup price right after end date, duplicate latest price with right after end date
			$price				= \App\Models\Price::productid($product['id'])->where('started_at', '>', date('Y-m-d H:i:s', strtotime($parameters['ended_at'])))->orderby('started_at', 'desc')->first();
			
			$promo 				= 0;

			if($price)
			{
			 	if(date('Y-m-d H:i:s', strtotime($parameters['ended_at'].' + 1 second')) != $price['started_at']->format('Y-m-d H:i:s'))
			 	{
			 		$prev 		= $price->toArray();
			 		$price 		= new \App\Models\Price;

			 		$price->fill($prev);

			 		$price->started_at 	= date('Y-m-d H:i:s', strtotime($parameters['ended_at'].' + 1 second'));

				 	if(!$price->save())
				 	{
				 		$errors->add('Price', $price->getError());
				 	}
			 	}
			}
			else
			{
				$price			= \App\Models\Price::productid($product['id'])->ondate($parameters['ended_at'])->orderby('started_at', 'desc')->first();

				if($price)
				{
			 		$prev 		= $price->toArray();
			 		$price 		= new \App\Models\Price;

			 		$price->fill($prev);

			 		$price->started_at 	= date('Y-m-d H:i:s', strtotime($parameters['ended_at'].' + 1 second'));

				 	if(!$price->save())
				 	{
				 		$errors->add('Price', $price->getError());
				 	}
				}
			}

			//2b. Check price on that start day
			//if there were setup price right after start date, create new one
			//if there were setup price exactly in time, update promo price
			$price				= \App\Models\Price::productid($product['id'])->ondate($parameters['started_at'])->orderby('started_at', 'desc')->first();
			
			$promo 				= 0;

			if($price)
			{
				if(isset($parameters['discount_amount']) && isset($parameters['discount_percentage']) && $parameters['discount_percentage'] != 0)
				{
					$promo 		= $price['price'] - $parameters['discount_amount'] - (($price['price'] - $parameters['discount_amount']) * $parameters['discount_percentage']/100);
				}
				elseif(isset($parameters['discount_amount']))
				{
					$promo 		= $price['price'] - $parameters['discount_amount'];
				}
				elseif(isset($parameters['discount_percentage']) && $parameters['discount_percentage'] != 0)
				{
					$promo 		= $price['price'] - ($price['price'] * $parameters['discount_percentage']/100);
				}

			 	if($parameters['started_at'] == $price['started_at']->format('Y-m-d H:i:s'))
			 	{
			 		$price->promo_price 	= "$promo";
			 	}
			 	else
			 	{
			 		$prev 		= $price->toArray();

			 		$price 		= new \App\Models\Price;

			 		$price->fill($prev);

			 		$price->started_at 		= $parameters['started_at'];
			 		$price->promo_price 	= "$promo";
			 	}

			 	if(!$price->save())
			 	{
			 		$errors->add('Price', $price->getError());
			 	}
			}

			//2c. Check price on that period
			//if there were setup price during period, update promo price
			$prices				= \App\Models\Price::productid($product['id'])->ondate([date('Y-m-d H:i:s', strtotime($parameters['started_at'].' + 1 second')), $parameters['ended_at']])->orderby('started_at', 'desc')->get();

			foreach ($prices as $key => $value) 
			{
				$price			= \App\Models\Price::id($value['id'])->first();
				
				if($price)
				{
					if(isset($parameters['discount_amount']) && isset($parameters['discount_percentage']) && $parameters['discount_percentage'] != 0)
					{
						$promo 		= $price['price'] - $parameters['discount_amount'] - (($price['price'] - $parameters['discount_amount']) * $parameters['discount_percentage']/100);
					}
					elseif(isset($parameters['discount_amount']))
					{
						$promo 		= $price['price'] - $parameters['discount_amount'];
					}
					elseif(isset($parameters['discount_percentage']) && $parameters['discount_percentage'] != 0)
					{
						$promo 		= $price['price'] - ($price['price'] * $parameters['discount_percentage']/100);
					}

			 		$price->promo_price 	= "$promo";
				
				 	if(!$price->save())
				 	{
				 		$errors->add('Price', $price->getError());
				 	}
				}

			}
				
			if(!$errors->count())
			{
				$pnumber 						= $pending->process_number+1;
				$messages['message'][$pnumber] 	= 'Sukses Menyimpan Perubahan Harga '.(isset($product['name']) ? $product['name'] : '');
				$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
			}
			else
			{
				$pnumber 						= $pending->process_number+1;
				$messages['message'][$pnumber] 	= 'Gagal Menyimpan Perubahan Harga '.(isset($product['name']) ? $product['name'] : '');
				$messages['errors'][$pnumber] 	= $errors;

				$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
			}

			$pending->save();
		}

		return true;
	}
}
