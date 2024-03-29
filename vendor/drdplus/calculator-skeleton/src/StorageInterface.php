<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

interface StorageInterface
{
    public function storeValues(array $values, ?\DateTimeInterface $cookiesTtlDateTime);

    public function deleteAll();

    public function getValue(string $name);

    public function getValues(): array;
}
