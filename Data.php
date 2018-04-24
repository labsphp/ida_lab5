<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 22.04.2018
 * Time: 20:30
 */
class Data
{
    private $matrixX = [];
    private $matrixY = [];
    private $countMatrixYElements = 0;
    private $bettaModel;
    private $matrixYModel = [];

    function __construct(array $dataArray)
    {
        //заполняем вектора
        $this->fillVectors($dataArray);
        //Нормируем вектора
        $this->normalizeVectorX($this->matrixX);
        $this->normalizeVectorY($this->matrixY);
    }

    /**
     * @return array
     */
    public function getMatrixX(): array
    {
        return $this->matrixX;
    }

    /**
     * @return array
     */
    public function getMatrixY(): array
    {
        return $this->matrixY;
    }

    /**
     * @return mixed
     */
    public function getBettaModel()
    {
        return $this->bettaModel;
    }

    /**
     * @param mixed $bettaModel
     */
    public function setBettaModel($bettaModel)
    {
        $this->bettaModel = $bettaModel;
    }

    /**
     * @return int
     */
    public function getCountMatrixYElements(): int
    {
        return $this->countMatrixYElements;
    }

    /**
     * @param array $matrixX
     */
    public function setMatrixX(array $matrixX)
    {
        $this->matrixX = $matrixX;
    }

    /**
     * @param array $matrixYModel
     */
    public function setMatrixYModel(array $matrixYModel)
    {
        $this->matrixYModel = $matrixYModel;
    }

    /**
     * @return array
     */
    public function getMatrixYModel(): array
    {
        return $this->matrixYModel;
    }

    /**
     * Заполнение векторов данными( преобразование массива вида [[1,1,1],[2,2,2]]=>[[1,2],[1,2],[1,2]])
     * @param array $data
     */
    private function fillVectors(array $data):void
    {
        $firstStep = true;
        for ($i = 0; $i < count($data); $i++) {
            //Последний элемент помещаем в вектор y
            $this->matrixY[] = array_pop($data[$i]);
            //Подсчитываем кол-во элементов в векторе
            $this->countMatrixYElements++;
            for ($j = 0; $j < count($data[$i]); $j++) {
                //Если вектор еще не существует, создаем его
                if ($firstStep) {
                    $this->matrixX[$j] = [];
                }
                //помещаем элемент в нужный нам вектор
                $this->matrixX[$j][] = $data[$i][$j];
            }
            $firstStep = false;
        }
        return;
    }


    /**
     * Нормируем элементы матрицы Х
     * @param array $vectors
     */
    private function normalizeVectorX(array &$vectors):void
    {
        foreach ($vectors as &$vector) {
            $max = max($vector);
            $min = min($vector);
            $diff = $max - $min;
            foreach ($vector as &$item) {
                $item /= $diff;
            }
        }
        return;
    }

    /**
     * Нормируем элементы вектора У
     * @param array $vector
     */
    private function normalizeVectorY(array &$vector):void
    {
        $max = max($vector);
        $min = min($vector);
        $diff = $max - $min;
        foreach ($vector as &$item) {
            $item /= $diff;
        }
        return;
    }


}