<?php

class Course extends Eloquent 
{
	public function matches()
    {
        return $this->hasMany('Match');
    }

}