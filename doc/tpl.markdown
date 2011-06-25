% Template Engine
% Harald Lapp (<harald@octris.org>)
% July, 2011

Overview
========

The octris framework provides an own template engine, which works independet of the structure of a template. It does
not matter, if the template is a simple text file or in HTML or XML format. The template engine converts template
markup to cacheable PHP code. It's not intended to compile the templates at runtime, but at deploy-time. So the
template compiler should only run ones, and a request should use the cached PHP converted templates for
displaying content.

Besides converting template syntax to PHP, the template engine performs the following tasks:

*   pre-localization of templates
*   compiling of "localization inline functions"
*   optimizing HTTP requests for including external Javascript and CSS files
*   compressing external Javascript and CSS files (YUICompressor required)

Template element syntax
-----------------------

Elements to be parsed by the template engine, have to be enclosed inside of {{...}}.

Template variables
------------------

Template variables are written in the form the following example, where _varname_ is a fully
qualified name of an existing template variable:

    {{$varname}}
    {{$varname:first}}
    {{$varname:0:second}}
    ...

The examples above shows the access to values of simple variables and multidimensional arrays:

    $varname
    $varname["first"]
    $varname["0"]["second"]

_Example:_

    <html>
        <head>
            <title>{{$title}}</title>
        </head>

        <body>
            <p>
                <b>Hello {{$name:name}} {{$name:surname}}</b>
            </p>
            
            <p>
                Please <a href="{{$url}}">click</a> here!
            </p>
        </body>
    </html>

Constants
---------

Template constants are written in the form the following example, where _CONSTANT_ is a fully
qualified name of an existing template constant:

    {{%CONSTANT}}

The template engine defines the following constants:

Strings
-------

Strings in templates are either enclosed by ' or " and are only allowed as parameters
for macros and methods.

    <!-- allowed -->
    {{write("testtext")}}
    {{write('testtext')}}
    
    <!-- forbidden -->
    {{"testtext"}}
    {{'testtext'}}

Block commands
--------------

A block encloses a part of a template and executes different actions of the enclosed template part:

    {{#block( parameter )}}
    ...
    {{#end}}

The template engine supports the following block commands:



Pre-localization of templates
=============================



Template language reference
===========================

Block commands
--------------

### #copy

This command copies a part of the template into an internal buffer. The command may be used to prevent duplicate
re-building of complex template parts. A HTML page navigation which should be displayed at the top and at the
bottom of the page, can be build the first time, copied and inserted as complete build snippet instead of 
re-building it at the bottom of the page. The copied template part may be accessed like a normal template variable.

_Copy block:_

    {{#copy($buffer)}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $buffer       | mixed     | x         | buffer variable to store copied content in    |
+---------------+-----------+-----------+-----------------------------------------------+

_Paste block:_

    {{$name}}

### #cron

Publish a template part based on time.

    {{#cron($start [, $end])}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $start        | mixed     | x         | start time as date/time string or unix        |
|               |           |           | timestamp                                     |
+---------------+-----------+-----------+-----------------------------------------------+
| $end          | mixed     | -         | optional end time as date/time string or unix |
|               |           |           | timestamp                                     |
+---------------+-----------+-----------+-----------------------------------------------+

The _$end_ parameter is optional. If it's not specified, the block will never be hidden again. 
Note, that this is just triggered on server side. The block will only be displayed/hidden on a 
new request.

### #cut

The _#cut_ block command is similar to _#copy_, expect, that it cut's the rendered block into the buffer. The command
is useful to post-process rendered template parts. for example: a built stylesheet may be cut, compressed by a css-
compressor and than output in the template.

_Cut block:_

    {{#cut($buffer)}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $buffer       | mixed     | x         | buffer variable to store cut content in       |
+---------------+-----------+-----------+-----------------------------------------------+

_Process (and paste) block (example):_

    {{compactCSS($name)}}


### #foreach

This block command implements an array iterator. The enclosed block will be 
repeated for each element of the array specified as parameter. For each 
iteration a template variable of the specified name will be filled with the 
contents of the array item of the iteration.

    {{#foreach($item, $array [, $meta])}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $item         | mixed     | x         | variable to store array item in               |
+---------------+-----------+-----------+-----------------------------------------------+
| $array        | array     | x         | array to iterate                              |
+---------------+-----------+-----------+-----------------------------------------------+
| $meta         | array     | -         | optional variable for metainformation         |
+---------------+-----------+-----------+-----------------------------------------------+

_Meta information:_

_#foreach_ adds the following keys/values to the optional _$meta_ variable:

+---------------+-----------------------------------------------------------+
| Name          | Description                                               |
+===============+===========================================================+
| is_first      | is true, if it's the first iteration step                 |
+----------------+----------------------------------------------------------+
| is_last       | is true, if it's the last iteration step                  |
+---------------+-----------------------------------------------------------+
| key           | contents of key of iterartion step                        |
+---------------+-----------------------------------------------------------+
| pos           | number of iteration step starting with 0                  |
+---------------+-----------------------------------------------------------+
| count         | total number of items in array                            |
+---------------+-----------------------------------------------------------+

To access the properties in a template, just specify a third parameter, a variable
for the _#foreach_ block:

    {{#foreach($item, $array, $meta)}}
        {{#if($meta:is_first)}}
            ...
        {{#end}}
    {{#end}}

### #loop

Loop over the enclosed template block.

    {{#loop($step, $from, $to [, $meta])}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $step         | int       | x         | current step of the loop                      |
+---------------+-----------+-----------+-----------------------------------------------+
| $from         | int       | x         | starting point of loop                        |
+---------------+-----------+-----------+-----------------------------------------------+
| $to           | int       | x         | ending point of loop                          |
+---------------+-----------+-----------+-----------------------------------------------+
| $meta         | array     | -         | optional variable for metainformation         |
+---------------+-----------+-----------+-----------------------------------------------+

It's allowed to specify _from_ with a bigger number than _to_.

_Meta information:_

_#loop_ adds the following keys/values to the optional _$meta_ variable:

+---------------+-----------------------------------------------------------+
| Name          | Description                                               |
+===============+===========================================================+
| is_first      | is true, if it's the first iteration step                 |
+----------------+----------------------------------------------------------+
| is_last       | is true, if it's the last iteration step                  |
+---------------+-----------------------------------------------------------+
| key           | number of iteration step starting with 0                  |
+---------------+-----------------------------------------------------------+
| pos           | number of iteration step starting with 0                  |
+---------------+-----------------------------------------------------------+
| count         | total number of items in array                            |
+---------------+-----------------------------------------------------------+

The properties _pos_ and _key_ contain the same values. The property _key_ is provided, 
to return the same meta information for both iteration block types _#foreach_ and 
_#loop_.

To access the properties in a template, just specify a third parameter, a variable
for the _#loop_ block:


    {{#loop($i, 0, 5, $meta)}}
        {{if($meta:is_first)}}
            ...
        {{#end}}
    {{#end}}

### #onchange

This command is normally used inside of a block, wich is repeated by commands 
like _#foreach_ and _#loop_. With this command it's possible to observe the value
of a template variable. If the variable changes between _two_ calls of 
_#onchange_, the enclosed block will be executed.

    {{#onchange($value)}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $value        | mixed     | x         | variable to observe                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

The example below demonstrates, how the variable _$step_ is observed. If _$step_
changes it's value, the enclosed block will be executed.

    {{#loop($step, 0, 5)}}
        ...
        {{#onchange($step)}}
            changed
        {{#end}}
    {{#end}}

### #trigger

This command is normally used inside of a block, wich is repeated by commands 
like _#foreach_ and _#loop_. _#trigger_ defines an internal counter, which is 
increased for each loop iteration by _1_. If a specified step is reached, the
enclosed template block will be executed.

    {{#trigger($steps, $start [, $reset])}}
    ...
    {{#end}}

_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $steps        | int       | x         | number of steps till trigger is activated     |
+---------------+-----------+-----------+-----------------------------------------------+
| $start        | int       | -         | optional step to start trigger counter at     |
+---------------+-----------+-----------+-----------------------------------------------+
| $reset        | mixed     | -         | optional variable may be used to reset the    |
|               |           |           | trigger. if the value of _reset_ changes      |
|               |           |           | between two steps, the trigger will be        |
|               |           |           | resetted                                      |
+---------------+-----------+-----------+-----------------------------------------------+

Macros
------

Macros are special functions, which are resolved at compile time. The template engine
provides a macro for importing sub-templates. On compile time all main and
sub-templates will be merged together. This is especially useful for the css/javascript 
compressor described later in this documentation.

To make macros resolvable at compile time, they only accept static parameters:

*   constant
*   string
*   number
*   bool

No variables or method calls are allowed for parameters of a macro.

    <!-- allowed -->
    {{@import('static.html')}}
    
    <!-- not allowed -->
    {{@import($variable)}}

Besides the already provided macros, it's possible to register own macros.

### @import

This macro is used to import sub-templates:

    {{@import('static.html')}}

### @uniqid

Generates a unique ID at compile time. This is useful for example to force 
inclusion of a javascript/css multiple times and to prevent the optimizer to filter
multiple time inclusion of external javascript/css files.

    <script type="text/javascript" src="libsjs/lib.js?{{@uniqid()}}"></script>
    <script type="text/javascript" src="libsjs/lib.js?{{@uniqid()}}"></script>

Functions
---------

The template engine provides several built-in functions. Besides the built-in
functions it's possible to register additional functions that can be used in
a template. The built-in functions are rewritten to optimized PHP code at compile
time, but all parameters are resolved at request time. Because of this, almost all
types of parameters are allowed:

*   variable
*   constant
*   method
*   string
*   number
*   bool

The template has the following built-in functions:


### Localization -- _

Localization is only 

Strings may also contain inline functions, when used with the localization function '_'.
In this case a string specified as first parameter will be processed by at compile time
with an own parser/converter for inline functions. See the "Inline function reference"
for more details.


### Comparison -- eq


### Logic -- and

This method takes two or more parameters and returns _true_, if **all** parameters are
_true_.

    {{and($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 1 and 0 = false -->
    {{and(1, 0)}}

    <!-- output: 1 and 1 = true -->
    {{and(1, 1)}}


### Logic -- not

This method takes one parameters and returns _true_, if the paramet is not true and _false_,
if the parameter is true.

    {{not($a)}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: not 0 = 1 -->
    {{not(0)}}

    <!-- output: not 1 = 0 -->
    {{not(1)}}


### Logic -- or

This method takes two or more parameters and returns _true_, if **one** parameter is _true_.

    {{or($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 0 or 0 = false -->
    {{or(0, 0)}}

    <!-- output: 1 or 1 = true -->
    {{or(1, 0)}}


### Logic -- xor

This method takes two parameters and returns _true_, if **only one** parameter is _true_.

    {{xor($a, $b)}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 1 xor 1 = false -->
    {{xor(0, 0)}}

    <!-- output: 1 xor 0 = true -->
    {{xor(1, 0)}}


### Math -- add

This method takes two or more parameters and add them returning the result.

    {{add($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 1 + 2 + 3 + 4 = 10 -->
    {{add(1, 2, 3, 4)}}


### Math -- decr

This method takes one or two parameters. If one parameter is specified, the input variable
will be decreased by _1_, otherwise a second parameter can be specified to define the amount
the input variable should be decreased by.

    {{decr($a [, $b])}}
    
+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | input variable                                |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | -         | optional amount for decreasing (default: 1)   |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 5 - 2 = 3 -->
    {{decr(5, 2)}}


### Math -- div

This method takes two or more parameters and devides them in the specified order returning the result.

    {{div($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 8 / 4 / 2 = 1 -->
    {{div(8, 4, 2)}}


### Math -- incr

This method takes one or two parameters. If one parameter is specified, the input variable
will be increased by _1_, otherwise a second parameter can be specified to define the amount
the input variable should be increased by.

    {{incr($a [, $b])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | input variable                                |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | -         | optional amount for increasing (default: 1)   |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 5 + 2 = 7 -->
    {{incr(5, 2)}}


### Math -- mod

This method takes two or more parameters and calculates the remainder of a division.

    {{mod($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 5 % 2 = 1 -->
    {{mod(5, 2)}}


### Math -- mul

This method takes two or more parameters and multiplies them in the specified order returning the result.

    {{mul($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 4 * 3 * 2 = 24 -->
    {{mul(4, 3, 2)}}


### Math -- neg

This method negates a value.

    {{neg($a)}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | value to negate                               |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: -10 -->
    {{neg(10)}}


### Math -- sub

This method takes two or more parameters and subtracts them from each other returning the result.

    {{sub($a, $b [, ...])}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $a            | mixed     | x         | first term                                    |
+---------------+-----------+-----------+-----------------------------------------------+
| $b            | mixed     | x         | second term                                   |
+---------------+-----------+-----------+-----------------------------------------------+
| $c, ...       | mixed     | -         | more optional terms                           |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

    <!-- output: 1 - 2 - 3 - 4 = -8 -->
    {{sub(1, 2, 3, 4)}}


### Misc -- include

This function is used to include a (static) file. The include is resolved
at runtime and therefore any embedded template code will not be parsed, instead
the file is treated like a simple text file.

    {{include($file)}}

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $file         | string    | x         | file to include                               |
+---------------+-----------+-----------+-----------------------------------------------+

_Example:_

..  source: html

    {{include('static/text.html')}}

String inline functions
-----------------------

Array inline functions
----------------------

### cycle

This command is normally used inside of a block, which is repeated by commands 
like _#foreach_ and _#loop_. _cycle_ iterates over the provided list of values
each time it is called the internal pointer will be moved to the next element
and this value will be handed over to the control variable. If the pointer
reaches the end of the list it will be reset to the first list item unless the
parameter _pingpong_ is set to true. In this case the pointer will move backwards.

    {{cycle($list [, $pingpong [, $reset]])}}
    
_Parameter:_

+---------------+-----------+-----------+-----------------------------------------------+
| Name          | Type      | Mandatory | Description                                   |
+===============+=======================================================================+
| $list         | array     | x         | array of items to use for iteration           |
+---------------+-----------+-----------+-----------------------------------------------+
| $pingpong     | bool      | -         | go back and forth instead of moving pointer   |
|               |           |           | to the first list item, if end is reached     |
+---------------+-----------+-----------+-----------------------------------------------+
| $reset        | mixed     | -         | optional variable may be used to reset the    |
|               |           |           | pointer. if the value of _reset_ changes      |
|               |           |           | between two steps, the pointer will be        |
|               |           |           | resetted                                      |
+---------------+-----------+-----------+-----------------------------------------------+


EBNF of template syntax
=======================


Javascript & CSS compressor
---------------------------

The compressor is called after all macros and constants in a template are resolved
and the template was converted to PHP code. The compressor looks through the html
template and optimizes Javascript and CSS inclusion. The following tasks are 
performed:

*   optimizing includes reducing necessary HTTP requests
*   compressing Javascript and CSS

Optimizing HTTP Requests
~~~~~~~~~~~~~~~~~~~~~~~~

The compressor parses a HTML template and looks for multiple following

    <script type="text/javascript" src="..."></script>

and

    <link rel="stylesheet" type="text/css" href="..." />

tags. It will combine multiple files and rewrite the HTML template to reduce 
external file inclusion. For example:

_before:_

    <html>
        <link rel="stylesheet" type="text/css" href="default.css" />
        <link rel="stylesheet" type="text/css" href="hacks.css" />
        <script type="text/javascript" src="inc1.js"></script>
        <script type="text/javascript" src="inc2.js"></script>
        <script type="text/javascript" src="inc3.js"></script>
        <body>
            ...
            <script type="text/javascript" src="inc4.js"></script>
            <script type="text/javascript" src="inc5.js"></script>
            ...
        </body>
    </html>

_after:_

    <html>
        <link rel="stylesheet" type="text/css" href="combined.css" />
        <script type="text/javascript" src="combined1.js"></script>
        <body>
            ...
            <script type="text/javascript" src="combined2.js"></script>
            ...
        </body>
    </html>

In addition, each file will be included only once. This is very useful, when you
use sub-templates and want to be sure, that every javascript required by the sub-
template is included and additionally make sure, that it was not already included
by an other template. The following example uses three templates -- main.html, 
sub1.html and sub2.html:

    <!-- main.html -->
    <html>
        <body>
            {{@import('sub1.html')}}
            {{@import('sub2.html')}}
        </body>
    </html>

    <!-- sub1.html -->
    <p>
        <script type="text/javascript" src="inc1.js"></script>
        <script type="text/javascript" src="inc2.js"></script>
        <script type="text/javascript" src="inc3.js"></script>
        ...
    </p>
    
    <!-- sub2.html -->
    <p>
        <script type="text/javascript" src="inc1.js"></script>
        <script type="text/javascript" src="inc2.js"></script>
        <script type="text/javascript" src="inc5.js"></script>
        ...
    </p>

After processing the above example using the compiler, you get one big template
main.html:

    <!-- main.html -->
    <html>
        <body>
            <p>
                <script type="text/javascript" src="inc1.js"></script>
                <script type="text/javascript" src="inc2.js"></script>
                <script type="text/javascript" src="inc3.js"></script>
                ...
            </p>
            <p>
                <script type="text/javascript" src="inc1.js"></script>
                <script type="text/javascript" src="inc2.js"></script>
                <script type="text/javascript" src="inc5.js"></script>
                ...
            </p>
        </body>
    </html>

The compressor will now optimize this and remove duplicate file inclusions:

    <!-- main.html -->
    <html>
        <body>
            <p>
                <script type="text/javascript" src="inc1.js"></script>
                <script type="text/javascript" src="inc2.js"></script>
                <script type="text/javascript" src="inc3.js"></script>
                ...
            </p>
            <p>
                <script type="text/javascript" src="inc5.js"></script>
                ...
            </p>
        </body>
    </html>

As the next step the optimizer will combine the inclusions, so there are actually
only two required includes left:

    <!-- main.html -->
    <html>
        <body>
            <p>
                <script type="text/javascript" src="combined.js"></script>
                ...
            </p>
            <p>
                <script type="text/javascript" src="inc5.js"></script>
                ...
            </p>
        </body>
    </html>

In the example above the file _combined.js_ contains all three _inc1.js_, _inc2.js_ and
_inc3.js_. The second block was reduced to only include _inc5.js_, because it's the only
javascript include still missing for the page.

Sometimes it is required to include a javascript multiple times. This can be
achieved simply by adding a unique identifier to the filename. For example: 

    <!-- main.html -->
    <html>
        <body>
            <p>
                <script type="text/javascript" src="inc1.js"></script>
                <script type="text/javascript" src="inc2.js"></script>
                <script type="text/javascript" src="inc3.js"></script>
                ...
            </p>
            <p>
                <script type="text/javascript" src="inc1.js?{{@uniqid()}}"></script>
                <script type="text/javascript" src="inc2.js?{{@uniqid()}}"></script>
                <script type="text/javascript" src="inc5.js"></script>
                ...
            </p>
        </body>
    </html>

_inc1.js_ and _inc2.js_ from the second block will be treated as different include files 
than _inc1.js_ and _inc2.js_ from the first block even if they have same content -- just 
because it's a different URL. So the resulting snippet after optimizing is:

    <!-- main.html -->
    <html>
        <body>
            <p>
                <script type="text/javascript" src="combined1.js"></script>
                ...
            </p>
            <p>
                <script type="text/javascript" src="combined2.js"></script>
                ...
            </p>
        </body>
    </html>

Compressing Javascript and CSS
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The compressor uses YUI Compressor to compress javascript and CSS files.
