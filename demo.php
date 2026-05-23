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

$traverser = new PhpParser\NodeTraverser(
    // ensure all nodes are cloned before modification, so original AST can be used for format-preserving printing
    new PhpParser\NodeVisitor\CloningVisitor,

    // resolve namespaced names, so we can get full class name with namespace
    new PhpParser\NodeVisitor\NameResolver,
);
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
$code = $prettyPrinter->printFormatPreserving($ast, $stmts, $parser->getTokens());

// save modified code back to file
file_put_contents('some_class.php', $code);
