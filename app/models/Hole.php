<?php

class Hole extends Eloquent 
{
	public function course()
    {
        return $this->belongsTo('Course');
    }
	
	public function holescores()
    {
        return $this->hasMany('Holescore');
    }
	
	public function courses()
    {
        return $this->hasManyThrough('Course','Holescore');
    }
}
