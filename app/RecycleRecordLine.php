<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecycleRecordLine extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'record_id',
		'pallet',
		'category',
		'lgross',
		'ltare',
		'price',
		'total_price',
		'pgi'
	];
}
