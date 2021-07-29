<?php


namespace App\Enum;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PostEnum extends AbstractExtension
{
    const STATUS_POST_OPEN = 0;
    const STATUS_POST_CLOSE = 1;


    public function getStringValue($value ){
        if($value === self::STATUS_POST_CLOSE){
            return 'close';
        }

        if($value === self::STATUS_POST_OPEN){
            return 'open';
        }
    }

    public function getEnum() {
        return array(
            'Ouvert' => self::STATUS_POST_OPEN,
            'Fermé' =>  self::STATUS_POST_CLOSE
        );
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('getStringValue', array($this, 'getStringValue')),
        );
    }
    


}