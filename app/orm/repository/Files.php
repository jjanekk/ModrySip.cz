<?php
namespace Orm\Repository;

use Nette\Database\Table\ActiveRow;
use YetORM\Entity;
use Nette\Database\Context as NdbContext;

class Files extends Repository{
    protected $table = 'file';
    protected $entity = 'Orm\Entity\File';

    /** @var  string */
    protected $path;


    /** @param  NdbContext $context */
    function __construct($path, NdbContext $context){
        parent::__construct($context);
        $this->path = $path;

    }

    function delete(Entity $entity){
        $row = $entity->toRow();
        $dir = $row->ref('fotogalery')->dir;
        $path = $this->path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
        $file = $path . $entity->file_name . $entity->surfix;
        if(is_file($file)) unlink($file);

        return  parent::delete($entity);
    }

    function getByGallery($id){
        return $this->createCollection($this->getTable()->where('fotogalery_id', $id));
    }

    function removeViewImage(){
        $this->getTable()->update(array('isViewImage' => 0));
    }

} 