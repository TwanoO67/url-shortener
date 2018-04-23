<?php
	//template from https://codepen.io/nikhil/pen/GuAho
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	require_once("config.php");
	
	//variable intermediaire
	$lien = "";
	$message = "";
	$shortlink = "$_SERVER[REQUEST_URI]";
	
	//verification du contenu
	$file_links = file_get_contents($filename);
	$links = ($file_links)? json_decode($file_links,true) : [];
	if($links[$shortlink]){
		$lien = $links[$shortlink];
	}
	
	//mode d'ajout
	if($_POST['mode'] == 'add'){
		if( !$protected || $_POST['pwd'] === $password ){
			if( !isset( $links[$_POST['shortlink']]) && $_POST['shortlink'] !== '/' ){
				//ajout du lien
				$links[$_POST['shortlink']] = $_POST['link'];
				//sauvegarde
				file_put_contents($filename, json_encode($links, JSON_PRETTY_PRINT));
				$message = " Ok, votre lien a été créer <a href='".$domain.$_POST['shortlink']."'>".$domain.$_POST['shortlink']."</a> ";			}
			else{
				$message = " Erreur, Le lien existe déjà ! ";
			}
		}
		else{
			$message = " Mot de passe incorrect !";
		}
		
	}
	
	
	function getUniqueId(){
		require_once("uniqueID.php");

		// Create an instance of uniqueID
		$uniqueID = new uniqueID();
		
		// Generate an ID
		$id = $uniqueID->generate();
		//echo $id; // 2398161031202658563
		
		// Shorten an ID
		$shortId = $uniqueID->shorten($id);
		return $shortId;
	}
	
	
	
	
?>

<html lang="fr">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="icon" type="image/png" href="camera.png" />
		<title><?php echo $titre; ?></title>
		<?php if ( $lien !== "") { ?>
		<meta http-equiv="refresh" content="0; URL=<?php echo $lien; ?>">
		<?php } ?>
	</head>
	<body>
		<br/><br/>
		<h1> <?php echo $titre; ?> </h1>
		<?php if ( $message !== '' ){
			echo "<p>$message</p>";
		} elseif ( $lien !== "") { ?>
		<form class="form-wrapper cf">
			<p>Redirection vers <a href="<?php echo $lien; ?>"><?php echo $lien; ?></a> ...</p>
		</form>
		<?php } elseif ( $lien !== "/" ) { ?>
			
			
			<form class="form-wrapper cf" action="/" method="post">
				<p> 
					<?php 
					//cas particulier pour la home
					if( $shortlink === "/" ){ 
						echo "Voulez-vous créer un lien réduit ? <br/>";
						$shortlink = getUniqueId(); 
					}
					else{
						echo "Aucun lien existant pour ".$shortlink."<br/> ";
						echo "Voulez-vous en créer un ? <br/>";
					}
					?>
				</p>
				<?php if($protected){ ?>
				<input type="password" placeholder="Mot de passe" name="pwd" required><br/>
				<?php } ?>
				 
				<input type='text' name="shortlink" value='<?php echo $shortlink; ?>'></input><br/>
				<input type='hidden' name="mode" value='add'></input>
		  	    <input type="text" placeholder="Lien complet... http://..." name="link" required>
			    <button type="submit">Ajouter</button>
			</form>
		<?php } ?>
		
		<div class="byline">
			<p><a href="/"><?php echo $titre; ?></a> est un service de <a href="//www.weberantoine.fr">WeberAntoine</a> </p>
		</div>
		
	</body>
</html>