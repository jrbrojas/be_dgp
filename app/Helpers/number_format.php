<?php

if (!function_exists('parse_flexible_number')) {
    function parse_flexible_number($input)
    {
        if ($input === null || $input === '') return null;

        // Si ya es número
        if (is_numeric($input)) {
            return floatval($input);
        }

        $s = trim((string)$input);

        // Quita basura
        $s = preg_replace('/\s+/', '', $s);
        $s = preg_replace('/[^\d.,+\-]/', '', $s);

        $hasComma = strpos($s, ',') !== false;
        $hasDot = strpos($s, '.') !== false;
        $lastDot = strrpos($s, '.');
        $lastComma = strrpos($s, ',');

        // Caso especial: solo puntos → miles
        if (!$hasComma && $hasDot) {
            $parts = explode('.', $s);
            $allGroupsAreThousands =
                count($parts) > 1 &&
                collect(array_slice($parts, 1))->every(fn ($g) => strlen($g) === 3) &&
                strlen($parts[0]) >= 1 && strlen($parts[0]) <= 3;

            if ($allGroupsAreThousands) {
                $s = str_replace('.', '', $s);
                return is_numeric($s) ? (float)$s : null;
            }
        }

        // Determina separador decimal por último carácter
        if (!$hasComma && !$hasDot) {
            // solo dígitos
            $s = str_replace(['.', ','], '', $s);
        } else {
            $decimalSep = $lastDot > $lastComma ? '.' : ',';
            $thousandsSep = $decimalSep === '.' ? ',' : '.';

            // Quita miles
            $s = str_replace($thousandsSep, '', $s);

            // Cambia coma decimal por punto
            if ($decimalSep === ',') {
                $s = str_replace(',', '.', $s);
            }
        }

        return is_numeric($s) ? (float)$s : null;
    }
}

if (!function_exists('numero_formateado')) {
    function numero_formateado($valor, $options = [])
    {
        $locale = $options['locale'] ?? 'es_PE';
        $decimals = $options['decimals'] ?? 0;
        $fallback = $options['fallback'] ?? '0';

        $num = parse_flexible_number($valor);
        if ($num === null) return $fallback;

        $fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $fmt->format($num);
    }
}
