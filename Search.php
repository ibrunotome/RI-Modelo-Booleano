<?php

	/**
	 * Class Search
	 *
	 * Responsável pelos métodos de busca
	 */
	class Search {
		private $maxDocuments;
		private $dictionaryWords;
		private $noResults;

		public function __construct($maxDocuments, $dictionaryWords) {
			$this->maxDocuments = $maxDocuments;
			$this->dictionaryWords = $dictionaryWords;
			$this->noResults = 'Pesquisa não retornou nenhum resultado';
		}

		/**
		 * Busca com operador lógico AND para 2 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchAND2Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se as 2 palavras existem no documento, Doc . $i = 1
				if ((isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && (isset($this->dictionaryWords[$search[2]]['Doc' . $i])))) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico OR para 2 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchOR2Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se pelo menos uma das palavras existem no documento, Doc . $i = 1
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				} else if (isset($this->dictionaryWords[$search[2]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico NOT para 2 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNOT2Words($search) {
			$result = NULL;
			# se palavra 1 existe no documento, e palavra 2 não
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se palavra 1 existe no documento $i, mas palavra 2 não, Doc . $i = 1
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && !isset($this->dictionaryWords[$search[2]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico NOT para 1 palavra
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNOT1Word($search) {
			$result = NULL;
			# se palavra 1 não existe no documento
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se palavra 1 existe no documento $i, mas palavra 2 não, Doc . $i = 1
				if (!isset($this->dictionaryWords[$search[1]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico AND para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchAND3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se pelo menos uma das palavras existem no documento, Doc . $i = 1
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && isset($this->dictionaryWords[$search[2]]['Doc' . $i]) && isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico OR para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchOR3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se pelo menos uma das palavras existem no documento, Doc . $i = 1
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i]) || isset($this->dictionaryWords[$search[2]]['Doc' . $i]) || isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico NOT para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNOT3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se existe palavra1, mas não existem palavra2 e nem palavra3
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && !isset($this->dictionaryWords[$search[2]]['Doc' . $i]) && !isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operador lógico NOT para 2 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNotWord2Times($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se não existe nenhuma das 2 palavras
				if ($search[0] == 'not' && $search[2] == 'not' && $search[1] != 'not' && $search[3] != 'not' && !isset($this->dictionaryWords[$search[1]]['Doc' . $i]) && !isset($this->dictionaryWords[$search[3]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos AND e OR para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchAndOr3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (palavra1 and palavra2) or palavra3
				if ((isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && isset($this->dictionaryWords[$search[2]]['Doc' . $i])) || isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos AND e NOT para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchAndNot3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (palavra1 and palavra2) not palavra3
				if (isset($this->dictionaryWords[$search[0]]['Doc' . $i]) && isset($this->dictionaryWords[$search[2]]['Doc' . $i]) && !isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos OR e AND para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchOrAnd3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (palavra1 and palavra2) or palavra3
				if ((isset($this->dictionaryWords[$search[0]]['Doc' . $i]) || isset($this->dictionaryWords[$search[2]]['Doc' . $i])) && isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos OR e NOT para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchOrNot3Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (palavra1 and palavra2) not palavra3
				if ((isset($this->dictionaryWords[$search[0]]['Doc' . $i]) || isset($this->dictionaryWords[$search[2]]['Doc' . $i])) && !isset($this->dictionaryWords[$search[4]]['Doc' . $i])) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos NOT e AND para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNotAnd2Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (not palavra1 and palavra2)
				if ((!isset($this->dictionaryWords[$search[1]]['Doc' . $i]) && isset($this->dictionaryWords[$search[3]]['Doc' . $i]))) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}

		/**
		 * Busca com operadores lógicos NOT e OR para 3 palavras
		 *
		 * @param array $search
		 *
		 * @return string
		 */
		public function searchNotOr2Words($search) {
			$result = NULL;
			for ($i = 1; $i <= $this->maxDocuments; $i++) {
				# se (not palavra1 and palavra2)
				if ((!isset($this->dictionaryWords[$search[1]]['Doc' . $i]) || isset($this->dictionaryWords[$search[3]]['Doc' . $i]))) {
					$result['Doc' . $i] = 1;
				}
			}
			return $result != NULL ? json_encode(array_keys($result)) : $this->noResults;
		}
	}