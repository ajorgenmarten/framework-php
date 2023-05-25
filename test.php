<?php

trait TraitTest {
    public $to_apply = [];
    public $callabes = [];

    function __call($name, $arguments)
    {
        if( in_array($name, $this->to_apply) ) foreach ($this->callabes as $callable) {
            $callable();
        }

        call_user_func([$this, $name], $arguments) && die;
    }
}

class Test {
    use TraitTest;

    public $to_apply = [];
    public $callabes = [];

    protected function functions(array $callabes):Test {
        $this->callabes = $callabes;
        return $this;
    }
    protected function apply(array $methods):void {
        $this->to_apply = $methods;
    }
    function call($name, $arguments = []) {
        call_user_func_array([$this, $name], $arguments);
    }
}

class TestExt extends Test {
    use TraitTest;

    function __construct()
    {
        $this->functions([
            function(){
                echo "first callable\n";
            },
            function() {
                echo "second callable\n";
            }
        ])->apply(["index"]);
    }
    public function index($name) {
        echo "function index $name";
    }

    private function foo() {
        echo "function foo";
    }
}
