<?php
namespace Payum\Yii2Extension\Storage;

use InvalidArgumentException;
use Payum\Core\Exception\LogicException;
use Payum\Core\Model\ArrayObject;
use Payum\Core\Model\Identificator;
use Payum\Core\Storage\AbstractStorage;

class ActiveRecordStorage extends AbstractStorage
{
    protected $_tableName;

    public function __construct($tableName, $modelClass)
    {
        parent::__construct($modelClass);

        $this->_tableName = $tableName;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        return new $this->modelClass('insert', $this->_tableName);
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

        if (!property_exists(get_class($model), 'activeRecord')
            || false == $model->activeRecord instanceof \yii\db\ActiveRecord) {
            throw new InvalidArgumentException(
                'Model required to have activeRecord property, which should be sub class of \yii\db\ActiveRecord class.'
            );
        }
    }

    function doFind($id){
        return $this->className()->findOne($id);
    }

    function findBy(array $criteria){
        return $this->className()->findOne($criteria);
    }
}
