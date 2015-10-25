<?php
$xpdo_meta_map['MlmSystemClient']= array (
  'package' => 'mlmsystem',
  'version' => '1.1',
  'table' => 'mlmsystem_clients',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'id' => NULL,
    'parent' => 0,
    'balance' => 0,
    'incoming' => 0,
    'outcoming' => 0,
    'leader' => 0,
    'status' => 1,
    'createdon' => NULL,
    'updatedon' => NULL,
    'properties' => NULL,
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
      'default' => 0,
      'index' => 'index',
    ),
    'balance' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'incoming' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'outcoming' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => false,
      'default' => 0,
    ),
    'leader' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'status' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'index' => 'index',
    ),
    'updatedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'index' => 'index',
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
    'id' => 
    array (
      'alias' => 'id',
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
      ),
    ),
    'parent' => 
    array (
      'alias' => 'parent',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'leader' => 
    array (
      'alias' => 'leader',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'leader' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'status' => 
    array (
      'alias' => 'status',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'status' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'createdon' => 
    array (
      'alias' => 'createdon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdon' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'updatedon' => 
    array (
      'alias' => 'updatedon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'updatedon' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'modUser',
      'local' => 'id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'UserProfile' => 
    array (
      'class' => 'modUserProfile',
      'local' => 'id',
      'foreign' => 'internalKey',
      'owner' => 'local',
      'cardinality' => 'one',
    ),
    'PaymentClient' => 
    array (
      'class' => 'PaymentSystemClient',
      'local' => 'id',
      'foreign' => 'user',
      'owner' => 'local',
      'cardinality' => 'one',
    ),
    'Status' => 
    array (
      'class' => 'MlmSystemStatus',
      'local' => 'status',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'MlmSystemClient',
        ),
      ),
    ),
    'Log' => 
    array (
      'class' => 'MlmSystemLog',
      'local' => 'id',
      'foreign' => 'identifier',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'local' => 
        array (
          'class' => 'MlmSystemClient',
        ),
      ),
    ),
    'ParentUser' => 
    array (
      'class' => 'modUser',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'ParentUserProfile' => 
    array (
      'class' => 'modUserProfile',
      'local' => 'parent',
      'foreign' => 'internalKey',
      'owner' => 'local',
      'cardinality' => 'one',
    ),
  ),
);
