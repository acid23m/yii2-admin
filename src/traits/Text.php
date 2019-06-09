<?php

namespace dashboard\traits;

use yii\base\InvalidArgumentException;
use yii\helpers\StringHelper;
use yii\validators\IpValidator;
use yii\validators\UrlValidator;

/**
 * Text utilities.
 *
 * @package dashboard\traits
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
trait Text
{
    /**
     * Transliterates filename.
     * @static
     * @param string $name Filename
     * @return string
     */
    public static function translitFile(string $name): string
    {
        $name = \mb_strtolower($name, 'utf-8');

        $abc = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            ' ' => '-',
            '/' => '_',
            '?' => '',
            '!' => '',
            ',' => '',
            ';' => '',
            ':' => '',
            '"' => '',
            "'" => '',
            '«' => '',
            '»' => '',
            '#' => '',
            '`' => '',
            '~' => '',
            '$' => '',
            '%' => '',
            '^' => '',
            '&' => '',
            '*' => ''
        ];

        return \strtr($name, $abc);
    }

    /**
     * Converts html to plain text.
     * @static
     * @param string $html Input HTML
     * @return string
     */
    public static function html2string(string $html): string
    {
        $search = [
            "'<script[^>]*?>.*?</script>'si", // Cut javaScript
            "'<[\/\!]*?[^<>]*?>'si", // Cut HTML-tags
            "'([\r\n])[\s]+'", // Cut space symbols
            "'&(quot|#34);'i", // Replace HTML-entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
//            "'&#(\d+);'e" // PHP
        ];

        $replace = [
            '',
            '',
            "\\1",
            '"',
            '&',
            '<',
            '>',
            ' ',
            \chr(161),
            \chr(162),
            \chr(163),
            \chr(169)
        ];

        return \trim(
            \preg_replace($search, $replace, $html)
        );
    }

    /**
     * Clears string from alphabetic chars.
     * Helpful for price field.
     * @static
     * @param string $str Input string
     * @return int
     */
    public static function stringToInt(string $str): int
    {
        $result = '';

        for ($i = 0, $iMax = \strlen($str); $i < $iMax; $i ++) {
            if (\is_numeric($str[$i])) {
                $result .= $str[$i];
            }
        }

        return (int) $result;
    }

    /**
     * Clears phone number from formatting symbols except digits and plus.
     * @static
     * @param string $phone_number Phone number
     * @return string Only plus and digits
     */
    public static function clearPhone(string $phone_number): string
    {
        return \preg_replace('/[^\+\d]/', '', $phone_number);
    }

    /**
     * Converts text to array.
     * @static
     * @param string $str Input string
     * @param bool $no_empty_str Remove or not empty values
     * @return array List of paragraphs
     */
    public static function nl2array(string $str, bool $no_empty_str = true): array
    {
        $data = \explode(PHP_EOL, $str);

        return $no_empty_str ? \array_filter($data) : $data;
    }

    /**
     * Checks if IP complies to mask.
     * @static
     * @param string $ip
     * @param string $mask
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function compareIpWithMask(string $ip, string $mask): bool
    {
        if ((new IpValidator())->validate($ip, $error) === false) {
            throw new InvalidArgumentException($error);
        }

        $ip_sections = \explode('.', $ip);
        $mask_sections = \explode('.', $mask);

        for ($i = 0; $i < 4; $i ++) {
            if ($mask_sections[$i] !== $ip_sections[$i] && $mask_sections[$i] !== '*') {
                return false;
            }
        }

        return true;
    }

    /**
     * Generates link for iframe source to embed to sites.
     * Supports Youtube, Vimeo.
     * @static
     * @param string $url Direct or share link
     * @return null|string Just url address, not html code with iframe
     */
    public static function embedVideo(string $url): ?string
    {
        $video_id = null;
        $hosting = null;

        // check url address
        $validator = new UrlValidator();
        if (!$validator->validate($url)) {
            return null;
        }

        if (\stripos($url, 'youtube.com') !== false) { // youtube direct site link
            $params = \parse_url($url);

            try {
                if (StringHelper::startsWith($params['path'], '/embed')) {
                    return $url;
                }

                // finds parameter
                $query = \explode('&', $params['query']);
                foreach ($query as &$item) {
                    if (StringHelper::startsWith($item, 'v=')) {
                        $video_id = \ltrim($item, 'v=');
                        $hosting = 'youtube';
                        break;
                    }
                }
            } catch (\Throwable $e) {
            }
        } elseif (\stripos($url, 'youtu.be') !== false) { // youtube share link
            $video_id = StringHelper::basename($url);
            $hosting = 'youtube';
        } elseif (\stripos($url, 'vimeo.com') !== false) { // vimeo link
            $video_id = StringHelper::basename($url);
            $hosting = 'vimeo';
        }

        if ($video_id === null) {
            return null;
        }

        switch ($hosting) {
            case 'youtube':
                return "https://www.youtube.com/embed/$video_id";
            case 'vimeo':
                return "https://player.vimeo.com/video/$video_id";
            default:
                return null;
        }
    }

}
