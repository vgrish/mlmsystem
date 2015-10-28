<?php

$xpdo_meta_map = array (
  'xPDOObject' => 
  array (
    0 => 'MlmSystemClient',
    1 => 'MlmSystemPath',
  ),
  'xPDOSimpleObject' => 
  array (
    0 => 'MlmSystemStatus',
    1 => 'MlmSystemEmail',
    2 => 'MlmSystemProfit',
    3 => 'MlmSystemProfitGroup',
    4 => 'MlmSystemLog',
    5 => 'MlmSystemTypeChanges',
    6 => 'MlmSystemModeChanges',
  ),
);

$this->map['modUser']['aggregates']['MlmSystemClient'] = array(
    'class' => 'MlmSystemClient',
    'local' => 'id',
    'foreign' => 'id',
    'cardinality' => 'one',
    'owner' => 'foreign',
);

