<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use willvincent\Rateable\Rateable;

class ItemRating extends Model
{
  use Rateable;

  protected $table = 'item_ratings';

  public $timestamps = false;

  public $fillable = ["item_id", "score", "added_on", "ip_address"];

}
