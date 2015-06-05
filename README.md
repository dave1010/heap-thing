# Heap thing data structure 

## Behaviour

* add objects to a heap
* process the heap by getting the objects to improve themselves

## Implementation

* while there are results that haven't had a depth check:
 * get the top result that hasn't had a depth check
 * mark that it's had a depth check
 * find do a depth search on it to get another result
 * add the new result to the heap

## Usage

Setup

    composer install

Running tests

    ./vendor/bin/phpunit
    
Running specs

    ./vendor/bin/phpspec run

## Structure

    src - classes
    tests - test classes
    spec - spec classes
    
