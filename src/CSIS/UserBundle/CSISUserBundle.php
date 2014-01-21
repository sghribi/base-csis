<?php

namespace CSIS\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CSISUserBundle extends Bundle {

  public function getParent() {
    return 'FOSUserBundle';
  }

}
