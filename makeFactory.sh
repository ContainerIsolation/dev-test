#!/usr/bin/env bash

# Create custom factory
# Usage ./makeFactory "Totallywicked\\DevTest\\Model\\ResourceIterator"

FQN="$1"
if [ -z "$FQN" ]; then
    echo "Class name not specified"
    exit 1
fi

CLASS_NAME="${FQN##*\\}"
NAMESPACE="${FQN%\\*}"

echo "\
<?php
namespace $NAMESPACE;

use Totallywicked\DevTest\Factory\AbstractFactory;

/**
 * Factory for @see $CLASS_NAME
 */
class ${CLASS_NAME}Factory extends AbstractFactory
{
    /**
     * @inheritDoc
     */
    protected $className = $CLASS_NAME::class;
}"
