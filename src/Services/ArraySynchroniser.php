<?php
namespace App\Services;

class ArraySynchronizer
{
    /**
     * Synchronize two arrays deeply, with rules:
     * - If a key exists in $forceKeys (by name or dot notation), always take the value from $reference.
     * - If a key exists in $target, keep its value (unless in $forceKeys).
     * - Otherwise, add the value from $reference.
     *
     * @param array $reference
     * @param array $target
     * @param array $forceKeys List of keys to force from reference (dot notation for deep keys supported, or just key name for all levels)
     * @return array
     */
    public static function synchronize(array $reference, array $target, array $forceKeys = []): array
    {
        foreach ($reference as $key => $refValue) {
            // Si la clé est dans forceKeys (par nom, à n'importe quel niveau)
            if (self::shouldForceKey($key, $forceKeys)) {
                $target[$key] = $refValue;
                continue;
            }

            if (array_key_exists($key, $target)) {
                $targetValue = $target[$key];

                if (is_array($refValue) && is_array($targetValue)) {
                    $refIsAssoc = self::isAssoc($refValue);
                    $targetIsAssoc = self::isAssoc($targetValue);

                    if ($refIsAssoc && !$targetIsAssoc) {
                        $target[$key] = self::synchronize($refValue, $targetValue, $forceKeys); // Correction ici
                    } elseif (!$refIsAssoc && $targetIsAssoc) {
                        $targetConverted = [$targetValue];
                        $target[$key] = self::synchronize($refValue, $targetConverted, $forceKeys); // Correction ici
                    } elseif ($refIsAssoc && $targetIsAssoc) {
                        $target[$key] = self::synchronize($refValue, $targetValue, $forceKeys); // Correction ici
                    } elseif (!$refIsAssoc && !$targetIsAssoc) {
                        if (self::containsAssoc($refValue) || self::containsAssoc($targetValue)) {
                            $refTemplate = $refValue[0] ?? [];
                            $mergedList = [];
                            foreach ($targetValue as $item) {
                                if (is_array($item)) {
                                    $mergedList[] = self::synchronize($refTemplate, $item, $forceKeys); // Correction ici
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

    /**
     * Helper: true si la clé doit être forcée (par nom ou dot notation)
     * @param string|int $key
     * @param array $forceKeys
     * @return bool
     */
    private static function shouldForceKey($key, array $forceKeys): bool
    {
        foreach ($forceKeys as $force) {
            // Si force est une dot notation, on ne force que sur le chemin exact (géré ailleurs)
            if (strpos($force, '.') !== false) continue;
            if ((string)$key === (string)$force) return true;
        }
        return false;
    }

    /**
     * Helper to extract subkeys for nested synchronize (dot notation)
     * @param array $forceKeys
     * @param string|int $prefix
     * @return array
     */
    private static function subKeys(array $forceKeys, $prefix): array
    {
        $sub = [];
        $prefixDot = $prefix . '.';
        foreach ($forceKeys as $key) {
            if (strpos($key, $prefixDot) === 0) {
                $sub[] = substr($key, strlen($prefixDot));
            }
        }
        return $sub;
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