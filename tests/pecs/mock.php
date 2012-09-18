<?php

class FooClass{

    function method(){
        return 'foo';
    }

    private function privateMethod(){
    }
}

describe("mock", function(){

    it("should create a new instance of the given interface", function(){
        $mock = \pecs\mock('FooClass')->create();
        
        expect($mock)->to_be_an_instance_of('FooClass');
    });

    it("should have watched methods", function(){
        $mock = \pecs\mock('FooClass')->create();

        $mock->method();
        expect($mock->method)->to_have_been_called();
        
        expect($mock->privateMethod())->to_throw();

        $mock->method(1, 'two');
        expect($mock->method)->to_have_been_called_with(1, 'two');
    });

    it("should return a given value on a given argument list", function(){
        $mock = \pecs\mock('FooClass')
            ->method('method')
                ->on()->return('none')
                ->on(1)->return('one')
                ->on(1, 2)->return('three')
                ->on(array('one', 'two', 'three'))->return(6)
            ->create();

        expect($mock->method())->to_be('none');
        expect($mock->method(1))->to_be('one');
        expect($mock->method(1, 2))->to_be('three');
        expect($mock->method(array('one', 'two', 'three')))->to_be(6);
    });
    
});