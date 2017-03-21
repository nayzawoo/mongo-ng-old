<?php

function activeState($route)
{
    return \Request::url() == route($route) ? 'active' : '';
}