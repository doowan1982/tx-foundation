<?php
namespace Tesoon\Foundation\Models;

/**
 * Class Lists
 * @package Tesoon\Foundation\Models
 */
final class Lists extends Data implements \Iterator{

    private $list = [];
    private $offset = 0;

    private $class = [];
    /**
     * @param array|string 指定当前列表需要绑定的Data子类class
     */
    public function __construct($class = '')
    {
        $this->class = $class;
    }

    /**
     * 返回当前List持有的Data子类class名称
     * @return string
     */
    public function getClass(): string{
        return $this->class;
    }

    /**
     * 移除$data
     * @param int $offset
     */
    public function remove(int $offset){
        if($offset < $this->offset){
            $this->offset--;
        }
        array_splice($this->list, $offset, 1);
    }

    /**
     * @param int $offset
     * @return Data|null
     */
    public function get(int $offset): ?Data{
        return $this->list[$offset] ?? null;
    }

    /**
     * @return int
     */
    public function size(): int{
        return count($this->list);
    }
    
    /**
     * @inheritdoc
     */
    public function add(Data $data){
        $this->list[] = $data;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->list[$this->offset];
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        $this->offset++;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return isset($this->list[$this->offset]);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->offset = 0;
    }

}