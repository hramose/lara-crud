<?php
/**
 * Created by PhpStorm.
 * User: Vitalik
 * Date: 18.09.2017
 * Time: 13:09
 */

namespace Trafik8787\LaraCrud\Contracts;


use Trafik8787\LaraCrud\Contracts\Component\TabsInterface;

interface FormManagerInterface
{
    /**
     * @param $typeSql тип данных
     * @param $nameField
     * @return mixed
     */
    public function getDataType ($typeSql, $nameField);

    /**
     * @return array
     */
    public function getNameColumns ():array ;

    /**
     * @return array
     */
    public function getTypeColumns ():array ;

    /**
     * @return mixed
     */
    public function getArrayField ();

    /**
     * @return mixed
     */
    public function getMultiple(string $field):bool;

}