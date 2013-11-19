<?php

Class Test {
    // This is a simple class to test the manipulation of php files

    private $var;

    public function Test()
    {
        $this->var = 'hello world';
    }

    public function run($doStuff){
        // Do stuff!
        echo $this->var;
    }
}