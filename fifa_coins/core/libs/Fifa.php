<?php
class Fifa{
	public static $skills = [
		'rating',
		'pace',
		'dribbling',
		'shooting',
		'defending',
		'heading',
		'passing',
		'height',
		'sales'
	];

	public static $footrans = [
		'Right' => 'Rechts',
		'Left' => 'Links'
	];

	/**
	 *	Gives the players in a specific club.
	 *
	 *	@param int $id The id of a club
	 *	@return array
	 */
	public static function getClub($id){
		$url = "http://tools.fifaguide.com/api/club/".$id;
		$json = file_get_contents($url);
		return json_decode($json, true);
	}

	/**
	 *	Gives the players in a specific club.
	 *
	 *	@param int $id The id of a club
	 *	@return bool|array
	 */
	public static function getTopTen($skill){
		if($id <= count($this->skills)-1 && $id >= 0){
			$url = "http://tools.fifaguide.com/api/topten/".$skill;
			$json = file_get_contents($url);
			return json_decode($json, true);
		}else{
			return false;
		}
	}

	/**
	 *	Gives the info of a specific player.
	 *
	 *	@param int $id The resource id of a player
	 *	@return array
	 */
	public static function getPlayer($id){
		$url = "http://tools.fifaguide.com/api/player/".$id;
		$json = file_get_contents($url);
		return json_decode($json, true);
	}

	/**
	 *	Returns the market value of a specific player.
	 *	@param int $id The resource id of a player
	 *	@param string $console The platform to get the price of.
	 *	Valid values are 'XBOX' and 'PS'
	 *	@return array
	 */
	public static function getValue($id, $console){
		$url = 'http://tools.fifaguide.com/api/marketvalue/'.$id.'/'.$console;
		$json = file_get_contents($url);
		return json_decode($json);
	}
}
?>