<?php

namespace App\Entities;

use App\Entities\TraitRelations\BelongsToUserTrait;

/**
 * Used for Queue Entities
 * 
 * @author cmooy
 */
class Queue extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use BelongsToUserTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_queues';

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'user_id'						,
											'process_name'					,
											'process_option'				,
											'parameter'						,
											'total_process'					,
											'task_per_process'				,
											'process_number'				,
											'total_task'					,
											'message'						,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'process_name'					=> 'max:255',
											'process_option'				=> 'max:255',
											'total_process'					=> 'numeric',
											'task_per_process'				=> 'numeric',
											'process_number'				=> 'numeric',
											'total_task'					=> 'numeric',
										];


	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to check running queue
	 *
	 * @param none
	 */
	public function scopeRunning($query, $variable)
	{
		return $query->whereRaw('`process_number` < `total_process`');
	}

	/**
	 * scope to check complete queue
	 *
	 * @param none
	 */
	public function scopeComplete($query, $variable)
	{
		return $query->whereRaw('`process_number` = `total_process`');
	}

}
