<?php
namespace datamodel;

class BaseDataModel extends  \think\Model
{
    /**
     * 增加实体，返回实体的主键
     * @param array $data
     * @return int|string
     */
    public function saveEntityAndGetId(array $data)
    {
        return $this->insertGetId($data);

    }

    /**
     * 根据条件返回修改的行数，没有修改返回0
     * @param array $where
     * @param array $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updateByWhere(array $where, $data)
    {
        return $this->where($where)->update($data);

    }
    /**
     * 根据条件，返回成功自增的行数
     * @param array $where
     * @param string $field
     * @param int $step
     * @return int|true
     * @throws \think\Exception
     */
    public function updateForInc(array $where,string $field,$step=1)
    {
        return $this->where($where)->setInc($field,$step);
    }

    /**
     * 根据条件，返回成功自减的行数
     * @param array $where
     * @param string $field
     * @param int $step
     * @return int|true
     * @throws \think\Exception
     */
    public function updateForDesc(array $where,string $field, $step)
    {
        return  $this->where($where)->setDec($field,$step);

    }

    /**
     * 根据主键ID查找实体，不存在返回null
     * @param $id
     * @return BaseDataModel|null
     * @throws \think\exception\DbException
     */
    public function getById($id)
    {
        return self::get($id);
    }
}