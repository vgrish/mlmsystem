<?php

class modMlmSystemObjectFieldGetListProcessor extends modObjectProcessor
{
	public $languageTopics = array('mlmsystem');

	/** {@inheritDoc} */
	public function process()
	{
		$this->classKey = $this->getProperty('class');
		if (empty($this->classKey)) {
			return $this->modx->lexicon('mlmsystem_err_class_ns');
		}

		$array = array();
		$fields = $this->modx->getFields($this->classKey);
		foreach ($fields as $field => $value) {
			$array[] = array(
				'name' => $field,
				'value' => $field,
				'id' => count($array) + 1
			);
		}

		$query = $this->getProperty('query');
		if (!empty($query)) {
			foreach($array as $k => $format) {
				if (stripos($format['name'], $query) === FALSE ) {
					unset($array[$k]);
				}
			}
			sort($array);
		}

		return $this->outputArray($array);
	}

}

return 'modMlmSystemObjectFieldGetListProcessor';