<?php
$xpdo_meta_map['MlmSystemProfit']= array (
  'package' => 'mlmsystem',
  'version' => '1.1',
  'table' => 'mlmsystem_profits',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'event' => NULL,
    'name' => NULL,
    'class' => '',
    'description' => NULL,
    'profit' => 0,
    'add_profit' => 0,
    'order_profit' => '0',
    'initiator_profit' => '0',
    'tree_active' => 1,
    'tree_profit' => NULL,
    'rank' => 0,
    'active' => 1,
    'editable' => 1,
    'properties' => NULL,
  ),
  'fieldMeta' => 
  array (
    'event' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'class' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'profit' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'add_profit' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'order_profit' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '11',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'initiator_profit' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '11',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'tree_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
    ),
    'tree_profit' => 
    array (
      'dbtype' => 'varchar',
      'phptype' => 'string',
      'precision' => '500',
      'null' => true,
    ),
    'rank' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => true,
      'default' => 0,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
    ),
    'editable' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
    ),
    'properties' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'event' => 
    array (
      'alias' => 'event',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'event' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Groups' => 
    array (
      'class' => 'MlmSystemProfitGroup',
      'local' => 'id',
      'foreign' => 'identifier',
      'owner' => 'local',
      'cardinality' => 'many',
    ),
  ),
  'aggregates' => 
  array (
    'Event' => 
    array (
      'class' => 'modEvent',
      'local' => 'event',
      'foreign' => 'name',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
