<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 24.04.2018
 * Time: 19:56
 */
class RegressionAnalysis
{
    private $data = [];

    function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * Линейный регрессионный анализ
     */
    public function regressionAnalysis():void
    {
        //Добавим вектор (1,1,1,1,1) = бетта0_модельне в матрицу Х
        $this->addBetta_0Model();
        //умножаем матрицы X_transposed*X
        $newArray = $this->multipleTransposedMatrix($this->data->getMatrixX(), $this->data->getMatrixX());
        //Умножаем матрциу Х на У
        $xy = $this->multipleMatrix($this->data->getMatrixX(), $this->data->getMatrixY());
        //найдем обратную матрицу
        $invMatrix = $this->findInverseMatrix($newArray);
        //Получим коеффициенты бетта-модельное
        $bettaModel = $this->multipleMatrix($invMatrix, $xy);
        $this->data->setBettaModel($bettaModel);
        return;
    }

    /**
     * Добавление beta_0 = (1,1,1,1,1)  в матрицу Х(в первую колонку)
     */
    private function addBetta_0Model():void
    {
        $count = $this->data->getCountMatrixYElements();
        //Заполняем вектор beta_0Model единицами
        $beta0_model[] = array_fill(0, $count, 1);
        //Соединяем массивы
        $mergedArray = array_merge($beta0_model, $this->data->getMatrixX());
        $this->data->setMatrixX($mergedArray);
        return;
    }

    /**
     * Умножение матрицы на транспонированную ей
     * @param array $matrix1
     * @param array $matrix2
     * @return array
     */
    private function multipleTransposedMatrix(array $matrix1, array $matrix2):array
    {
        $newArr = [];
        //Делаем проход по верхней треугольной матрице
        for ($i = 0; $i < count($matrix1); $i++) {
            for ($j = $i; $j < count($matrix2); $j++) {
                //умножаем вектора
                $sum = 0;
                for ($k = 0; $k < count($matrix1[$i]); $k++) {
                    $sum += $matrix1[$i][$k] * $matrix2[$j][$k];
                }
                $newArr[$i][$j] = $newArr[$j][$i] = $sum;
            }
        }
        return $newArr;
    }

    /**
     * Умножение матриц
     * @param array $matrix1
     * @param array $matrix2
     * @return array
     */
    private function multipleMatrix(array $matrix1, array $matrix2):array
    {
        $newArr = [];
        for ($i = 0; $i < count($matrix1); $i++) {
            for ($j = 0; $j < count($matrix2); $j++) {
                $sum = 0;
                //Если элементы второй матрцицы - массивы ([[1,2,3],[3,2,4]])
                if (is_array($matrix2[$j])) {
                    //умножаем вектора
                    for ($k = 0; $k < count($matrix1[$i]); $k++) {
                        $sum += $matrix1[$i][$k] * $matrix2[$j][$k];
                    }
                    $newArr[$i][$j] = $sum;
                } else {
                    //пришла матрица-вектор ([1,2,3])
                    for ($k = 0; $k < count($matrix1[$i]); $k++) {
                        $sum += $matrix1[$i][$k] * $matrix2[$k];
                    }
                    array_push($newArr, $sum);
                    break;
                }
            }
        }
        return $newArr;
    }

    /**
     * Нахождение обратной матрицы методом Жордано-Гауса
     * @param array $matrix
     * @return array
     */
    private function findInverseMatrix(array $matrix):array
    {
        //Получаем единичную матрциу
        $I = $this->getIdentityMatrix(count($matrix));
        //мерджим матрицы
        for ($i = 0; $i < count($matrix); $i++) {
            $matrix[$i] = array_merge($matrix[$i], $I[$i]);
        }
        $invMatrix = $matrix;
        for ($i = 0; $i < count($matrix); $i++) {
            //Разделяющий элемент - элемент, находящийся на главной диагонали(на него делим текущую строку)
            $pivot = $matrix[$i][$i];
            $pivotIndex = $i;

            //i-й рядок делим на разделяющий элемент
            for ($j = 0; $j < count($matrix[$i]); $j++) {
                $invMatrix[$i][$j] /= $pivot;
            }

            /*
             * Приведем все оставшиеся элементы матрицы к правильному виду
             * newElem = oldElem-(a*b)/pivot
             */
            //Делаем проход по строкам матрицы
            for ($k = 0; $k < count($matrix); $k++) {
                /*
                 * Если текущая строка - строка, содержащая разделяющий элемент, то пропускае ее, так как значения для
                 * нее уже обчислены
                */
                if ($k == $pivotIndex) continue;
                for ($j = 0; $j < count($matrix[$i]); $j++) {
                    $currentValue = $matrix[$k][$j];
                    $a = $b = 0;
                    //Рассчитываем коеффициенты а и b
                    if ($j == $pivotIndex) {
                        $a = $pivot;
                        $b = $currentValue;
                    } else {
                        //элемент, который стоит в рядке с разделяющим элементом в той же позиции, что и текущий элемент
                        $a = $matrix[$pivotIndex][$j];
                        /*элемент, который находится в том же рядке, что и текущий элемент, с
                        индексом = индексу разделяющего элемента
                        */
                        $b = $matrix[$k][$pivotIndex];
                    }

                    $newValue = $currentValue - ($a * $b) / $pivot;
                    $invMatrix[$k][$j] = $newValue;
                }
            }
            $matrix = $invMatrix;
        }
        //Отделим единичную матрицу
        for ($i = 0; $i < count($matrix); $i++) {
            array_splice($matrix[$i], 0, count($I));
        }
        return $matrix;
    }

    /**
     *  Получение единичной матрицы
     * @param int $n
     * @return array
     */
    private function getIdentityMatrix(int $n):array
    {
        $I = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $I[$i][$j] = ($i == $j) ? 1 : 0;
            }
        }
        return $I;
    }

    /**
     * Проверка модели
     * @param array $x
     * @param array $bettaModel
     * @return array
     */
    public function checkModel(array $x, array $bettaModel):array
    {
        //Преобразуем матрицу X
        $x = $this->transformMatrix($x);
        //Получаем y-модельное
        $matrixYModel = $this->multipleMatrix($x, $bettaModel);
        $this->data->setMatrixYModel($matrixYModel);

        return [$this->data->getMatrixY(), $this->data->getMatrixYModel()];
    }

    /**
     * Трансформация матрицы с вида [[1,1,1,1,1],[2,2,2,2,2],[3,3,3,3,3]] =>[[1,2,3],[1,2,3],[1,2,3],[1,2,3],[1,2,3]]
     * @param array $arr
     * @return array
     */
    private function transformMatrix(array $arr):array
    {
        $res = [];
        for ($i = 0; $i < count($arr); $i++) {
            for ($j = 0; $j < count($arr[$i]); $j++) {
                $res[$j][$i] = $arr[$i][$j];
            }
        }
        return $res;
    }

    public function averageError():float
    {
        $n = count($this->data->getBettaModel());
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += abs($this->data->getMatrixY()[$i] - $this->data->getMatrixYModel()[$i]) / $this->data->getMatrixY()[$i];
        }
        return round($sum * 100 / $n, 2);
    }

}