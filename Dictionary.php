<?php

	/**
	 * Class Dictionary
	 *
	 * Cria lê e armazena as palavras num dicionário
	 */
	class Dictionary {
		# Caracters que serão ignorados
		private $removeChars = array('(', ')', '»', '!', '?', ' es ', ' não ', ' és ', ' tu ', '.', ',', ' - ', '—', ' a ', ' e ', ' i ', ' o ', ' u ', ' de ', ' em ', ' por ', ' os ', ' na ', ' no ', ' não ', ' do ', ' dos ', ' nos ', ' pelo ', ' pelos ', ' à ', ' às ', ' as ', ' aí ', ' é ', ' eles ', ' são ', ' para ', ' da ', ' das ', ' na ', ' nas ', ' pela ', ' pelas ', ' um ', ' uns ', ' dum ', ' duns ', ' num ', ' nuns ', ' uma ', ' umas ', ' duma ', ' dumas ', ' numa ', ' numas ', ' que ', ' eu ', ' você ', ' nós ', ' nos ', ' com ', ' se ', ' já ', ' há ', ' foi ', ' me ', ' meu ', ' seu ', ' nosso ', ' tinha ', ' minha ', ' mais ', ' era ', ' mas ', ' sua ', ' se ', ' mim ', ' ser ', ' ou ', '"');
		private $maxDocuments;

		public function __construct($maxDocuments) {
			$this->maxDocuments = $maxDocuments;
		}

		/**
		 * Constrói dicionário de palavras
		 *
		 * @return void;
		 */
		public function buildDictionary() {
			$dictionary = array();
			$allWords = $this->readTXT();
			foreach ($allWords as $word) {
				for ($i = 1; $i <= $this->maxDocuments; $i++) {
					$doc = 'Doc' . $i;
					$allWordsOfThisDoc = array();
					$file = fopen('textos/file' . $i . '.txt', 'r');
					while (!feof($file)) {
						$linha = strtolower(fgets($file, 4096));
						$linha = $this->cleanString($linha);
						$textAux = explode(' ', $linha);

						# concatena as palavras das linhas num array
						foreach ($textAux as $value) {
							if (strlen($value) > 2) { # descarta artigos ou letras que sobraram com 2 ou 1 letra
								$allWordsOfThisDoc[] = $value;
							}
						}
					}
					$frequencyOfWord = array_count_values($allWordsOfThisDoc);
					if (in_array($word, $allWordsOfThisDoc)) {
						$dictionary[$word][$doc] = $frequencyOfWord[$word];
					}
				}
			}
			$_SESSION['dictionary'] = $dictionary;
		}

		/**
		 * Lê vários txts no formato file . $i . txt
		 *
		 * @return array $allWords
		 */
		private function readTXT() {
			$allWords = array();
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				$file = fopen('textos/file' . $i . '.txt', 'r');
				while (!feof($file)) {
					$linha = strtolower(fgets($file, 4096));
					$linha = str_replace($this->removeChars, ' ', $linha);
					$linha = $this->cleanString($linha);
					$textAux = explode(' ', $linha);
					
					# concatena as palavras das linhas num array
					foreach ($textAux as $value) {
						if (strlen($value) > 2) { # descarta artigos ou letras que sobraram com 2 ou 1 letra
							$allWords[] = $value;
						}
					}
				}
			}
			$allWords = array_unique($allWords);
			return $allWords;
		}

		/**
		 * Limpa a string de caracteres sujos (acentos, hifens, etc)
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public function cleanString($string) {
			$string = str_replace(array('[\', \']'), '', $string);
			$string = preg_replace('/[0-9]+/', '', $string);
			$string = preg_replace('/\[.*\]/U', '', $string);
			$string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
			$string = htmlentities($string, ENT_COMPAT, 'utf-8');
			$string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
			return trim($string);
		}
	}