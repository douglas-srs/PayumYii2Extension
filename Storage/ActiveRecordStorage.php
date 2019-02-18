<?php
namespace Payum\Yii2Extension\Storage;

use InvalidArgumentException;
use Payum\Core\Exception\LogicException;
use Payum\Core\Model\ArrayObject;
use Payum\Core\Model\Identificator;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\AbstractStorage;

class ActiveRecordStorage extends AbstractStorage
{
    protected $_tableName;
    public $_modelClass;

    public function __construct($tableName, $modelClass)
    {
        parent::__construct($modelClass);

        $this->_tableName = $tableName;
        $this->_modelClass = $modelClass;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $model = new $this->_modelClass;
        //$model->scenario = 'insert';
        //$model->tableName = $this->_tableName;
        return $model;
    }

    public static function getDb(){
        return \Yii::$app->get('userdb');
    }

    /*
    public function createModel()
    {
        return new $this->modelClass('insert', $this->_tableName);
    }*/

    /**
     * {@inheritDoc}
     */
    protected function doUpdateModel($model)
    {
        //die(print_r($model));
        $model->save();
    }

    /**
     * {@inheritDoc}
     */
    protected function doDeleteModel($model)
    {
        $model->delete();
    }

    /**
     * {@inheritDoc}
     */
    protected function doGetIdentity($model)
    {
        if ($model->isNewRecord) {
            throw new LogicException('The model must be persisted before usage of this method');
        }
        return new Identity($model->{$model->primaryKey()}, $model);
    }

    /**
     * {@inheritDoc}
     */
    function findModelById($id)
    {
        $className = $this->modelClass;
        return $className::findModelById($this->_tableName, $id);
    }

    /**
     * {@inheritDoc}
     */
    protected function assertModelSupported($model)
    {
        parent::assertModelSupported($model);

        if (false == $model instanceof \yii\db\ActiveRecord) {
            throw new InvalidArgumentException(
                'Model required to have activeRecord property, which should be sub class of \yii\db\ActiveRecord class.'
            );
        }
    }

    function doFind($id){
        //return $this->className()->findOne($id);
        $model = new $this->_modelClass;
        return $model->findOne($id);
    }

    function findBy(array $criteria){
        //return $this->className()->findOne($criteria);
        $model = new $this->_modelClass;
        return $model->findOne($criteria);
    }

}
