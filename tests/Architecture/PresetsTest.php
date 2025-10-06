<?php

arch()->preset()->php();

arch()->preset()->security()
    ->ignoring([
        'assert',
        'unserialize',
        'parse_str',
    ]);
