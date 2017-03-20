<?php
    // library of generally useful functions

    // takes a number of cents and returns dollar amount in display format "$xx(..)x.xx"
    function print_amount($cents){
        return "$" . number_format(($cents /100), 2, '.', ' ');
    }