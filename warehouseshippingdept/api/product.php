<?php
    class Product {
        public $id = -1;

        public function __construct(
            public $name, 
            public $weight_kg, 
            public $width_cm,
            public $length_cm,  
            public $height_cm
        ){}
    }