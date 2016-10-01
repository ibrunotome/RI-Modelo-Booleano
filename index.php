<?php
	session_start();
	set_time_limit(100);
	$maxDocuments = 60;
	require_once('Dictionary.php');
	require_once('Search.php');
	$dictionary = new Dictionary($maxDocuments);

	if (!isset($_SESSION['dictionary'])) {
		$dictionaryWords = $dictionary->buildDictionary();
		ksort($_SESSION['dictionary']);
		$dictionaryWords = $_SESSION['dictionary'];
		$search = new Search($maxDocuments, $dictionaryWords);
	} else {
		$dictionaryWords = $_SESSION['dictionary'];
		$search = new Search($maxDocuments, $dictionaryWords);
	}

	# Lista a tabela de frequências se for requisitado
	if (isset($_GET['listFrequency']) && $_GET['listFrequency'] == 'true') {
		$json = json_encode($dictionaryWords);
		echo '<a href="./"><< VOLTAR</a>';
		echo '<pre>Tabela de Frequências<br><br>';
		print_r(json_decode($json, TRUE));
		echo '</pre>';
		exit;
	}
	
	# Se receber um post do formulário, ativa a busca
	if (isset($_POST['search'])) {
		$searchTerm = strtolower($_POST['search']);
		$searchTerm = $dictionary->cleanString($searchTerm);
		$searchTerm = explode(' ', $searchTerm);
		if ((count($searchTerm) == 1) && isset($dictionaryWords[$searchTerm[0]])) {
			# realiza a busca normal com 1 palavra
			$result = $dictionaryWords[$searchTerm[0]];
			$result = json_encode(array_keys($result));
		} else if ((count($searchTerm) == 2) && in_array('not', $searchTerm)) {
			# checa operador lógico NOT com 1 palavra (ex: not palavra)
			$result = $search->searchNOT1Word($searchTerm);
		} else if ((count($searchTerm) == 3) && in_array('or', $searchTerm)) {
			# checa operador lógico OR com 2 palavras (ex: palavra1 or palavra2)
			$result = $search->searchOR2Words($searchTerm);
		} else if ((count($searchTerm) == 3) && in_array('and', $searchTerm)) {
			# checa operador lógico AND com 2 palavras (ex: palavra1 and palavra2)
			$result = $search->searchAND2Words($searchTerm);
		} else if ((count($searchTerm) == 3) && in_array('not', $searchTerm)) {
			# checa operador lógico NOT com 2 palavras (ex: palavra1 not palavra2)
			$result = $search->searchNOT2Words($searchTerm);
		} else if ((count($searchTerm) == 4) && in_array('not', $searchTerm) && !in_array('and', $searchTerm) && !in_array('or', $searchTerm)) {
			# checa operador lógico NOT com 2 palavras (ex: not palavra1 not palavra2)
			$result = $search->searchNotWord2Times($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('or', $searchTerm) && !in_array('and', $searchTerm) && !in_array('not', $searchTerm)) {
			# checa operador lógico OR com 3 palavras (ex: palavra1 or palavra2 or palavra3)
			$result = $search->searchOR3Words($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('and', $searchTerm) && !in_array('or', $searchTerm) && !in_array('not', $searchTerm)) {
			# checa operador lógico AND com 3 palavras (ex: palavra1 and palavra2 and palavra3)
			$result = $search->searchAND3Words($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('not', $searchTerm) && !in_array('and', $searchTerm) && !in_array('or', $searchTerm)) {
			# checa operador lógico NOT com 3 palavras (ex: palavra1 not palavra2 not palavra3)
			$result = $search->searchNOT3Words($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('and', $searchTerm) && in_array('or', $searchTerm) && !in_array('not', $searchTerm)) {
			# checa operadores lógicos AND e OR com 3 palavras (ex: palavra1 and palavra2 or palavra3)
			$result = $search->searchAndOr3Words($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('and', $searchTerm) && !in_array('or', $searchTerm) && in_array('not', $searchTerm)) {
			# checa operadores lógicos AND e NOT com 3 palavras (ex: palavra1 and palavra2 not palavra3)
			$result = $search->searchAndNot3Words($searchTerm);
		} else if ((count($searchTerm) == 5) && in_array('or', $searchTerm) && !in_array('and', $searchTerm) && in_array('not', $searchTerm)) {
			# checa operadores lógicos OR e NOT com 3 palavras (ex: palavra1 or palavra2 not palavra3)
			$result = $search->searchAndNot3Words($searchTerm);
		} else if ((count($searchTerm) == 4) && in_array('not', $searchTerm) && !in_array('or', $searchTerm) && in_array('and', $searchTerm)) {
			# checa operadores lógicos NOT e AND com 2 palavras (ex: not palavra1 and palavra2)
			$result = $search->searchNotAnd2Words($searchTerm);
		} else if ((count($searchTerm) == 4) && in_array('not', $searchTerm) && !in_array('and', $searchTerm) && in_array('or', $searchTerm)) {
			# checa operadores lógicos NOT e OR com 2 palavras (ex: not palavra1 or palavra2)
			$result = $search->searchNotOr2Words($searchTerm);
		} else {
			$result = 'Pesquisa não retornou nenhum resultado';
		}
	} else {
		$result = '';
	}
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Trabalho I - Modelo Booleano</title>
	</head>
	<body style="text-align: center">
		<form method="post">
			<input type="text" name="search" autocomplete="off" autofocus placeholder="Pesquisar...">
			<input type="submit" value="Buscar">
		</form>
		<a href="./index.php?listFrequency=true">Listar Frequência</a>
		<?php
			if (isset($_POST['search']) && $result != 'Pesquisa não retornou nenhum resultado') {
				echo '<h3>Resultados para a busca de "<i>' . $_POST['search'] . '</i>":</h3>';
				echo '<pre>';
				$arrayResults = json_decode($result, TRUE);
				foreach ($arrayResults as $key => $value) {
					echo $value . '<br>';
				}
				echo '</pre>';
			} else {
				echo '<h3>' . $result . '</h3>';
			}
		?>

		<pre>
			<br><b>Modos de pesquisa</b>
			<br>not palavra1
			<br>not palavra1 not palavra2
			<br>not palavra1 and palavra2
			<br>not palavra1 or palavra2
			<br>palavra1 or palavra2
			<br>palavra1 and palavra2
			<br>palavra1 not palavra2
			<br>palavra1 or palavra2 or palavra3
			<br>palavra1 and palavra2 and palavra3
			<br>palavra1 not palavra2 not palavra3
			<br>palavra1 and palavra2 or palavra3
			<br>palavra1 and palavra2 not palavra3
			<br>palavra1 or palavra2 and palavra3
			<br>palavra1 or palavra2 not palavra3
		</pre>
	</body>
</html>