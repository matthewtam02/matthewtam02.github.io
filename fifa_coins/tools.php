<?php require_once'core/init.php';?>
<!DOCTYPE html>
<html lang="en">
<?php include'inc/head.php';?>
<body>
	<div id="wrapper">
		<?php include'inc/header.php';?>
		<div id="content">
			<div class="centered">
				<h1 class="title mar">Handige <span style="font-weight:700;color:#FAAC58;">FIFA</span> tools om de beste spelers uit te zoeken!</h1>
				<br>
				<button class="input bigbutton hoverfx">Koop nu!</button>

				<div class="section white">
					<h1 class="title">Spelers top 10</h1>
					<form action="" method="POST">
						<h1 class="label">Sorteer op:</h1>
						<select name="s" onchange="submit()" class="input">
							<?php
								$skills = [
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

								foreach($skills as $key => $value){
									echo '<option value="'.$key.'">'.ucfirst($value).'</option>';
								}
							?>
						</select>
					</form>
					<table style="margin:0 auto;font-size:1.2em;width:500px;text-align:left;">
						<?php
							if(!empty($_POST['s']) && $_POST['s'] <= count($skills)){
								$skill = $skills[$_POST['s']];
							}
							else $skill = 'rating';

							$url = 'http://tools.fifaguide.com/api/topten/'.$skill;
							$json = file_get_contents($url);
							$result = json_decode($json, true);
							if(is_array($result)){
								echo '<thead> <tr> <th>Rank</th> <th>Name</th> <th>Height</th> </tr> </thead>';
								foreach($result as $key => $value){
									echo '<tr><td><b>';
									echo $key+1;
									echo '</b></td><td>';
									echo $value['first_name'].' '.$value['last_name'];
									echo '</td><td>';
									echo $value['height'].' cm';
									echo '</td></tr>';
								}
							}else{
								echo 'Sorry, nog niet beschikbaar';
							}
						?>
					</table>
				</div>
				<div class="section">
					<h1 class="title">Speler info</h1>
					<form action="" method="POST">
						<table style="margin:0 auto;text-align:left">
							<tr>
								<td class="label">Resource ID</td>
								<td><input type="text" name="p" class="input"></td>
								<td><a href="http://tools.fifaguide.com/database.php"><img src="img/icomoon/info2.svg" class="info"></a></td>
							</tr>
							<tr>
								<td class="label">Platform</td>
								<td>
									<select class="input" onclick="submit()" name="c">
										<option value="0">XBOX</option>
										<option value="1">PS</option>
									</select>
								</td>
								<td></td>
							</tr>
						</table>
					</form>
					<br>
					<br>
					<table style="margin: 0 auto">
						<?php
							if(!empty($_POST['p']) && !empty($_POST['c'])):
								if($_POST['c'] === 0)$platform = 'XBOX';
								elseif($_POST['c'] === 0)$platform = 'PS';
								else $platform = 'XBOX';
								$price = Fifa::getValue($_POST['p'], $platform)->price;
								$player = Fifa::getPlayer($_POST['p']);
								?>
						<tr>
							<td class="label">Naam:</td>
							<td><?php echo $player['first_name'].' '.$player['last_name'];?></td>
						</tr>
						<tr>
							<td class="label">Bijnaam:</td>
							<td><?php echo $player['common_name'];?></td>
						</tr>
						<tr>
							<td class="label">Geboortedatum:</td>
							<td><?php echo $player['dob'];?></td>
						</tr>
						<tr>
							<td class="label">Rating:</td>
							<td><?php echo '#'.$player['rating'];?></td>
						</tr>
						<tr>
							<td class="label">Lengte:</td>
							<td><?php echo $player['height'].' cm';?></td>
						</tr>
						<tr>
							<td class="label">Voet:</td>
							<td><?php echo Fifa::$footrans[$player['foot']];?></td>
						</tr>
						<?php
							endif;
						?>
					</table>
				</div>
				<div class="section white">
					<h1 class="title">Speler van de dag</h1>
					<p class="label">
						<?php
							$players = include'inc/players.php';
							$player = $players[strval(date('d'))];
							echo $player;
						?>
					</p>
				</div>
			</div>
		</div>
		<?php include'inc/footer.php';?>
	</div>
</body>
</html>