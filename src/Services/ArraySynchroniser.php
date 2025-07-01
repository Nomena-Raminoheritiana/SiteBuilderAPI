<?php
namespace App\Services;

class ArraySynchronizer
{
    public static function synchronize(array $reference, array $target): array
    {
        foreach ($reference as $key => $refValue) {
            if (array_key_exists($key, $target)) {
                $targetValue = $target[$key];

                if (is_array($refValue) && is_array($targetValue)) {
                    $refIsAssoc = self::isAssoc($refValue);
                    $targetIsAssoc = self::isAssoc($targetValue);

                    if ($refIsAssoc && !$targetIsAssoc) {
                        // ref associatif, target indexé (liste) → fusion simple, ou garder target
                        $target[$key] = self::synchronize($refValue, $targetValue);
                    } elseif (!$refIsAssoc && $targetIsAssoc) {
                        // ref indexé, target associatif → convertir target en liste
                        $targetConverted = [$targetValue];
                        $target[$key] = self::synchronize($refValue, $targetConverted);
                    } elseif ($refIsAssoc && $targetIsAssoc) {
                        $target[$key] = self::synchronize($refValue, $targetValue);
                    } elseif (!$refIsAssoc && !$targetIsAssoc) {
                        if (self::containsAssoc($refValue) || self::containsAssoc($targetValue)) {
                            $refTemplate = $refValue[0] ?? [];

                            $mergedList = [];
                            foreach ($targetValue as $item) {
                                if (is_array($item)) {
                                    $mergedList[] = self::synchronize($refTemplate, $item);
                                } else {
                                    $mergedList[] = $item;
                                }
                            }
                            $target[$key] = $mergedList;
                        } else {
                            $target[$key] = $targetValue;
                        }
                    }
                } else {
                    // scalar : ne pas écraser
                    $target[$key] = $targetValue;
                }
            } else {
                $target[$key] = $refValue;
            }
        }

        // Supprimer clés non dans reference
        foreach ($target as $key => $_) {
            if (!array_key_exists($key, $reference)) {
                unset($target[$key]);
            }
        }

        return $target;
    }

    private static function isAssoc(array $arr): bool
    {
        return $arr !== [] && array_keys($arr) !== range(0, count($arr) - 1);
    }

    private static function containsAssoc(array $arr): bool
    {
        foreach ($arr as $item) {
            if (is_array($item) && self::isAssoc($item)) {
                return true;
            }
        }
        return false;
    }
}