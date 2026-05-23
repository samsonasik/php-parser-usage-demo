<?php

require_once 'vendor/autoload.php';

$parser = (new PhpParser\ParserFactory)->createForNewestSupportedVersion();

// parse
try {
    $stmts = $parser->parse(file_get_contents('some_class.php'));
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
    return;
}

// traverse (walking through the AST) via NodeTraverser with Node visitor(s)
// eg: with NameResolver visitor to resolve namespaced names
$traverser = new PhpParser\NodeTraverser(new PhpParser\NodeVisitor\NameResolver);
$ast = $traverser->traverse($stmts);

// for pretty dumping, can use symfony/var-dumper, tracy/tracy, etc

// show all structures
dump($ast);
// get full namespaced name
dump($ast[0]->stmts[0]->namespacedName);
// get short name
dump($ast[0]->stmts[0]->name);

// rename class name
$ast[0]->stmts[0]->name = new PhpParser\Node\Identifier('RenamedClass');

// regenerate code from modified AST
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$code = $prettyPrinter->prettyPrintFile($ast);

// save modified code back to file
file_put_contents('some_class.php', $code);
