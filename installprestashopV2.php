<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Téléchargeur de Versions PrestaShop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 600px;
            margin: 0 auto;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
		.footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
        }
		.php-info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 10px;
        }
    </style>
	<script>
        function openImagePopup() {
            var popupWindow = window.open("", "ImagePopup","width=640,height=420");
            popupWindow.document.write('<img src="https://i.imgur.com/a8kLbGg.jpg" alt="Compatibility Chart"/>');
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>PrestaShop Download | Unzip | Install</h2>
		<p>Ce script a été créé pour aider la communauté des développeurs PrestaShop. Il facilite le téléchargement et l'installation des différentes versions de PrestaShop directement depuis le dépôt GitHub.</p>
        <?php
		function getPrestaShopVersions() {
			$url = "https://api.github.com/repos/PrestaShop/PrestaShop/tags";
			$opts = [
				"http" => [
					"method" => "GET",
					"header" => "User-Agent: request"
				]
			];

			$context = stream_context_create($opts);
			$json = file_get_contents($url, false, $context);
			$tags = json_decode($json, true);

			$versions = [];
			foreach ($tags as $tag) {
				$versions[] = $tag['name'];
			}

			return $versions;
		}

		function downloadPrestaShop($version) {
			$url = "https://github.com/PrestaShop/PrestaShop/releases/download/{$version}/prestashop_{$version}.zip";
			$zipFile = "prestashop_{$version}.zip";

			file_put_contents($zipFile, fopen($url, 'r'));

			return $zipFile;
		}

		function unzipPrestaShop($zipFile) {
			$zip = new ZipArchive;
			if ($zip->open($zipFile) === TRUE) {
				$zip->extractTo('.');
				$zip->close();
				echo "Décompression de {$zipFile} réussie.<br>";
				echo "<form method='post'>";
				echo "<input type='hidden' name='redirect' value='index.php'>";
				echo "Voulez-vous démarrer l'installation de PrestaShop maintenant ? <input type='submit' value='OUI'>";
				echo "</form>";
			} else {
				echo "Échec de la décompression de {$zipFile}<br>";
			}
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (!empty($_POST['version']) && empty($_POST['unzip']) && empty($_POST['redirect'])) {
				$zipFile = downloadPrestaShop($_POST['version']);
				echo "<div>Téléchargement de {$zipFile} terminé.</div>";
				echo "<div>Voulez-vous décompresser le fichier téléchargé ?</div>";
				echo "<form method='post'>";
				echo "<input type='hidden' name='version' value='{$_POST['version']}'>";
				echo "<input type='hidden' name='unzip' value='{$zipFile}'>";
				echo "<input type='submit' value='OUI'>";
				echo "</form>";
			} elseif (!empty($_POST['unzip']) && empty($_POST['redirect'])) {
				unzipPrestaShop($_POST['unzip']);
			} elseif (!empty($_POST['redirect'])) {
				header('Location: ' . $_POST['redirect']);
				exit;
			}
		} else {
			$versions = getPrestaShopVersions();
			echo "<form method='post'>";
			echo "<select name='version'>";
			foreach ($versions as $version) {
				echo "<option value='{$version}'>{$version}</option>";
			}
			echo "</select>";
			echo "<input type='submit' value='Télécharger'>";
			echo "</form>";
			 
		}
		?>
		<p><center>Votre hebergement tourne actuellement en PHP: <b><?php echo phpversion(); ?></b></center></p>
        <p><b><center>ATTENTION</b>:<br> La version de votre PHP n'est peut-être pas compatible avec la version Prestashop que vous allez installer.<br><br><a href="javascript:void(0);" onclick="openImagePopup()">Vérifiez ici le tableau de compatibilité.</a></center></p>
    </div>
		<div class="footer">
            <p>&copy; 2023 PROGERANCE.COM. - Ce script est sous licence - Academic Free License AFL 3.0</p>
            <p>Soutenez notre travail : <a href="https://www.buymeacoffee.com/progerance">Offrez-moi un café</a></p>
        </div>	
    </div>
</body>
</html>
