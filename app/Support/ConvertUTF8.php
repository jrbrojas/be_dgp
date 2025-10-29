<?php

namespace App\Support;

class ConvertUTF8
{
    /**
     * Detecta la codificación del archivo a partir de un muestreo.
     */
    public static function detectEncoding(string $path): string
    {
        // Detectar UTF-8 BOM
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new \RuntimeException("No se pudo abrir el archivo: $path");
        }
        $bom = fread($fh, 3);
        fclose($fh);
        if ($bom === "\xEF\xBB\xBF") {
            return 'UTF-8-BOM';
        }

        // Muestreamos ~64KB para detección
        $sample = file_get_contents($path, false, null, 0, 64 * 1024);
        if ($sample === false) {
            throw new \RuntimeException("No se pudo leer el archivo: $path");
        }

        // Si es UTF-8 válido, devolvemos UTF-8
        if (mb_check_encoding($sample, 'UTF-8')) {
            return 'UTF-8';
        }

        // Intento de detección: orden común para CSV de Windows
        $enc = mb_detect_encoding($sample, ['Windows-1252', 'ISO-8859-1', 'CP850', 'ASCII'], true);
        return $enc ?: 'ISO-8859-1';
    }

    /**
     * Garantiza un archivo UTF-8. Si ya lo es, lo devuelve tal cual.
     * Si no, crea un archivo temporal convertido a UTF-8 y devuelve su ruta.
     *
     * @param string $path Ruta absoluta del CSV de entrada
     * @param bool $ignoreInvalid Si true, ignora bytes inválidos en la conversión (//IGNORE)
     * @return string Ruta del archivo en UTF-8 (puede ser la misma o una temporal)
     */
    public static function ensureUtf8(string $path, bool $ignoreInvalid = false): string
    {
        $srcEncoding = self::detectEncoding($path);

        // Si es UTF-8 BOM, quitamos BOM por streaming
        if ($srcEncoding === 'UTF-8-BOM') {
            $tmp = self::tempPath($path, 'utf8');
            self::stripBomTo($path, $tmp);
            return $tmp;
        }

        // Si ya es UTF-8 válido, lo devolvemos tal cual
        if ($srcEncoding === 'UTF-8') {
            // Extra check: si trae BOM encubierto o bytes raros, lo podemos limpiar
            $sample = file_get_contents($path, false, null, 0, 64 * 1024);
            if ($sample !== false && mb_check_encoding($sample, 'UTF-8')) {
                return $path;
            }
        }

        // Convertimos desde srcEncoding -> UTF-8 por streaming
        $from = $srcEncoding === 'UTF-8' ? 'UTF-8' : $srcEncoding;
        $tmpOut = self::tempPath($path, 'utf8');

        $in  = fopen($path, 'rb');
        $out = fopen($tmpOut, 'wb');
        if ($in === false || $out === false) {
            if (is_resource($in)) fclose($in);
            if (is_resource($out)) fclose($out);
            throw new \RuntimeException("No se pudo abrir archivo para convertir a UTF-8");
        }

        // Filtro stream iconv: convert.iconv.<FROM>-UTF-8[//IGNORE][//TRANSLIT]
        $suffix = $ignoreInvalid ? '//IGNORE' : '//TRANSLIT';
        $filter = @stream_filter_append($out, "convert.iconv.$from/UTF-8$suffix", STREAM_FILTER_WRITE);
        if ($filter === false) {
            fclose($in); fclose($out);
            throw new \RuntimeException("No se pudo adjuntar filtro de conversión iconv ($from -> UTF-8)");
        }

        // Copiamos por bloques
        while (!feof($in)) {
            $chunk = fread($in, 1024 * 1024); // 1MB
            if ($chunk === false) {
                fclose($in); fclose($out);
                throw new \RuntimeException("Error leyendo el archivo durante la conversión");
            }
            fwrite($out, $chunk);
        }

        fclose($in);
        fclose($out);

        // Validar que el resultado sea UTF-8
        $sampleOut = file_get_contents($tmpOut, false, null, 0, 64 * 1024);
        if ($sampleOut === false || !mb_check_encoding($sampleOut, 'UTF-8')) {
            throw new \RuntimeException("La conversión a UTF-8 no fue válida. Verifica caracteres especiales.");
        }

        return $tmpOut;
    }

    /**
     * Quita el BOM UTF-8 del inicio (si existe) y escribe a un nuevo archivo.
     */
    private static function stripBomTo(string $src, string $dst): void
    {
        $in  = fopen($src, 'rb');
        $out = fopen($dst, 'wb');
        if ($in === false || $out === false) {
            if (is_resource($in)) fclose($in);
            if (is_resource($out)) fclose($out);
            throw new \RuntimeException("No se pudo abrir archivo para quitar BOM");
        }

        $first3 = fread($in, 3);
        if ($first3 !== "\xEF\xBB\xBF") {
            // No había BOM: reescribir desde el principio
            // Retrocede el puntero y copia todo
            if ($first3 !== false) {
                fwrite($out, $first3);
            }
        }

        // Copia el resto
        stream_copy_to_stream($in, $out);

        fclose($in);
        fclose($out);
    }

    /**
     * Genera una ruta temporal junto al archivo original (mismo directorio) con sufijo.
     */
    private static function tempPath(string $path, string $suffix): string
    {
        $dir  = dirname($path);
        $base = pathinfo($path, PATHINFO_FILENAME);
        $ext  = pathinfo($path, PATHINFO_EXTENSION);
        $rand = bin2hex(random_bytes(4));
        $name = $ext ? "{$base}.{$suffix}.{$rand}.{$ext}" : "{$base}.{$suffix}.{$rand}";
        return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    }
}
