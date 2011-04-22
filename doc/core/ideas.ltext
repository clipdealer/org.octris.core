page / template

-   bind template instance to page?
-   "namespaces" for template variables:

    $tpl->setValue('variable', value, 'boss:forum');
    
    would create a variable -> 'boss:forum:variable' = value
    
    other idea:
    
    $ns = $tpl->getNamespace('boss:forum');
    $ns->setValue('variable', value);
    
    would to the same as the setValue above 


- all datasources are iteratable in context of a template -> dbo, too