<?php namespace Netfizz\FormBuilder;

//use Illuminate\Database\Eloquent\Relations\Relation;
//use Illuminate\Database\Eloquent\Collection;

trait RelationshipTrait {

    /**
     * @var array
     */
    protected $relationsAttibutes = array();


    /**
     *
     */
    protected static function bootRelationshipTrait()
    {

        static::saving(function($instance) {
            $instance->fillRelationsAttributes();
        });

        static::saved(function($instance) {
            $instance->saveRelations();
        });

        static::deleted(function($instance) {
            $instance->cascadeDelete();
        });

    }


    /**
     * @param $attribute
     * @return bool
     */
    public static function isRelationshipProperty($attribute)
    {
        if ( ! method_exists(get_called_class(), $attribute)) {
            return false;
        }

        // call method and check if this method return an eloquent Relationships class
        $relationObj = self::$attribute();
        if (is_subclass_of($relationObj, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            return $relationObj;
        }

        return false;
    }


    /**
     * Fill every attributes which is a relation in relationsAttibutes property
     */
    protected function fillRelationsAttributes()
    {
        foreach($this->attributes as $attribute => $value)
        {
            if ($relationObj = $this->isRelationshipProperty($attribute))
            {
                $this->setRelationsAttributes($attribute, $this->getAttribute($attribute));
                unset($this->attributes[$attribute]);
            }
        }
    }


    /**
     * @param $key
     * @param $value
     */
    public function setRelationsAttributes($key, $value)
    {
        $this->relationsAttibutes[$key] = $value;
    }


    /**
     *
     */
    public function saveRelations()
    {
        foreach($this->relationsAttibutes as $attribute => $collection)
        {
            $relationObj = $this->$attribute();

            if (is_array($collection) && is_array(current($collection)))
            {
                $relatedModel = $relationObj->getRelated();
                $keyName = $relatedModel->getKeyName();
                $previousCollection = $relationObj->getResults();

                $keepItemsIds = array();

                // Update existing items and add new
                foreach($collection as $item)
                {
                    $check = array_filter($item);
                    if (empty($check))
                    {
                        continue;
                    }

                    if (array_key_exists($keyName, $item)
                        && $itemModel = $previousCollection->find($item[$keyName]))
                    {
                        $itemModel->update($item);
                    }
                    else
                    {
                        unset($item[$keyName]);
                        $itemModel = $relationObj->create($item);
                    }

                    $keepItemsIds[] = $itemModel->getKey();
                }

                // delete old relationship
                $deleteIds = array_diff($previousCollection->modelKeys(), $keepItemsIds);
                if ( count($deleteIds) > 0 )
                {
                    $relatedModel->destroy($deleteIds);
                }
            }


            else
            {
                $relationObj->sync($collection);
            }
        }
    }


    /**
     * Perform cascade delete on every related model instance
     */
    public function cascadeDelete()
    {
        // Do not perform cascade delete if the instance is only soft deleted
        if (method_exists($this, 'trashed') && $this->trashed())
        {
            return null;
        }


        // Loop relationships
        foreach($this->getCascadeDeleteRelations() as $relations)
        {
            if ($relationObj = $this->isRelationshipProperty($relations))
            {
                $relationType = class_basename(get_class($relationObj));

                switch($relationType) {
                    case 'BelongsToMany' :
                        $this->$relations()->detach();
                        break;

                    case 'morphTo' :
                    case 'morphOne' :
                    case 'MorphMany' :
                        $this->$relations()->delete();
                        break;
                }
            }
        }
    }


    /**
     * Return model relations method name
     *
     * @return array
     */
    protected function getCascadeDeleteRelations()
    {
        return isset($this->cascadeDelete) ? $this->cascadeDelete : array();
    }

} 