<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     ProgressBar
 * @copyright   2019 Podvirnyy Nikita (KRypt0n_)
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @author      Podvirnyy Nikita (KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 */

namespace ProgressBar;

class ProgressBar
{
    protected $maxCount;
    protected $length;
    protected $prefix;
    protected $postfix;
    protected $progressChar;
    protected $skeleton; // Скелет прогресс бара
    protected $exp; // Экспонента прогресса. Формально - количество символов, на которое будет изменяться прогресс бар за 1 процент

    /**
     * Конструктор прогресс бара
     * 
     * @param double $maxCount - максимальное число операндов до достижения цели
     * @param int $length - длина прогресс бара (в символах, учитывается только активное поле символов)
     * @param string $progressChar - символ, которым будет заполняться прогресс бар
     * 
     * @throws \Exception - выбрасывает исключения при аллогичных значениях параметров
     * 
     * После инициализации класса сразу же отрисовывается пустой прогресс бар
     * 
     * @example:
     * 
     * $progress = new \ProgressBar\ProgressBar (228, 25);
     * 
     * for ($i = 0; $i <= 228; ++$i)
     *     $progress->update ($i);
     * 
     * $progress->clear (); // Удаляем прогресс бар после отработки
     * 
     */

    public function __construct ($maxCount, $length, $prefix = '', $postfix = '', $progressChar = '█')
    {
        if (!is_numeric ($maxCount) || $maxCount < 0)
            throw new \Exception ('$maxCount param must be a non-negative number');

        if (!is_int ($length) || $length <= 0)
            throw new \Exception ('$length param must be an integer bigger than zero');

        if (!is_string ($progressChar))
            throw new \Exception ('$progressChar param must be an symbol');

        $this->maxCount     = $maxCount;
        $this->length       = $length;
        $this->prefix       = $prefix;
        $this->postfix      = $postfix;
        $this->progressChar = $progressChar;

        $this->skeleton = $prefix .'0% |';

        for ($i = 0; $i < $length; ++$i)
            $this->skeleton .= ' ';

        $this->skeleton .= '|'. $postfix;
        $this->exp = $length / 100;

        echo $this->skeleton;
    }

    /**
     * Обновление прогресс бара
     * 
     * @param double $position - позиция прогресс бара
     * 
     * @return int - возвращает процентное соотношение прогресс бара
     * 
     * @throws \Exception - выбрасывает исключения при неверных значениях параметра $position
     * 
     */

    public function update ($position)
    {
        if ($position > $this->maxCount)
            throw new \Exception ('$position param mustn\'t be upper than $maxCount');

        if ($position < 0)
            throw new \Exception ('$position param must be upper than zero');
        
        $permLength = strlen ($this->skeleton);

        $this->skeleton = $this->prefix . ($process = (int)($position / $this->maxCount * 100)) .'% |';
        
        $processExp = $process * $this->exp;

        for ($i = 0; $i < $this->length; ++$i)
            $this->skeleton .= $i < $processExp ?
                $this->progressChar : ' ';

        $this->skeleton .= '|'. $this->postfix;

        $this->offset ($permLength);
        echo $this->skeleton;

        return $process;
    }

    /**
     * Очистка прогресс бара
     * Удаляет прогресс бар из консоли, заполняя его место пробелами и смещая указатель в начало бывшего прогресс бара
     * 
     */

    public function clear ()
    {
        $this->offset ($length = strlen ($this->skeleton));

        echo str_repeat (' ', $length);

        $this->offset ($length);
    }

    /**
     * Смещение указателя на $length символов
     * 
     * @param int $length - количество символов для смещения
     * 
     */

    protected function offset ($length)
    {
        for ($i = 0; $i < $length; ++$i)
            echo chr (8);
    }
}
