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

  public static function getTotalVotes($item_id) {
    return ItemRating::where("item_id", $item_id)->count();
  }

  public static function get($item_id, $ip) {
    return ItemRating::where("item_id", $item_id)->where("ip_address", $ip)->orderBy('id', 'desc')->first();
  }

  public static function getVotesAvg($item_id) {
    $avg = ItemRating::where("item_id", $item_id)->avg('score');

    return round($avg, 2);
  }

}
