KwParserBundle
==============

This bundle provides an a simple Lexer and a SLR Parser. Both classes can be accessed as a symfony service.

You must provide a context free grammar through configuration to make the tokenization and parsing work.



##Installation

### 1) download the composer package

``` bash
$ php composer.phar require kw/parser-bundle
```



### 2) Enable the Bundle
``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Kw\ParserBundle\KwParserBundle()	
    );
}
```

### 3) Configuration

####Lexer
In order to use the Lexer, to tokenize a string into an array of tokens (which can then be parsed by the GLR Parser) you must provide a set terminal symbols. 
Each terminal symbol consists of a name (in upper case letters) and a regular expression.

####Parser
in order to make the parser do its work a specification of the context free grammar to parse is necessary. You need to specify the start production name and the production rules.

for the production you must follow the follwing conventions:
- all non terminal smybols must be written in lowercase
- all terminal symbols must be in upper case

Here is an example of a valid configuartion for a simple cfg:

``` ymal
#app/config/config.yml
kw_parser:
    cfg:
        start: 'e'
        productions:
            e:
                - ['t']
                - ['e', 'T_PLUS', 't']
            t:
                - ['t', 'T_MULT', 'f']
                - ['f']
            f:
                - ['T_ID']
                - ['T_OPEN', 'e', 'T_CLOSE']
        terminals:
            T_PLUS: '\+'
            T_MULT: '\*'
            T_ID: 'id'
            T_OPEN: '\('
            T_CLOSE: '\)'

```



