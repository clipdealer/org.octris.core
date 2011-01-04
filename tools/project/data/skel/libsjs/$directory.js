/**
 * Main javascript library for {{$directory}}.
 *
 * @octdoc      c:libsjs/{{$directory}}
 * @copyright   copyright (c) {{$year}} by {{$company}}
 * @author      {{$author}} <{{$email}}>
 */
/**/

{{#foreach($ns, explode('.', $directory), $meta)}}{{#if($meta:is_first)}}
if (!('{{$ns}}' in window)) window['{{$ns}}'] = {};
{{let($_path, $ns)}}{{#else}}
if (!('{{$ns}}' in {{$_path}})) {{$_path}}.{{$ns}} = {};
{{let($_path, concat($_path, '.', $ns))}}{{#end}}{{#end}}