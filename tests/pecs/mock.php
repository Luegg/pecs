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

class ExtenderClass extends ClassToMock{
}


describe("pecs", function(){

    describe("mock()", function(){

        it("should return a Mocker", function(){
            $mocker = \pecs\mock('ClassToMock');
            expect($mocker)->to_be_an_instance_of('pecs\\Mocker');
        });

        it("should fail if non existant interface or class is to be mocked", function(){
            expect(function(){
                    \pecs\mock('NonExistantClassToMock');
                })->to_throw();
        });
    });

    describe("Mocker", function(){

        it("should create a new instance of the given class", function(){
            $mock = \pecs\mock('ClassToMock')->create();
            
            expect($mock)->to_be_an_instance_of('ClassToMock');
            expect($mock)->to_be_an_instance_of('InterfaceToMock');
        });

        it("should create a new instance of the given interface", function(){
            $mock = \pecs\mock('InterfaceToMock')->create();

            expect($mock)->to_be_an_instance_of('InterfaceToMock');
        });

        it("should also work within namespaces", function(){
            $mock = \pecs\mock('pecs\\Formatter')->create();

            expect($mock)->to_be_an_instance_of('pecs\\Formatter');
        });

        it("should return a MethodMocker", function(){
            $methodMocker = \pecs\mock('ClassToMock')
                ->method('methodA');

            expect($methodMocker)->to_be_an_instance_of('pecs\\MethodMocker');
        });
    });

    describe("mock", function(){

        it("should have watched functions as properties for each method", function(){
            $mock = \pecs\mock('ClassToMock')->create();

            $mock->methodA();
            $mock->methodB();
            expect($mock->methodA)->to_have_been_called();
            expect($mock->methodB)->to_have_been_called();

            expect(isset($mock->privateMethod))->to_be(false);

            $mock->methodA(1, 'two');
            expect($mock->methodA)->to_have_been_called_with(1, 'two');

            $mock = \pecs\mock('ExtenderClass')->create();

            $mock->methodA();
            $mock->methodB();
            expect($mock->methodA)->to_have_been_called();
            expect($mock->methodB)->to_have_been_called();
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
                    ->on(null, null)->throws('LogicException', 'this one failed')
                ->create();

            expect(function() use ($mock){
                    $mock->methodA();
                })->to_throw();
            expect(function() use ($mock){
                    $mock->methodA(null);
                })->to_throw('LengthException');
            expect(function() use ($mock){
                    $mock->methodA(null, null);
                })->to_throw('LogicException', 'this one failed');
        });
    
    });
});