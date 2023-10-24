<?php
namespace App\Service\Serializer;

class Normalizer
{
    public function normalizeByField(mixed $object, $field)
    {
        $getterFunction ='get'.ucfirst($field);
        if(is_object($object)){
            return $object->$getterFunction();
        }
        return array_map(function ($objectInner) use ($getterFunction) {
            return $objectInner->$getterFunction();
        },$object);
    }

}