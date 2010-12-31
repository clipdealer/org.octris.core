/****c* libsjs/{{$directory}}
 * NAME
 *      {{$directory}}.js
 * FUNCTION
 *      main javascript library for {{$directory}}
 * COPYRIGHT
 *      copyright (c) {{$year}} by {{$company}}
 * AUTHOR
 *      {{$author}} <{{$email}}>
 ****
 */

{{#foreach($ns, explode('.', $directory), $meta)}}{{#if($meta:is_first)}}
if (!('{{$ns}}' in window)) window['{{$ns}}'] = {};
{{let($_path, $ns)}}{{#else}}
if (!('{{$ns}}' in {{$_path}})) {{$_path}}.{{$ns}} = {};
{{let($_path, concat($_path, '.', $ns))}}{{#end}}{{#end}}