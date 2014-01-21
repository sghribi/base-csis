<?php

namespace CSIS\EamBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Search
{   
    private $booleans;    
    //private $ou;
	//private $non;
	private $tag;
	
	
	private $open;
	private $close;
	
	public function getBooleans() {
    return $this->booleans;
  }
  /*
  public function getOu() {
    return $this->ou;
  }
  
  public function getNon() {
    return $this->non;
  }
  */
  public function getOpen() {
    return $this->open;
  }
  
  public function getTag() {
    return $this->tag;
  }
  
  public function getClose() {
    return $this->close;
  }
  
  public function setBooleans($booleans) {
    $this->booleans = $booleans;

    return $this;
  }
  
  public function setOpen($open) {
    $this->open = $open;

    return $this;
  }
  
  public function setTag($tag) {
    $this->tag = $tag;

    return $this;
  }
  
  public function setClose($close) {
    $this->close = $close;

    return $this;
  }
  
}