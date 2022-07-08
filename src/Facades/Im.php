<?php

namespace KyyIM\Facades;

use Illuminate\Support\Facades\Facade;
use KyyIM\Interfaces\GroupInterface;
use KyyIM\Interfaces\ImInterface;
use KyyIM\Interfaces\InstitutionInterface;
use KyyIM\Interfaces\MessageInterface;
use KyyIM\Interfaces\UserInterface;

/**
 * @method static UserInterface user()
 * @method static GroupInterface group()
 * @method static InstitutionInterface institution()
 * @method static MessageInterface message()
 * @see ImInterface
 */
class Im extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string {
        return ImInterface::class;
    }
}
