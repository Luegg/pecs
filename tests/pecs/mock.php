<?php

interface InterfaceToMock{
    function methodA();
}

class ClassToMock implements InterfaceToMock{

    function methodA(){
        return 'foo';
    }

    function methodB($arg1, $arg2){
        return 'bar';
    }

    private function privateMethod(){
    }
}


describe("pecs", function(){

    describe("mock", function(){

        it("should return a Mocker", function(){
            $mocker = \pecs\mock('ClassToMock');
            expect($mocker)->to_be_an_instance_of('pecs\\Mocker');

            expect(\pecs\mock('NonExistantClassToMock'))->to_throw('Exception');
        });
    });

    describe("Mocker", function(){

        it("should create a new instance of the given interface", function(){
            $mock = \pecs\mock('ClassToMock')->create();
            
            expect($mock)->to_be_an_instance_of('ClassToMock');
            expect($mock)->to_be_an_instance_of('InterfaceToMock');

            $mock = \pecs\mock('InterfaceToMock')->create();

            expect($mock)->to_be_an_instance_of('InterfaceToMock');
        });

        it("should have watched methods", function(){
            $mock = \pecs\mock('ClassToMock')->create();

            $mock->methodA();
            expect($mock->methodA)->to_have_been_called();

            expect($mock->privateMethod())->to_throw();

            $mock->methodA(1, 'two');
            expect($mock->methodA)->to_have_been_called_with(1, 'two');
        });

        it("should return a MethodMocker", function(){
            $methodMocker = \pecs\mock('ClassToMock')
                ->method('methodA');

            expect($methodMocker)->to_be_an_instance_of('pecs\\MethodMocker');
        });

        it("should return a given value on a given argument list", function(){
            $mock = \pecs\mock('ClassToMock')
                ->method('methodA')
                    ->on()->returns('none')
                    ->on(1)->returns('one')
                    ->on(1, 2)->returns('three')
                ->method('methodB')
                    ->on(array('one', 'two', 'three'))->returns(6)
                ->create();

            expect($mock->methodA())->to_be('none');
            expect($mock->methodA(1))->to_be('one');
            expect($mock->methodA(1, 2))->to_be('three');

            expect($mock->methodB(array('one', 'two', 'three')))->to_be(6);
        });

        it("should throw a given exception on a given argument list", function(){
            $mock = \pecs\mock('ClassToMock')
                ->method('methodA')
                    ->on()->throws()
                    ->on(null)->throws('LengthException')
                    ->on()->throws('LogicException', 'this one failed')
                ->create();

            expect($mock->methodA())->to_throw('Exception');
            expect($mock->methodA())->to_throw('LengthException');
            expect($mock->methodA())->to_throw('LogicException', 'this one failed');
        });
        
    });
});