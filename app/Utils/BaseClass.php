<?php

if (!class_exists('QueryBuilder')) {
    /**
     * Class QueryBuilder
     *
     * Đây là một lớp xây dựng truy vấn SQL cho phép thêm các điều kiện tìm kiếm và nhóm chúng.
     * Hỗ trợ các điều kiện cơ bản như '=', '<', '>', 'LIKE', v.v., và cho phép nhóm các điều kiện với AND hoặc OR.
     */
    class QueryBuilder
    {
        protected $params = [];
        protected $groups = []; // Mảng lưu trữ các nhóm điều kiện
        protected $allowedConditions = ['=', '>', '<', '>=', '<=', '<>', '!=', 'LIKE', 'IS NOT NULL', 'IS NULL', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN']; // Mảng các điều kiện được chấp nhận

        public function __construct($params = [])
        {
            $this->params = $params;
        }

        /**
         * Thêm một tham số điều kiện vào truy vấn.
         *
         * @param string $field Tên trường.
         * @param string $condition Điều kiện ('=', '>', '<', 'LIKE', v.v.).
         * @param mixed $value Giá trị so sánh.
         * @param string $logic Loại logic ('AND', 'OR') để kết nối điều kiện này với điều kiện sau đó.
         * @param string|null $group Tên nhóm nếu tham số này là một phần của một nhóm.
         *
         *  @throws InvalidArgumentException Khi điều kiện không được hỗ trợ.
         */
        public function addParam($field, $condition, $value = '', $logic = 'AND', $group = null)
        {
            if (!in_array(strtoupper($condition), $this->allowedConditions)) {
                throw new \InvalidArgumentException("Condition không được hỗ trợ: $condition. Chỉ các điều kiện sau được chấp nhận: " . implode(', ', $this->allowedConditions));
            }
            if (!is_string($field) || !is_string($condition)) {
                throw new \InvalidArgumentException("Field và Condition phải là chuỗi.");
            }
            if (!in_array(strtoupper($logic), ['AND', 'OR'])) {
                throw new \InvalidArgumentException("Logic phải là 'AND' hoặc 'OR'.");
            }
            $param = [
                'field' => $field,
                'condition' => strtoupper($condition),
                'value' => $value,
                'logic' => strtoupper($logic)
            ];
            if ($group) {
                if (!isset($this->groups[$group])) {
                    throw new \InvalidArgumentException("Nhóm không tồn tại: $group");
                }
                $this->groups[$group][] = $param;
            } else {
                $this->params[] = $param;
            }
        }

        /**
         * Bắt đầu một nhóm mới để nhóm các điều kiện.
         *
         * @param string $groupName Tên nhóm.
         *
         *  @throws InvalidArgumentException Khi tên nhóm đã tồn tại.
         */
        public function beginGroup($groupName)
        {
            if (isset($this->groups[$groupName])) {
                throw new \InvalidArgumentException("Nhóm đã tồn tại: $groupName");
            }
            $this->groups[$groupName] = [];
        }

        /**
         * Kết thúc một nhóm điều kiện và thêm nó vào các tham số chính.
         *
         * @param string $groupName Tên của nhóm.
         * @param string $logic Logic để kết nối nhóm này với truy vấn chính ('AND' hoặc 'OR').
         *
         *  @throws InvalidArgumentException Nếu nhóm không tồn tại hoặc logic không hợp lệ.
         */
        public function endGroup($groupName, $logic = 'AND')
        {
            if (!isset($this->groups[$groupName])) {
                throw new \InvalidArgumentException("Nhóm không tồn tại: $groupName");
            }
            if (!in_array(strtoupper($logic), ['AND', 'OR'])) {
                throw new \InvalidArgumentException("Logic phải là 'AND' hoặc 'OR'.");
            }
            $this->params[] = [
                'group' => $groupName,
                'logic' => strtoupper($logic),
                'content' => $this->groups[$groupName]
            ];
            unset($this->groups[$groupName]);
        }

        /**
         * Xây dựng câu điều kiện truy vấn sql.
         *
         * @return array Một mảng chứa 'where' và 'queryParams'.
         */
        public function buildQuery()
        {
            if (empty($this->params)) {
                return [];
            }
            $whereClauses = [];
            $queryParams = [];
            $lastLogic = 'AND';
            foreach ($this->params as $param) {
                if (isset($param['group'])) {
                    $groupClause = $this->processGroup($param['content'], $queryParams);
                    $whereClauses[] = ($whereClauses ? " $lastLogic " : "") . "($groupClause)";
                    $lastLogic = $param['logic'];
                } else {
                    $clause = $this->processCondition($param, $queryParams);
                    $whereClauses[] = ($whereClauses ? " $lastLogic " : "") . $clause;
                    $lastLogic = $param['logic'];
                }
            }
            $where = implode('', $whereClauses);

            return [
                'where' => $where,
                'params' => $queryParams
            ];
        }

        /**
         * Xử lý một nhóm điều kiện.
         *
         * @param array $groupParams Các tham số của nhóm.
         * @param array &$queryParams Tham chiếu đến mảng các tham số truy vấn.
         *
         * @return string Chuỗi điều kiện của nhóm.
         */
        protected function processGroup($groupParams, &$queryParams)
        {
            $groupClauses = [];
            $lastLogic = 'AND';
            foreach ($groupParams as $param) {
                $clause = $this->processCondition($param, $queryParams);
                $groupClauses[] = ($groupClauses ? " $lastLogic " : "") . $clause;
                $lastLogic = $param['logic'];
            }
            return implode('', $groupClauses);
        }

        /**
         * Xử lý một điều kiện riêng lẻ.
         *
         * @param array $param Chi tiết của tham số.
         * @param array &$queryParams Tham chiếu đến mảng các tham số truy vấn.
         *
         * @return string Chuỗi điều kiện.
         */
        protected function processCondition($param, &$queryParams)
        {
            extract($param);
            $rs = '';
            if (is_array($value)) {
                if (in_array($condition, ['BETWEEN', 'NOT BETWEEN']) && isset($value['start'], $value['end'])) {
                    $queryParams[] = $value['start'];
                    $queryParams[] = $value['end'];
                    $rs = "($field $condition ? AND ?)";
                } else {
                    $placeholders = implode(', ', array_fill(0, count($value), '?'));
                    foreach ($value as $val) {
                        $queryParams[] = $val;
                    }
                    $rs = "$field $condition ($placeholders)";
                }
            } elseif (in_array($condition, ['IS NOT NULL', 'IS NULL'])) {
                $rs = "$field $condition";
            } else {
                $queryParams[] = $value;
                $rs = "$field $condition ?";
            }
            return $rs;
        }
    }
}

if (!class_exists('BuildPagination')) {
    /**
     * Class BuildPagination
     *
     * This class is used to build pagination for database queries.
     * It handles various aspects such as total records, current page,
     * total pages, data, and other pagination-related information.
     */
    class BuildPagination
    {
        public $data = array();
        public $from = 0;
        public $to = 0;
        public $total = 0;
        public $totalPages = 0;
        public $currentPage = 0;
        public $url = null;
        public $currentUrl = null;
        public $params = null;
        public $limit = 50;
        protected $joins = [];
        protected $sorts = [];
        protected $groups = [];
        protected $table = '';
        protected $where = [];
        protected $selects = ['*'];
        protected $count = '*';
        protected $allowedConditions = ['=', '>', '<', '>=', '<=', '<>', '!=', 'LIKE', 'IS NOT NULL', 'IS NULL', 'BETWEEN', 'NOT BETWEEN', 'IN', 'NOT IN'];
        protected $typeOfJoin = ['INNER', 'LEFT', 'RIGHT'];
        protected $having = [];

        /**
         * Build pagination for the given parameters.
         *
         * @param int $page The current page number.
         * @param int $limit The number of records per page.
         * @param string $url The base URL for the pagination links.
         * @param string $params Additional parameters to be appended to the URL.
         * @param bool $all If true, retrieve all records without pagination.
         *
         *  @throws InvalidArgumentException If the table is not set.
         */
        public function build($page, $limit, $url, $params, $all = false)
        {
            if (!empty($this->table)) {
                $this->limit = $limit;
                $this->url = $url;
                $this->currentUrl = $url . $params . $page;
                $this->params = $params;
                $this->total = $this->getTotal();
                if ($all) {
                    $this->setAttribute('all', true);
                    $this->currentPage = 1;
                    $this->totalPages = 1;
                    $this->data = $this->getData(true);
                } else {
                    $totalPages = ceil($this->total / $limit);
                    $this->totalPages = $totalPages == 0 ? 1 : $totalPages;
                    if ($page > $this->total) {
                        $this->currentPage = $this->totalPages;
                    } else {
                        $this->currentPage = $page;
                    }
                    $this->data = $this->getData();
                }
                $this->from = $this->total == 0 ? 0 : ($this->currentPage - 1) * $limit + 1;
                $this->to = ($this->currentPage - 1) * $limit + sizeof($this->data);
            } else {
                throw new \InvalidArgumentException('Table must be set');
            }
        }
        public function sum($filed)
        {
            $sum = DB::table($this->table);
            $sum = $this->applyJoins($sum);
            $sum = $this->applyGroupBy($sum);
            $sum = $this->applyHaving($sum);
            $sum = $this->applyWhere($sum)->sum($filed);
            return $sum;
        }
        protected function getTotal(): int
        {
            $total = DB::table($this->table);
            $total = $this->applyJoins($total);
            $total = $this->applyGroupBy($total);
            $total = $this->applyHaving($total);
            $total = $this->applyWhere($total)->select("COUNT(" . $this->count . ") AS total")->get();
            return (empty($this->groups)) ? $total[0]->total : sizeof($total);
        }
        /**
         * Get the data based on the current query.
         *
         * @param bool $all If true, retrieve all records without pagination.
         * @return array The retrieved data.
         */
        protected function getData($all = false): array
        {
            $data = DB::table($this->table);
            $data = $this->applyJoins($data);
            $data = $this->applyWhere($data);
            $data = $this->applyGroupBy($data);
            $data = $this->applyHaving($data);
            $data = $this->applyLimit($data, $all);
            $data = $this->applySort($data)->select(...$this->selects)->get();
            return $data ?? [];
        }

        protected function applyJoins($query)
        {
            foreach ($this->joins as $join) {
                if (!empty($join[4])) {
                    switch (strtoupper($join[4])) {
                        case 'INNER':
                            $query->join($join[0], $join[1], $join[2], $join[3]);
                            break;
                        case 'LEFT':
                            $query->leftJoin($join[0], $join[1], $join[2], $join[3]);
                            break;
                        case 'RIGHT':
                            $query->rightJoin($join[0], $join[1], $join[2], $join[3]);
                            break;
                        default:
                            $query->join($join[0], $join[1], $join[2], $join[3]);
                            break;
                    }
                } else {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            return $query;
        }

        protected function applySort($query)
        {
            foreach ($this->sorts as $sort) {
                $query->orderBy($sort[0], $sort[1]);
            }
            return $query;
        }
        protected function applyGroupBy($query)
        {
            foreach ($this->groups as $group) {
                $query->groupBy($group);
            }
            return $query;
        }
        protected function applyWhere($query)
        {
            if (!empty($this->where)) {
                $query->whereRaw($this->where['where'], $this->where['params']);
            }
            return $query;
        }
        protected function applyHaving($query)
        {
            foreach ($this->having as $having) {
                $query->having($having[0], $having[1], $having[2]);
            }
            return $query;
        }
        protected function applyLimit($query, $all = false)
        {
            if ($all) {
                return $query;
            }
            return $query->limit(($this->currentPage - 1) * $this->limit, $this->limit);
        }

        /**
         * Set an attribute of the query builder.
         *
         * This method allows you to set custom attributes to the query builder.
         * The attributes can be used to store additional information or perform custom operations.
         * @param string $name  The name of the attribute to set.
         * @param mixed  $value The value to assign to the attribute.
         *  @throws InvalidArgumentException If the attribute name already exists.
         *  @throws InvalidArgumentException If the attribute name is not a string.
         */
        public function setAttribute($name, $value)
        {
            if (is_string($name)) {
                if (property_exists($this, $name)) {
                    throw new \InvalidArgumentException("Property '$name' exist");
                }
                $this->$name = $value;
            } else {
                throw new \InvalidArgumentException('Name must be string');
            }
        }
        /**
         * Set the table name for the query builder.
         *
         * @param string $table The name of the table to set.
         *  @throws InvalidArgumentException If the table name is not a string.
         */
        public function setTable($table)
        {
            if (is_string($table)) {

                $this->table = $table;
            } else {
                throw new \InvalidArgumentException('table must be string');
            }
        }
        /**
         * Set the SELECT clause for the query builder.
         *
         * @param array $selects An array of column names to select.
         *
         *  @throws InvalidArgumentException If the select names are not strings.
         */
        public function setSelect($selects)
        {
            $this->selects = $selects;
        }

        /**
         * Set the JOIN clause for the query builder.
         *
         * @param array $joins An array of join conditions. Each join condition is an array with 4 elements:
         *                      - 0: The table name to join.
         *                      - 1: The flied of table join.
         *                      - 2: The join condition.
         *                      - 3: The flied of joined table.
         *                      - 4: The type of join, which can be 'INNER', 'LEFT', or 'RIGHT'..
         *
         *  @throws InvalidArgumentException If the 'joins' parameter is not an array.
         *  @throws InvalidArgumentException If any join condition does not have 4 or 5 elements or any element is empty.
         */
        public function setJoins($joins)
        {
            $this->joins = [];
            if (!is_array($joins)) {
                throw new \InvalidArgumentException('Joins must be an array');
            }

            foreach ($joins as $index => $join) {
                if (count($join) !== 4 && count($join) !== 5) {
                    throw new \InvalidArgumentException('Each join must have 4 or 5 parameters');
                }
                $this->validateJoinParameters($join, $index);
            }
            $this->joins = $joins;
        }
        /**
         * Validates join parameters for the query builder.
         *
         * @param array $join  The join condition.
         * @param int   $index The index of the join condition in the joins array.
         *
         *  @throws InvalidArgumentException If any join condition does not have 4 elements or any element is empty.
         *  @throws InvalidArgumentException If the join condition contains an unsupported condition or join type.
         *
         * @return void
         */
        protected function validateJoinParameters($join, $index)
        {
            foreach ($join as $key => $param) {
                if (!is_string($param) || empty($param)) {
                    throw new \InvalidArgumentException("Join parameter at index $index, key $key must be a non-empty string");
                }
                if ($key == 2 && !in_array($param, $this->allowedConditions)) {
                    throw new \InvalidArgumentException("Unsupported condition: $param. Only the following conditions are accepted: " . implode(', ', $this->allowedConditions));
                }
                if ($key == 4 && !in_array($param, $this->typeOfJoin)) {
                    throw new \InvalidArgumentException("Unsupported join type: $param. Only the following join types are accepted: " . implode(', ', $this->typeOfJoin));
                }
            }
        }
        /**
         * Set the sort for the query builder.
         *
         * @param string $filed The name of the column to sort by.
         * @param string $type  The type of sorting. It can be either 'ASC' or 'DESC'. Default is 'ASC'.
         *
         *  @throws InvalidArgumentException If the field name is not a string.
         *  @throws InvalidArgumentException If the type is not 'ASC' or 'DESC'.
         */
        public function setSort($filed, $type = 'ASC')
        {
            if (is_string($filed) && in_array(strtoupper($type), ['ASC', 'DESC'])) {
                $this->sorts[] = [$filed, $type];
            } else {
                throw new \InvalidArgumentException('Filed must be string and type must be "ASC" or "DESC"');
            }
        }
        /**
         * Set the GROUP BY clause for the query builder.
         *
         * @param string $group The name of the column to group by.
         *  @throws InvalidArgumentException If the group name is not a string.
         */
        public function setGroupBy($group)
        {
            if (is_string($group) && !empty($group)) {
                $this->groups[] = $group;
            } else {
                throw new \InvalidArgumentException('Group name by must be string');
            }
        }

        /**
         * Set the WHERE condition for the query builder.
         *
         * @param array|string $where The WHERE condition. It can be a string or an array with 'where' and 'params' keys.
         *  @throws InvalidArgumentException If the 'where' parameter is not a string or an array with 'params' specified.
         */
        public function setWhere($where)
        {
            if (is_string($where) && !empty($where)) {
                $this->where = ['where' => $where, 'params' => []];
            } elseif (is_array($where) && !empty($where['where'])) {
                $where_explode = explode('?', $where['where']);
                if (count($where_explode) == (count($where['params']) + 1)) {
                    $this->where = $where;
                } else {
                    throw new \InvalidArgumentException("Error syntax method where: count params != count ? in argument where: " . $where['where'] . " <br> params: " . json_encode($where['params']));
                }
            } else {
                throw new \InvalidArgumentException('where by must be string or array with params specified');
            }
        }
        public function having($filed, $operator, $value = false)
        {
            if ($value === false) {
                $this->having[] = [$filed, '=', $operator];
            } else {
                if (in_array($operator, $this->allowedConditions)) {
                    $this->having[] = [$filed, $operator, $value];
                } else {
                    throw new \InvalidArgumentException("Unsupported condition: $operator. Only the following conditions are accepted: " . implode(', ', $this->allowedConditions));
                }
            }
        }
        public function setCount($count)
        {
            if (is_string($count) && !empty($count)) {
                $this->count = $count;
            } else {
                throw new \InvalidArgumentException('count by must be string and not empty');
            }
        }
    }
}

if (!class_exists('Session')) {
    class Session
    {
        public $flush = false;

        public function __construct($flush = false){
            $this->flush = $flush;
        }
    
        public function set($key = null, $value = null)
        {
            if ($key === null) {
                return $this->flush ? $_SESSION['flush'] ?? [] : $_SESSION;
            }
    
            if ($value === null) {
                if ($this->flush) {
                    return $_SESSION['flush'][$key] ?? null;
                } else {
                    return $_SESSION[$key] ?? null;
                }
            }
    
            if ($this->flush) {
                $_SESSION['flush'][$key] = $value;
            } else {
                $_SESSION[$key] = $value;
            }
    
            return new static();
        }
    
        public function has($key)
        {
            if ($this->flush) {
                return isset($_SESSION['flush'][$key]);
            } else {
                return isset($_SESSION[$key]);
            }
        }
        public function get($key)
        {
            if ($this->flush) {
                return $_SESSION['flush'][$key] ?? NULL;
            } else {
                return $_SESSION[$key] ?? NULL;
            }
        }
    
        public function remove($key){
            if($this->flush){
                unset($_SESSION['flush'][$key]);
                return;
            }
            unset($_SESSION[$key]);
        }
        public static function clearFlush()
        {
            unset($_SESSION['flush']);
            return new static();
        }
    
        public static function __callStatic($name, $arguments)
        {
            if (method_exists(static::class, $name)) {
                return forward_static_call_array([static::class, $name], $arguments);
            }
    
            throw new \BadMethodCallException("Method $name does not exist.");
        }
    }
    
}
