<?php

$classDefinition = <<<PHP
<?php 
    namespace $namespace;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class $className extends Eloquent{
        protected \$table = 'table-name';
    }

PHP;

echo $classDefinition;
