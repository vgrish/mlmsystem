<?php
$xpdo_meta_map['MlmSystemPath']= array (
  'package' => 'mlmsystem',
  'version' => '1.1',
  'table' => 'mlmsystem_paths',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'id' => NULL,
    'parent' => NULL,
    'level' => 0,
    'order' => 0,
  ),
  'fieldMeta' => 
  array (
    'id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => true,
      'index' => 'pk',
    ),
    'level' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'string',
      'null' => true,
      'default' => 0,
      'index' => 'pk',
    ),
    'order' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'string',
      'null' => true,
      'default' => 0,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'unique_key' => 
    array (
      'alias' => 'unique_key',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'level' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'order' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
