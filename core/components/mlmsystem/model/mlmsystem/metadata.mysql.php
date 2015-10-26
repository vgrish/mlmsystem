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
    2 => 'MlmSystemLog',
    3 => 'MlmSystemProfit',
    4 => 'MlmSystemProfitGroup',
  ),
);

$this->map['modUser']['aggregates']['MlmSystemClient'] = array(
    'class' => 'MlmSystemClient',
    'local' => 'id',
    'foreign' => 'id',
    'cardinality' => 'one',
    'owner' => 'foreign',
);
