<?php
/**
 * Created by PhpStorm.
 * User: Vitalik
 * Date: 18.09.2017
 * Time: 13:25
 */

namespace Trafik8787\LaraCrud\Form;

use Illuminate\Contracts\Foundation\Application;
use Trafik8787\LaraCrud\Contracts\Component\TabsInterface;
use Trafik8787\LaraCrud\Form\Component\ComponentManagerBuilder;


class FormTable extends FormManagerTable
{

    private $tabs;

    public function __construct (TabsInterface $tabs) {
        $this->tabs = $tabs;
    }
    /**
     * @param string $form = edit|insert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderFormEdit () {



        $this->id = $this->admin->route->parameters['adminModelId'];

        $data = [
          'id' => $this->id,
          'urlAction' => $this->admin->route->parameters['adminModel'],
          'titlePage'  => $this->objConfig->getTitle(),
          'formMetod' => 'PATCH',
          'objField' => $this->getFieldRender()
        ];

//        dump($this->admin);
        return view('lara::Form.form', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderFormInsert ()
    {
        $data = [
            'urlAction' => $this->admin->route->parameters['adminModel'],
            'titlePage'  => $this->objConfig->getTitle(),
            'formMetod' => 'POST',
            'objField' => $this->getFieldRender()
        ];

        return view('lara::Form.form', $data);
    }

    /**
     * @return mixed
     */
    public function getModelData () {
        return $this->objConfig->getModelObj()->find($this->id);
    }


    /**
     * @return array
     * todo должен возвращать масив с отрендеренными tamplate component
     */
    public function getFieldRender ()
    {
        /**
         * добавляем в класс Tabs обьект конфигурации
         */
        $this->tabs->objConfig($this->objConfig);

        $model = $this->getModelData();
       // dd($model);
        $result = [];

        foreach ($this->getArrayField() as $item) {
            $model_field_value = null;
            //конструктор форм
            $objBilder = (new ComponentManagerBuilder($item));
                $objBilder->classStyle();
                $objBilder->type();
                $objBilder->label();
                $objBilder->placeholder();
                $objBilder->title();
                $objBilder->name();
                $objBilder->disableEditor();


                if (!empty($model->{$item['field']})) {
                    $model_field_value = $model->{$item['field']};
                }

                $objBilder->value($this->objConfig->getValue($item['field'], $model_field_value));

           // $result[] = $objBilder->build()->run();
            $result[] = $objBilder->build();
        }
        $result = $this->tabs->build($result);

        return $result;
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     * todo метод срабатывает при обновлении
     */
    public function updateForm ()
    {
        $arr_request = $this->admin->getRequest()->all();

        unset($arr_request['_method']);
        unset($arr_request['_token']);

        $nameColumn = $this->objConfig->nameColumns();
        $model = $this->objConfig->getModelObj()->find($arr_request[$this->admin->KeyName]);

        foreach ($arr_request as $name => $item) {
            if (!empty($nameColumn[$name])) {
                $model->{$name} = $item;
            }
        }

        $model->save();

        if (!empty($arr_request['save_button'])) {
            return redirect('/' . config('lara-config.url_group') . '/' . $this->admin->route->parameters['adminModel']);
        }

        return redirect()->back();
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     * todo метод срабатывает при добавлении
     */
    public function insertForm ()
    {
        $arr_request = $this->admin->getRequest()->all();

        unset($arr_request['_token']);

        $nameColumn = $this->objConfig->nameColumns();

        $model = $this->objConfig->getModelObj();

        foreach ($arr_request as $name => $item) {
            if (!empty($nameColumn[$name])) {
                $model->{$name} = $item;
            }
        }
        $model->save();

        if (!empty($arr_request['save_button'])) {
            return redirect('/' . config('lara-config.url_group') . '/' . $this->admin->route->parameters['adminModel']);
        }

        return redirect()->back();
    }

}