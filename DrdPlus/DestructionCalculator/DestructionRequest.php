<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\AttackSkeleton\AttackRequest;
use DrdPlus\Codes\Properties\PropertyCode;

class DestructionRequest extends AttackRequest
{
    public const STRENGTH = PropertyCode::STRENGTH;
    public const VOLUME_UNIT = 'volume_unit';
    public const VOLUME_VALUE = 'volume_value';
    public const SQUARE_UNIT = 'square_unit';
    public const SQUARE_VALUE = 'square_value';
    public const MATERIAL = 'material';
    public const ROLL_ON_DESTRUCTING = 'roll_on_destructing';
    public const SHOULD_ROLL_ON_DESTRUCTING = 'new_roll_on_destructing';
    public const INAPPROPRIATE_TOOL = 'inappropriate_tool';
    public const WEAPON_HOLDING_VALUE = 'weapon_holding_value';
    public const ITEM_SIZE = 'item_size';
    public const BODY_SIZE = 'body_size';
}