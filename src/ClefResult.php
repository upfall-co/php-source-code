<?php


namespace Clef;



class ClefResult
{
    private $count = 0;

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
    private $resultSet = null;
    private $max = 0;
    private $min = 0;
    private $total = 0;
    private $errMsg = '';
    private $errCode = '';
    private $result = false;
    private $lastId = null;

    public function __construct($count=0,$resultSet=null ,$result = false , $errMsg = '' , $errCode = '',$lastId=null)
    {
        $this->count = $count;
        $this->resultSet = $resultSet;
        $this->result = $result;
        $this->errMsg = $errMsg;
        $this->errCode = $errCode;
        $this->lastId = $lastId;
    }
    /**
     * @return string
     */
    public function getLastId(): string
    {
        return $this->lastId;
    }

    /**
     * @param string $lastId
     */
    public function setLastId(string $lastId): void
    {
        $this->lastId = $lastId;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }

    /**
     * @param bool $result
     */
    public function setResult(bool $result): void
    {
        $this->result = $result;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return array
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * @param array $resultSet
     */
    public function setResultSet($resultSet): void
    {
        $this->resultSet = $resultSet;
    }

    /**
     * @return string
     */
    public function getErrMsg(): string
    {
        return $this->errMsg;
    }

    /**
     * @param string $errMsg
     */
    public function setErrMsg(string $errMsg): void
    {
        $this->errMsg = $errMsg;
    }

    /**
     * @return string
     */
    public function getErrCode(): string
    {
        return $this->errCode;
    }

    /**
     * @param string $errCode
     */
    public function setErrCode(string $errCode): void
    {
        $this->errCode = $errCode;
    }




}