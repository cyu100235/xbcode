<?php
declare(strict_types = 1);

namespace hg\apidoc\export\utils;


class Helper
{

    /**
     * 将MySQL数据类型映射为Swagger/OpenAPI数据类型
     *
     * @param string $mysqlType MySQL数据类型（例如：bigint(11)、char(32)、varchar(255) 等）
     * @return array 返回Swagger类型（type 和 format）
     */
    static public function mySQLTypeToSwaggerType($mysqlType) {
        if (empty($mysqlType)){
            return ['type' => 'string'];
        }
        // 去掉类型后面的长度和其他修饰符，如(11), (255), UNSIGNED等
        $cleanType = strtolower(preg_replace('/\(.+\)/', '', $mysqlType));
        $typeMap = [
            // 字符串类型
            'varchar'  => ['type' => 'string'],
            'char'     => ['type' => 'string'],
            'text'     => ['type' => 'string'],
            'tinytext' => ['type' => 'string'],
            'mediumtext'=> ['type' => 'string'],
            'longtext' => ['type' => 'string'],
            'enum'     => ['type' => 'string'],
            'set'      => ['type' => 'string'],

            // 数字类型
            'int'      => ['type' => 'integer', 'format' => 'int32'],
            'smallint' => ['type' => 'integer', 'format' => 'int32'],
            'tinyint'  => ['type' => 'boolean'], // 通常tinyint(1)表示布尔值
            'bigint'   => ['type' => 'integer', 'format' => 'int64'],
            'mediumint'=> ['type' => 'integer', 'format' => 'int32'],

            // 浮点数/小数类型
            'float'    => ['type' => 'number', 'format' => 'float'],
            'double'   => ['type' => 'number', 'format' => 'double'],
            'decimal'  => ['type' => 'string'], // 避免精度丢失，使用string

            // 日期/时间类型
            'date'     => ['type' => 'string', 'format' => 'date'],
            'datetime' => ['type' => 'string', 'format' => 'date-time'],
            'timestamp'=> ['type' => 'string', 'format' => 'date-time'],
            'time'     => ['type' => 'string', 'format' => 'time'],
            'year'     => ['type' => 'integer', 'format' => 'int32'],

            // 其他类型
            'json'     => ['type' => 'object'], // JSON 格式
            'blob'     => ['type' => 'string', 'format' => 'binary'], // 用于文件
            'binary'   => ['type' => 'string', 'format' => 'binary'],
            'varbinary'=> ['type' => 'string', 'format' => 'binary'],

            'file'=> ['type' => 'file', 'format' => 'binary'],
            'files'=> ['type' => 'file', 'format' => 'binary'],
            'object'=> ['type' => 'object'],
            'array'=> ['type' => 'array'],
            'tree'=> ['type' => 'array'],
        ];

        // 如果找到类型的直接匹配项，则返回映射
        if (isset($typeMap[$cleanType])) {
            return $typeMap[$cleanType];
        }

        // 如果类型不在映射表中，返回默认的字符串类型
        return ['type' => 'string'];
    }


}