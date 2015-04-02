<?php

namespace CSIS\EamBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SuperSearch
{
    /**
     * @Assert\Valid()
     */
    private $form1;

    /**
     * @Assert\Valid()
     */
    private $form2;

    /**
     * @Assert\Valid()
     */
    private $form3;

    /**
     * @Assert\Valid()
     */
    private $form4;

    /**
     * @Assert\Valid()
     */
    private $form5;

    /**
     * @Assert\Valid()
     */
    private $form6;

    /**
     * @Assert\Valid()
     */
    private $form7;

    /**
     * @Assert\Valid()
     */
    private $form8;

    public function getForm1()
    {
        return $this->form1;
    }

    public function getForm2()
    {
        return $this->form2;
    }

    public function getForm3()
    {
        return $this->form3;
    }

    public function getForm4()
    {
        return $this->form4;
    }

    public function getForm5()
    {
        return $this->form5;
    }

    public function getForm6()
    {
        return $this->form6;
    }

    public function getForm7()
    {
        return $this->form7;
    }

    public function getForm8()
    {
        return $this->form8;
    }

    public function setForm1($form1)
    {
        $this->form1 = $form1;

        return $this;
    }

    public function setForm2($form2)
    {
        $this->form2 = $form2;

        return $this;
    }

    public function setForm3($form3)
    {
        $this->form3 = $form3;

        return $this;
    }

    public function setForm4($form4)
    {
        $this->form4 = $form4;

        return $this;
    }

    public function setForm5($form5)
    {
        $this->form5 = $form5;

        return $this;
    }

    public function setForm6($form6)
    {
        $this->form6 = $form6;

        return $this;
    }

    public function setForm7($form7)
    {
        $this->form7 = $form7;

        return $this;
    }

    public function setForm8($form8)
    {
        $this->form8 = $form8;

        return $this;
    }
}
