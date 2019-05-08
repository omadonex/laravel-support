<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

class ScaleCalculator
{
    /*
     * Константы скалирования и осей
     */
    const INCH_SM = 2.54;               //2.54 сантиметра в дюйме
    const DEFAULT_SCALE_ERROR = 0.5;    //погрешность измерений (мм)
    const SCALE_TYPE_NO = 'no';         //скалирование: нет
    const SCALE_TYPE_UP = 'up';         //скалирование: вверх (растяжение)
    const SCALE_TYPE_DOWN = 'down';     //скалирование: вниз (сжатие)
    const AXIS_X = 'x';                 //ось: по ширине
    const AXIS_Y = 'y';                 //ось: по высоте

    /*
     * Внутренние переменные
     */
    private $wPix;          //оригинал: ширина (пиксели)
    private $hPix;          //оригинал: высота (пиксели)
    private $w;             //оригинал: ширина (мм)
    private $h;             //оригинал: высота (мм)
    private $dpi;           //оригинал: разрешение (dpi)
    private $scaleError;    //допустимая погрешность измерений (мм)

    /**
     * ScaleCalculator constructor.
     * @param $wPix
     * @param $hPix
     * @param $dpi
     * @param float $scaleError
     */
    public function __construct($wPix, $hPix, $dpi, $scaleError = self::DEFAULT_SCALE_ERROR)
    {
        $this->wPix = $wPix;
        $this->hPix = $hPix;
        $this->w = self::toMm($wPix, $dpi);
        $this->h = self::toMm($hPix, $dpi);
        $this->dpi = $dpi;
        $this->scaleError = $scaleError;
    }

    /**
     * @param $pix
     * @param $dpi
     * @return float|int
     */
    public static function toMm($pix, $dpi)
    {
        return $pix / $dpi * self::INCH_SM * 10;
    }

    /**
     * @param $mm
     * @param $dpi
     * @return float
     */
    public static function toPix($mm, $dpi)
    {
        return round($mm * $dpi / (self::INCH_SM * 10));
    }

    /**
     * Возвращает данные о скалировании к нужным размерам, на вход принимает размеры, к которым необходимо
     * привести исходные размеры
     *
     * @param $wTo (нужная ширина)
     * @param $hTo (нужная высота)
     * @return array
     */
    public function getScaleData($wTo, $hTo)
    {
        //Получаем предполагаемый вариант скалирования по осям
        $xScaleType = $this->getAxisScaleType($this->w, $wTo);
        $yScaleType = $this->getAxisScaleType($this->h, $hTo);

        //Если по обеим осям скалирование не нужно, то ничего не пересчитываем
        if (($xScaleType === self::SCALE_TYPE_NO) && ($yScaleType === self::SCALE_TYPE_NO)) {
            return $this->prepareScaleData();
        }

        //Если по какой-то из осей не нужно скалирование, значит нам его нужно применить по другой оси,
        //но тут есть разные варианты
        //1. По второй оси предполагаемое скалирование "ВНИЗ" - в этом случае мы не применяем скалирвоание и оставляем
        //как есть, так как, если мы его применим, то уменьшится другая сторона меньше требуемого размера
        //2. Скалируем по второй оси "ВВЕРХ"
        if (($xScaleType === self::SCALE_TYPE_NO) || ($yScaleType === self::SCALE_TYPE_NO)) {
            //1 вариант
            if (($xScaleType === self::SCALE_TYPE_DOWN) || ($yScaleType === self::SCALE_TYPE_DOWN)) {
                return $this->prepareScaleData();
            }

            //2 вариант
            return $this->scaleEval($wTo, $hTo, self::SCALE_TYPE_UP);
        }

        //Если дошли до этого места, то по обеим осям предполагается скалирование, НО, ВАЖНО, если скалирование разное,
        //то мы вычисляем скалирование в обоих направлениях и считаем минимальную поправку в абсолютных значениях.
        //Применяем тот вариант, в котором она меньше.
        if ($xScaleType !== $yScaleType) {
            $data1 = $this->scaleEval($wTo, $hTo, self::SCALE_TYPE_UP, true);
            $data2 = $this->scaleEval($wTo, $hTo, self::SCALE_TYPE_DOWN, true);
            $maxAdjust1 = max(abs($data1['adjust']['w']), abs($data1['adjust']['h']));
            $maxAdjust2 = max(abs($data2['adjust']['w']), abs($data2['adjust']['h']));
            if ($maxAdjust1 < $maxAdjust2) {
                return $data1;
            }

            return $data2;
        }

        //Скалирование по обеим осям одинаковое, считаем нужную пропорцию и применяем скалирование
        return $this->scaleEval($wTo, $hTo, $xScaleType);
    }

    /**
     * Подготавливает возвращаемые данные в одинаковый формат
     * Если $applied === false - это значит, что скалирование не было применено
     *
     * @param bool $applied
     * @param array $scaled
     * @param array $adjust
     * @return array
     */
    private function prepareScaleData($applied = false, $scaled = [], $adjust = [])
    {
        return [
            'applied' => $applied,
            'original' => [
                'w' => $this->w,
                'h' => $this->h,
                'dpi' => $this->dpi,
                'wPix' => $this->wPix,
                'hPix' => $this->hPix,
            ],
            'scaled' => $applied ? $scaled : [
                'w' => $this->w,
                'h' => $this->h,
                'dpi' => $this->dpi,
                'proportion' => 1,
                'axis' => null,
                'scale' => null,
            ],
            'adjust' => $applied ? $adjust : [
                'w' => 0,
                'h' => 0,
                'wPix' => 0,
                'hPix' => 0,
            ],
        ];
    }

    /**
     * Вычисляяет скалирование в нужном направлении (вверх или вниз)
     * Особое свойсто имеет необязательный параметр (скалирование с выбором), он применяется если необходимо
     * посчитать скалирование в двух направлениях (когда разное скалирование по осям)
     *
     * @param $wTo (нужная ширина)
     * @param $hTo (нужная высота)
     * @param $scaleType (направление скалирования)
     * @param bool $scaleTypeDiff (скалирование с выбором)
     * @return array
     */
    private function scaleEval($wTo, $hTo, $scaleType, $scaleTypeDiff = false)
    {
        //Высчитываем необходимую пропорцию для осей в зависимости от направления
        $scaleParams = $this->getScaleParams($wTo, $hTo, $scaleType, $scaleTypeDiff);

        //Пересчитываем размеры
        $dpi = round($this->dpi / $scaleParams['proportion']);
        $scaled = [
            'w' => ($scaleParams['axis'] === self::AXIS_X) ? $wTo : $this->w * $scaleParams['proportion'],
            'h' => ($scaleParams['axis'] === self::AXIS_Y) ? $hTo : $this->h * $scaleParams['proportion'],
            'dpi' => $dpi,
            'proportion' => $scaleParams['proportion'],
            'axis' => $scaleParams['axis'],
            'scale' => $scaleType,
        ];

        //Подсчитываем растяжение (хвостики, которые необходимо либо обрезать, либо растянуть(
        $adjustW = ($scaleParams['axis'] === self::AXIS_X) ? 0 : $wTo - $scaled['w'];
        $adjustH = ($scaleParams['axis'] === self::AXIS_Y) ? 0 : $hTo - $scaled['h'];
        $adjust = [
            'w' => $adjustW,
            'h' => $adjustH,
            'wPix' => self::toPix($adjustW, $dpi),
            'hPix' => self::toPix($adjustH, $dpi),
        ];

        return $this->prepareScaleData(true, $scaled, $adjust);
    }

    /**
     * Вычисляет направление скалирования
     *
     * @param $param
     * @param $paramNeed
     * @return string
     */
    private function getAxisScaleType($param, $paramNeed)
    {
        if (abs($param - $paramNeed) < $this->scaleError) {
            return self::SCALE_TYPE_NO;
        }

        if ($param > $paramNeed) {
            return self::SCALE_TYPE_DOWN;
        }

        if ($param < $paramNeed) {
            return self::SCALE_TYPE_UP;
        }

        return self::SCALE_TYPE_NO;
    }

    /**
     * Вычисляет пропорцию скалирования
     *
     * @param $param
     * @param $paramNeed
     * @param $scaleType
     * @return float|int
     */
    private function getAxisProportion($param, $paramNeed, $scaleType)
    {
        if ($scaleType === self::SCALE_TYPE_NO) {
            return 1;
        }

        return $paramNeed / $param;
    }

    /**
     * Вычисляет необходимую пропорция для скалирование в заданном направлении по осям
     * Особое свойсто имеет необязательный параметр (скалирование с выбором), он применяется если необходимо
     * посчитать скалирование в двух направлениях (когда разное скалирование по осям)
     *
     * @param $wTo
     * @param $hTo
     * @param $scaleType
     * @param $scaleTypeDiff
     * @return array
     */
    private function getScaleParams($wTo, $hTo, $scaleType, $scaleTypeDiff)
    {
        $xProportion = $this->getAxisProportion($this->w, $wTo, $scaleType);
        $yProportion = $this->getAxisProportion($this->h, $hTo, $scaleType);

        if (!$scaleTypeDiff) {
            $proportion = max($xProportion, $yProportion);
        } else {
            if ($scaleType === self::SCALE_TYPE_UP) {
                $proportion = max($xProportion, $yProportion);
            } else {
                $proportion = min($xProportion, $yProportion);
            }
        }
        
        $axis = ($proportion === $xProportion) ? self::AXIS_X : self::AXIS_Y;

        return [
            'proportion' => $proportion,
            'axis' => $axis,
        ];
    }
}