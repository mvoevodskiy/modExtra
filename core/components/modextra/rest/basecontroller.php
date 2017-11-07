<?php

class modExtraBaseRestController extends modRestController
{
    public $allowedMethods = array('get');
    public $classKey = 'modResource';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $whereCondition = array();
    public $entities = array();


    /** {@inheritdoc} */
    public function methodAllowed($method)
    {
        return in_array($method, $this->allowedMethods);
    }

    /** {@inheritdoc} */
    public function get()
    {
        if ($this->methodAllowed('get')) {
            return parent::get();
        } else {
            return $this->failure('Method not allowed');
        }
    }

    /** {@inheritdoc} */
    public function post()
    {
        if ($this->methodAllowed('post')) {
            return parent::post();
        } else {
            return $this->failure('Method not allowed');
        }
    }

    /** {@inheritdoc} */
    public function put()
    {
        if ($this->methodAllowed('put')) {
            return parent::put();
        } else {
            return $this->failure('Method not allowed');
        }
    }

    /** {@inheritdoc} */
    public function delete()
    {
        if ($this->methodAllowed('delete')) {
            return parent::delete();
        } else {
            return $this->failure('Method not allowed');
        }
    }

    /** {@inheritdoc} */
    protected function prepareListQueryBeforeCount(xPDOQuery $c)
    {
        if (!empty($this->whereCondition)) {
            $c->where($this->whereCondition);
        }
        return $c;
    }

    /**
     * @param xPDOQuery $q
     * @return array
     */
    public function query($q)
    {
        $q->prepare();
        $q->stmt->execute();
        $this->modx->executedQueries++;
        $res = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }


    /**
     * @param string $classKey
     * @param int|array $criteria
     * @param int $limit
     * @return array
     */
    public function getEntities($classKey, $criteria, $limit = 1)
    {
        $key = md5(serialize($criteria));
        if (!isset($this->entities[$classKey][$key])) {
            $q = $this->modx->newQuery($classKey);
            $q->select(array($classKey => '*'));
            $q->where($criteria);
            $q->limit($limit);
            $res = $this->query($q);

            if ($limit == 1 and count($res)) {
                $res = $res[0];
            }

            $this->entities[$classKey][$key] = !empty($res) ? $res : null;
        }

        return $this->entities[$classKey][$key];
    }


    /**
     * @param string $classKey
     * @param array $ids
     * @return array|xPDOObject
     */
    public function getEntitiesByIds($classKey, $ids)
    {
        return $this->getEntities($classKey, array('id:IN' => $ids));
    }


    public function getEntitiesInfoWithTV($classKey, $ids, $delimiter = '||')
    {
        $entities = array();
        if (!is_array($ids)) {
            $ids = explode($delimiter, $ids);
        }
        $ents = $this->getEntitiesByIds($classKey, $ids);
        foreach ($ents as $entity) {
            $entities[] = array_merge($entity, $this->getTVs($entity));
        }
        return $entities;
    }


    /**
     * @param array|modResource $resource
     * @return array
     */
    public function getTVs($resource)
    {
        $tvValues = array();
        if (is_object($resource)) {
            $resource = $resource->toArray();
        }

        $q = $this->modx->newQuery('modTemplateVar');
        $q->innerJoin('modTemplateVarTemplate', 'modTemplateVarTemplate',
            'modTemplateVarTemplate.tmplvarid = modTemplateVar.id');
        $q->select(array('modTemplateVar' => '*'));
        $q->where(array('modTemplateVarTemplate.templateid' => $resource['template']));
        $tvs = $this->query($q);

        foreach ($tvs as $tv) {
            $tvValues[$tv['name']] = $tv['default_text'];
        }

        $q = $this->modx->newQuery('modTemplateVarResource');
        $q->innerJoin('modTemplateVar', 'modTemplateVar', 'modTemplateVarResource.tmplvarid = modTemplateVar.id');
        $q->select(array('modTemplateVarResource' => '*', 'modTemplateVar' => '*'));
        $q->where(array('modTemplateVarResource.contentid' => $resource['id']));
        $tvs = $this->query($q);

        foreach ($tvs as $tv) {
            $tvValue = $tv['value'];
            if (strpos($tvValue, '{') === 0 or strpos($tvValue, '[') === 0) {
                $tvValue = $this->modx->fromJSON($tvValue);
            }
            $tvValues[$tv['name']] = $tvValue;
        }
        return $tvValues;
    }


    /**
     * Example connect miniShop2 service
     *
     * @return null|object
     */
//    public function ms2()
//    {
//        $ms2 = $this->modx->getService('minishop2');
//        $ms2->initialize($this->modx->context->key, array('json_response' => false));
//        return $ms2;
//    }

}