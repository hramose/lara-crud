<?php
/**
 * Created by PhpStorm.
 * User: Vitalik
 * Date: 30.08.2017
 * Time: 22:43
 */

namespace Trafik8787\LaraCrud\Models;

use Illuminate\Contracts\Foundation\Application;
use Trafik8787\LaraCrud\Contracts\NodeModelConfigurationInterface;

abstract class NodeModelConfigurationManager implements NodeModelConfigurationInterface
{
    protected $model;
    protected $class;
    public $admin;
    protected $alias;
    protected $app;
    protected $title;
    protected $url;
    public $objRoute;
    private static $objModel;

    protected $buttonDelete = true;
    protected $buttonEdit = true;
    protected $fieldShow = [];
    protected $fieldName = [];
    protected $textLimit = [];
    protected $fieldOrderBy = [0, 'asc'];
    protected $showEntries = 10;
    protected $setWhere = [];
    protected $columnColorWhere = [];


    protected $closure;
    /**
     * NodeModelConfigurationManager constructor.
     * @param Application $app
     * @param null $model
     */
    public function __construct (Application $app, $model = null) {

        $this->app= $app;
        $this->model = $model;
    }

    /**
     * @param $query
     */
    public function scopeTest ($query)
    {
        $query->where('id', '=', 1);
    }

    /**
     * @return null
     */
    public function getModel () {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getModelObj()
    {
        if (empty(self::$objModel)) {
            self::$objModel = new $this->model;
        }
        return self::$objModel;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return mixed
     */
    public function getTitle ()
    {
        return $this->title;
    }


    /**
     * @return array
     */
    public function getFieldName() {
        return $this->fieldName;
    }

    /**
     * @return bool
     */
    public function getButtonDelete (): bool
    {
        return $this->buttonDelete;
    }

    /**
     * @return bool
     */
    public function getButtonEdit (): bool
    {
        return $this->buttonEdit;
    }

    /**
     * @return array
     */
    public function getFieldShow(): array {
        return $this->fieldShow;
    }


    /**
     * @return array
     *
     */
    public function getTextLimit($object)
    {
        foreach ($this->textLimit as $field => $limit) {
            $object->{$field} = str_limit($object->{$field}, $limit, $end = '...');
        }
        return $object;
    }

    /**
     * @return array
     */
    public function getFieldOrderBy(): array
    {
        return $this->fieldOrderBy;
    }

    /**
     * @return mixed
     */
    public function getShowEntries():int
    {
        return $this->showEntries;
    }


    /**
     * @return array
     * todo поля доступные для выборки
     */
    public function nameColumns ():array
    {

        $field = $this->admin->TableColumns;
        $field_name = $this->getFieldName();
        $field_display = $this->getFieldShow();

        //проверяем определен ли масив полей которые должны отображатся и осуществляем схождение масивов всех полей и обьявленных
        if (!empty($field_display)) {
            $field = array_intersect($field_display, $field);
        }

        $data = [];
        foreach ($field as $fields) {

            if (isset($field_name[$fields])) {
                $data[$fields] = $field_name[$fields];
            } else {
                $data[$fields] = $fields;
            }

        }

        return $data;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getWhere($query)
    {
        if (!empty($this->setWhere)) {
            $query->where($this->setWhere[0], $this->setWhere[1], $this->setWhere[2]);
        }

        return $query;
    }


    /**
     * @param $obj
     * @return mixed
     * todo хук для строк таблицы
     */
    public function SetTableRowsRenderCollback ($obj)
    {
        return $this->closure->call($this, $obj);
    }


    /**
     * @return array|bool
     * todo метод готовид масив для віделения цветами строк в таблице
     */
    public function getColumnColorWhere()
    {
        $data = false;
        if (!empty($this->columnColorWhere)) {
            foreach ($this->columnColorWhere as $item) {
                $data[] = [
                    'field' => $item[0],
                    'operand' => $item[1],
                    'value' => $item[2],
                    'color' => $item[3],
                ];
            }
        }

        return $data;
    }
}