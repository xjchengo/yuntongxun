<?php namespace Xjchen\Yuntongxun;

use Gregwar\Captcha\PhraseBuilderInterface;
use InvalidArgumentException;

class PhraseBuilder implements PhraseBuilderInterface
{
    protected $length;
    protected $charset;

    public function __construct($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789')
    {
        $this->length = intval($length);
        if (!$this->length) {
            throw new InvalidArgumentException('验证码长度必须为大于0的整数');
        }
        if (!is_string($charset) or strlen($charset) == 0) {
            throw new InvalidArgumentException('字符集格式必须为String，且不能为空');
        }
        //支持拆分中文，但目前所用的生成验证码包不支持中文
        $this->charset = preg_split('/(?<!^)(?!$)/u', $charset);
    }

    /**
     * Generates  random phrase of given length with given charset
     *
     * don't use the argument, just for conforming to the interface.
     */
    public function build($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789')
    {
        $phrase = '';

        for ($i = 0; $i < $this->length; $i++) {
            $phrase .= $this->charset[array_rand($this->charset)];
        }

        return $phrase;
    }

    /**
     * "Niceize" a code
     */
    public function niceize($str)
    {
        return strtr(strtolower($str), '01', 'ol');
    }
}
