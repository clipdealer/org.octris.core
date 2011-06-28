% Parameter importing and validating using validation schemas
% Harald Lapp (<harald@octris.org>)
% July, 2011

Abstract
--------

The OCTRiS framework provides powerful functionality for validating parameters -- request parameters, server
and environment variables, command line options. The OCTRiS validation schemas support validation from simple values
to complex arrays.

Field validation rules
----------------------

_Example #1:_

Example shows required schema for validating login credentials:

    $schema = array(
        'default' => array(                        // entry point, always required!
            'type'          => validate::T_OBJECT,
            'properties'    => array(
                'username' => array(                // field: username
                    'type'  => validate::T_PRINTABLE
                ),
                'password' => array(                // field: password
                    'type'  => validate::T_PRINTABLE
                )
            )
        )
    );

The example abolve can be cleaned up a little bit, if only the default schema is used:

    $schema = array(
        'type'          => validate::T_OBJECT,
        'properties'    => array(
            'username' => array(                // field: username
                'type'  => validate::T_PRINTABLE
            ),
            'password' => array(                // field: password
                'type'  => validate::T_PRINTABLE
            )
        )
    );

_Example: #2:_

Example shows required schema for user-registration data containing a list of up to 5 website URLs:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'username' => array(
                    'type'  => validate::T_PRINTABLE
                ),
                'password' => array(
                    'type'  => validate::T_PRINTABLE,
                ),
                'password2' => array(               // a second password field to compare to
                    'type'      => validate::T_CALLBACK,
                    'callback'  => function($value) {
                        return ($value === $_POST->get('password')->value);
                    },
                    'required'  => true
                ),
                'websites' => array(
                    'type'      => validate::T_ARRAY,
                    'items'     => 'url',           // name of allowed sub-schema
                    'min_items' => 0,
                    'max_items' => 5
                )
            )
        ),
        
        'url' => array(                             // sub-schema: url
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'url'   => array(
                    'type' => validate::T_URL
                ),
                'title' => array(
                    'type' => validate::T_PRINTABLE
                )
            )
        )
    )

_Example: #3:_

Example shows required schema for an array with 10 string values:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_ARRAY,
            'min_items'  => 10,
            'max_items'  => 10,
            'items'      => 'printable'
        ),
        
        'printable' => array(
            'type'      => validate::T_PRINTABLE
        )

The example above could also be rewritten to:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_ARRAY,
            'min_items'  => 10,
            'max_items'  => 10,
            'items'      => array(
                'type'      => validate::T_PRINTABLE
            )
        )

_Example: #4:_

Example shows required schema for the feature keyrename. Consider a command line application receiving
the following command line parameters:

    ./test.php  --convert input-file output-file

The command line parameters are handled by the data provider and the following
insternal array structure is stored:

    array(
        '--convert' => true,
        0           => 'input-file',
        1           => 'output-file'
    )
    
Parameters without explicit parameter name would be assigned an ordered numeric key name. To make it possible
to validate and grant access to these parameters, a naming for these parameters can be specified using the 
keyrename feature. Keyrename is currently only allowed for the default scheme, but not for sub-schemes.

    $schema = array(
        'default' => array(
            'type'       => validate::T_OBJECT,
            'keyrename'  => array(
                'input', 'output'
            ),
            'properties' => array(
                '--convert' => array(
                    'type'  => validate::T_BOOL
                ),
                'input' => array(
                    'type'  => validate::T_PRINTABLE,
                ),
                'output' => array(
                    'type'  => validate::T_PRINTABLE,
                )
            )
        )
    )


Pre-processing values
---------------------

A callback function may be specified to pre-process values to validate:

    $schema = array(
        'default'   => array(
            'type'  => validate::T_OBJECt,
            '...'
        )
    )

Validation chaining
-------------------

Validation chaining is achieved by the 'chain' validator, which accepts
an array with multiple types/options to validate in chain as option parameter. 
The validation chain stops, if a validator fails or after all steps of the chain
succeeded.

In the example below validation chaining is used to validate usernames first if 
it contains only printable characters and additionally if it doesn't match invalid
names:

    $schema = array(
        'default' => array(
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'username' => array(
                    'type'      => validate::T_CHAIN,
                    'chain' => array(
                        array(
                            'type'      => validate::T_PRINTABLE
                        ),
                        array(
                            'type'      => validate::T_CALLBACK,
                            'callback'  => function($value) {
                                return !in_array($value, array('admin', 'chef'));
                            }
                        )
                    )
                )
            )
        )
    )
    
Event hooks
-----------

It's allowed to define event callbacks, that will be triggered during validation.
The following events are available:

*   onFailure -- triggered, when a value is missing or a value is invalid
*   onSuccess -- triggered, when a value exists and the value is valid

The registration example from above would change to:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'username' => array(
                    'type'      => validate::T_PRINTABLE,
                    'onFailure' => function() {
                        ...
                    }
                ),
                'password' => array(
                    'type'  => validate::T_PRINTABLE,
                ),
                'password2' => array(               // a second password field to compare to
                    'type'      => validate::T_CALLBACK,
                    'options'   => array(
                        'callback' => function($value) {
                            return ($value === $_POST['password']->value);
                        }
                    ),
                    'required'  => true
                    'onFailure' => function() {
                        ...
                    }
                ),
                'websites' => array(
                    'type'      => validate::T_ARRAY,
                    'items'     => 'url',           // name of allowed sub-schema
                    'min_items' => 0,
                    'max_items' => 5
                )
            )
        ),
    
        'url' => array(                             // sub-schema: url
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'url'   => array(
                    'type'      => validate::T_URL
                    'onFailure' => function() {
                        ...
                    }
                ),
                'title' => array(
                    'type' => validate::T_PRINTABLE
                )
            )
        )
    )

Page validation
---------------

While a direct called schema validation always executes the whole schema on the input array,
the page validation works a little different. Let's have a look at our registration example.

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'username' => array(
                    'type'  => validate::T_PRINTABLE
                ),
                'password' => array(
                    'type'  => validate::T_PRINTABLE,
                ),
                'password2' => array(               // a second password field to compare to
                    'type'      => validate::T_CALLBACK,
                    'options'   => array(
                        'callback' => function($value) {
                            return ($value === $_POST->get('password')->value);
                        }
                    ),
                    'required'  => true
                )
            )
        )
    )

The page validation requires this layout, because request parameters will always have to have
a named key (for example: 'username'). So the schema has to be:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_OBJECT,     // required(!)
            'properties' => array(                  // required(!)
                ...
            )
        )
    )

Or cleaned up as shown in the example #1 at the beginning of this document. However. The schema
validator will _not_ receive the whole schema including 'username', 'password', etc. but only 
a snippet for each field.

This is due to how the page validator handles request parameters and validation schemas. However.
The page validator accepts two speciel fields to a schema:

*   'required' -- while this one is for normal schemas just a boolean flag, in the page validator
    it takes a string, that will be collected, if the field is missing. If 'required' is available
    and contains a string, the field is indeed a required field.
    
*   'invalid' -- this is a new required field for the page validator. It takes a string, that will
    be collected, if the validation of the field fails.
    
The registration schema would than change to:

    $schema = array(
        'default' => array(                         // default schema
            'type'       => validate::T_OBJECT,
            'properties' => array(
                'username' => array(
                    'type'      => validate::T_PRINTABLE
                    'required'  => 'The username is a required field',
                    'invalid'   => 'The username contains illegal characters'
                ),
                'password' => array(
                    'type'      => validate::T_PRINTABLE,
                    'required'  => 'The password is a required field',
                    'invalid'   => 'The password contains illegal characters'
                ),
                'password2' => array(               // a second password field to compare to
                    'type'      => validate::T_CALLBACK,
                    'options'   => array(
                        'callback' => function($value) {
                            return ($value === $_POST->get('password')->value);
                        }
                    ),
                    'required'  => 'The password2 is a required field',
                    'invalid'   => 'The password2 and password don't match'
                )
            )
        )
    )

