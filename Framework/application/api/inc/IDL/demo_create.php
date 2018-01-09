<?php
array (
  'name' => 'create',
  'method' => 'POST',
  'request' => 
  array (
    'id' => 
    array (
      'name' => 'user_id',
      'type' => 'integer',
      'description' => '1231233',
      'validate' => 
      array (
        'Integer' => 
        array (
          'checkMin' => 
          array (
            0 => 0,
          ),
          'checkMax' => 
          array (
            0 => 4294967295,
          ),
        ),
      ),
    ),
    'name' => 
    array (
      'name' => 'user_name',
      'type' => 'string',
      'description' => '',
      'validate' => 
      array (
        'String' => 
        array (
          'checkMinLength' => 
          array (
            0 => 0,
          ),
          'checkMaxLength' => 
          array (
            0 => 255,
          ),
        ),
      ),
    ),
    'type' => 
    array (
      'name' => 'reader_type',
      'description' => '32123123',
      'validate' => 
      array (
        'Enum' => 
        array (
          'checkEnum' => 
          array (
            0 => 
            array (
              0 => 'ADSF',
              1 => 'ISOWER',
              2 => '665',
              3 => 'ISO465456WER',
              4 => 'AAAAA',
            ),
            1 => 'reader_type_enum',
          ),
        ),
      ),
    ),
  ),
  'response' => 
  array (
    'list' => 
    array (
      'name' => 'user_list_ttt',
      'type' => 'array',
      'description' => 'asdf',
      'validate' => 
      array (
        'Array' => 
        array (
          'checkMaxSize' => 
          array (
            0 => 100,
          ),
          'checkMinSize' => 
          array (
            0 => 1,
          ),
        ),
      ),
      'repeated' => 
      array (
        'name' => 'user_list_struct',
        'type' => 'struct',
        'description' => '11111111111',
        'element' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'type' => 'integer',
            'validate' => 
            array (
              'Integer' => 
              array (
                'checkMin' => 
                array (
                  0 => 0,
                ),
                'checkMax' => 
                array (
                  0 => 9223372036854775807,
                ),
              ),
            ),
          ),
          'user_id' => 
          array (
            'name' => 'user_id',
            'type' => 'integer',
            'description' => '1231233',
            'validate' => 
            array (
              'Integer' => 
              array (
                'checkMin' => 
                array (
                  0 => 0,
                ),
                'checkMax' => 
                array (
                  0 => 4294967295,
                ),
              ),
            ),
          ),
          'user_name' => 
          array (
            'name' => 'user_name',
            'type' => 'string',
            'description' => '',
            'validate' => 
            array (
              'String' => 
              array (
                'checkMinLength' => 
                array (
                  0 => 0,
                ),
                'checkMaxLength' => 
                array (
                  0 => 255,
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
)