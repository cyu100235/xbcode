<?php
declare(strict_types=1);

namespace hg\apidoc\export;

use hg\apidoc\export\utils\ConfigProvider;
use hg\apidoc\export\utils\Helper as ExportHelper;
use hg\apidoc\utils\Helper;

class ExportSwagger
{

    protected $config;

    protected $definitions=[];


   public function __construct($config)
    {
        $this->config = $config;
    }

    protected function handleApidocData($apiData)
    {
        $controllerList=[];
        foreach ($apiData as $item) {
            if (!empty($item['controller'])){
                $controllerList[] = $item;
            }else if(!empty($item['children']) && count($item['children'])>0){
                $childrenData =  $this->handleApidocData($item['children'],$controllerList);
                if (!empty($childrenData) && count($childrenData)>0){
                    $controllerList = array_merge($controllerList,$childrenData);
                }
            }
        }
        return $controllerList;
    }

    /**
     * 处理参数的 schema
     * @return void
     */
    protected function handleParamSchema($params,$apiItem)
    {
        $properties = [];
        $required = [];
        foreach ($params as $paramItem) {
            if (empty($paramItem['name']) || !is_string($paramItem['name'])){
                continue;
            }

            $typeData = ['type' => 'string'];
            if (!empty($paramItem['type'])){
                $typeData = ExportHelper::mySQLTypeToSwaggerType($paramItem['type']);
            }else if (!empty($paramItem['children']) && count($paramItem['children'])>0){
                $typeData = ['type' => 'object'];
            }
            $fieldData=[
                'type'=>$typeData['type'],
                'description'=>!empty($paramItem['desc'])?$paramItem['desc']:'',
            ];
            if (!empty($typeData['format'])){
                $fieldData['format'] = $typeData['format'];
            }

            if (isset($paramItem['require']) && $paramItem['require']===true){
                $required[]=$paramItem['name'];
            }
            if (!empty($paramItem['children']) && count($paramItem['children'])>0){
                $childrenSchema = $this->handleParamSchema($paramItem['children'],$apiItem);

                // 处理ref
                if (
                    !empty($paramItem['ref']) &&
                    empty($paramItem['withoutField']) &&
                    empty($paramItem['field']) &&
                    !(!empty($paramItem['type']) && $paramItem['type']=='tree')
                ){
                    $refPath = "";
                    $refMothod = "";
                    if (is_array($paramItem['ref'])){
                        $refPath=$paramItem['ref'][0];
                        if (count($paramItem['ref'])>1){
                            $refMothod=$paramItem['ref'][1];
                        }
                    }else{
                        $refPath=$paramItem['ref'];
                    }
                    $refKey = str_replace("\\", ".", $refPath);
                    $refKey = str_replace("@", ".", $refKey);
                    if ($refMothod){
                        $refKey =$refKey.".".$refMothod;
                    }
                    if (empty($this->definitions[$refKey])){
                        $definitionItem = null;
                        if ($typeData['type']=='object'){
                            $definitionItem = [
                                'type'=>$typeData['type'],
                                'properties'=>$childrenSchema['properties'],
                                'required'=>$childrenSchema['required'],
                            ];
                        }else if($typeData['type']=='array'){
                            $definitionItem = [
                                'type'=>$typeData['type'],
                                'items'=>[
                                    'type'=>'object',
                                    'properties'=>$childrenSchema['properties'],
                                    'required'=>$childrenSchema['required'],
                                ],
                            ];
                        }
                        if ($definitionItem){
                            $this->definitions[$refKey] = $definitionItem;
                        }

                    }

                    if ($typeData['type']=='object'){
                        $fieldData['$ref'] = '#/components/schemas/'.$refKey;
                    }else if($typeData['type']=='array'){
                        $fieldData['items'] = [
                            '$ref'=>'#/components/schemas/'.$refKey
                        ];
                    }
                }else if ($typeData['type']=='object'){
                    $fieldData['properties'] = $childrenSchema['properties'];
                    $fieldData['required'] = $childrenSchema['required'];
                }else if($typeData['type']=='array'){
                    $fieldData['items'] = [
                        'type'=>'object',
                        'properties'=>$childrenSchema['properties'],
                        'required'=>$childrenSchema['required'],
                    ];
                }
            }else if ($typeData['type']=='array'){
                $fieldData['items'] = [
                    'type'=>!empty($paramItem['childrenType'])?$paramItem['childrenType']:'string',
                ];
            }

            $properties[$paramItem['name']] = $fieldData;
        }
        return [
            'properties'=>$properties,
            'required'=>$required,
        ];
    }


    protected function getTagName($controller)
    {
        $names = [];
        if (!empty($controller['appKey']) && !empty($this->config['controller_tags_rule']) && in_array('app',$this->config['controller_tags_rule'])){
            $names[] = str_replace(",", ".", $controller['appKey']);
        }
        if (!empty($controller['group']) && !empty($this->config['controller_tags_rule']) && in_array('group',$this->config['controller_tags_rule'])){
            $names[] = $controller['group'];
        }
        $names[] = $controller['controller'];
        return implode(".", $names);
    }
    public function exportJson($apidocConfig,$apidocData)
    {
        $config = $this->config;
        $this->definitions = [];

        $tags = [];
        $paths = [];
        $controllerList = $this->handleApidocData($apidocData['apiData']);
        foreach ($controllerList as $item) {
            if(!empty($item['children']) && count($item['children'])>0){
                $tag = [
                    'name'=>$this->getTagName($item),
                    'description'=>$item['title'],
                ];
                $tags[]=$tag;
                foreach ($item['children'] as $apiItem) {
                    if (empty($apiItem['method'])){
                        continue;
                    }
                    if (!empty($paths[$apiItem['url']])){
                        continue;
                    }
                    $apiPath = [];
                    $apiData = [
                        'tags'=>[$tag['name']],
                        'summary'=>!empty($apiItem['title'])?$apiItem['title']:'',
                        'description'=>!empty($apiItem['desc'])?$apiItem['desc']:'',
                    ];
                    // 安全验证字段
                    $authApiKeyField = '';
                    if (
                        !empty($config['swagger_options']) &&
                        !empty($config['swagger_options']['components']) &&
                        !empty($config['swagger_options']['components']['securitySchemes']) &&
                        !empty($config['swagger_options']['components']['securitySchemes']['api_key']) &&
                        !empty($config['swagger_options']['components']['securitySchemes']['api_key']['name'])
                    ){
                        $authApiKeyField = $config['swagger_options']['components']['securitySchemes']['api_key']['name'];
                    }
                    $parameters = [];
                    if(!empty($apiItem['header']) && count($apiItem['header'])>0){
                        foreach ($apiItem['header'] as $headerItem) {
                            //自动过滤请求头中，全局安全验证字段，避免必填验证
                            if (!empty($authApiKeyField) && $authApiKeyField==$headerItem['name']){
                                continue;
                            }
                            $typeData = ExportHelper::mySQLTypeToSwaggerType($headerItem['type']);
                            $paramter = [
                                'name'=>$headerItem['name'],
                                'in'=>'header',
                                'required'=>$headerItem['require'],
                                'type'=>$typeData['type'],
                                'description'=>!empty($headerItem['desc'])?$headerItem['desc']:'',
                            ];
                            if (!empty($typeData['format'])){
                                $paramter['format'] = $typeData['format'];
                            }
                            $parameters[]=$paramter;
                        }
                    }
                    // 处理query参数
                    if(!empty($apiItem['query']) && count($apiItem['query'])>0){
                        foreach ($apiItem['query'] as $queryItem) {
                            $typeData = ExportHelper::mySQLTypeToSwaggerType(!empty($queryItem['type'])?$queryItem['type']:'');
                            $paramter = [
                                'name'=>$queryItem['name'],
                                'in'=>'query',
                                'required'=>!empty($queryItem['require'])?$queryItem['require']:false,
                                'type'=>$typeData['type'],
                                'description'=>!empty($queryItem['desc'])?$queryItem['desc']:'',
                            ];
                            if (!empty($typeData['format'])){
                                $paramter['format'] = $typeData['format'];
                            }
                            $parameters[]=$paramter;
                        }
                    }
                    // 处理路由参数
                    if(!empty($apiItem['routeParam']) && count($apiItem['routeParam'])>0){
                        foreach ($apiItem['routeParam'] as $routeItem) {
                            $typeData = ExportHelper::mySQLTypeToSwaggerType($routeItem['type']);
                            $paramter = [
                                'name'=>$routeItem['name'],
                                'in'=>'path',
                                'required'=>$routeItem['require'],
                                'type'=>$typeData['type'],
                                'description'=>!empty($routeItem['desc'])?$routeItem['desc']:'',
                            ];
                            if (!empty($typeData['format'])){
                                $paramter['format'] = $typeData['format'];
                            }
                            $parameters[]=$paramter;
                        }
                    }
                    if(count($parameters)>0){
                        $apiData['parameters'] = $parameters;
                    }

                    // 处理body
                    if(!empty($apiItem['param']) && count($apiItem['param'])>0){
                        $bodyParams = $this->handleParamSchema($apiItem['param'],$apiItem);
                        $contentType = 'application/json';
                        if (!empty($apiItem['contentType'])){
                            $contentType =$apiItem['contentType'];
                        }else if (!empty($apiItem['paramType']) && $apiItem['paramType']=='formdata'){
                            $contentType = 'multipart/form-data';
                        }
                        $apiData['requestBody']=[
                            'required'=>true,
                            "description"=> "",
                            'content'=>[
                                $contentType=>[
                                    'schema'=>[
                                        'type'=>'object',
                                        'properties'=>$bodyParams['properties'],
                                        'required'=>$bodyParams['required']
                                    ]
                                ]
                            ]
                        ];
                    }

                    // 成功响应体
                    $returnedSuccessProperties = [];
                    if(!empty($apiItem['returned']) && count($apiItem['returned'])>0){
                        $returnedSuccessParams = $this->handleParamSchema($apiItem['returned'],$apiItem);
                        $returnedSuccessProperties = $returnedSuccessParams['properties'];
                    }

                    $responses = [];
                    if(!empty($apiItem['responseStatus']) && count($apiItem['responseStatus'])>0){
                        foreach ($apiItem['responseStatus'] as $responseStatusItem) {
                            $paramter = [
                                'description'=>!empty($responseStatusItem['desc'])?$responseStatusItem['desc']:'',
                            ];
                            $contentType = !empty($responseStatusItem['contentType'])?$responseStatusItem['contentType']:'application/json';
                            if ($responseStatusItem['name']=='200'){
                                // 成功
                                $paramter['content'] = [
                                    $contentType=>[
                                        'schema'=>[
                                            'type'=>'object',
                                            'properties'=>$returnedSuccessProperties
                                        ]
                                    ]
                                ];
                            }
                            $responses[$responseStatusItem['name']]=$paramter;
                        }
                    }else{
                        // 未配置响应状态码
                        $responses['200']=[
                            'content'=>[
                                'application/json'=>[
                                    'schema'=>[
                                        'type'=>'object',
                                        'properties'=>$returnedSuccessProperties
                                    ]
                                ]
                            ]
                        ];
                    }
                    $apiData['responses'] = $responses;
                    $methods = !empty($apiItem['method']) && is_array($apiItem['method']) ? $apiItem['method'] : [$apiItem['method']];
                    foreach ($methods as $method) {
                        $apiPath[Helper::lower($method)] = $apiData;
                    }
                    $paths[$apiItem['url']] = $apiPath;
                }
            }
        }

        $json = [
            'openapi'=>'3.0.1',
            'info'=>[
                'title'=>$apidocConfig['title'],
                'description'=>$apidocConfig['desc'],
                'version'=>'1.0.7'
            ],
            'tags'=>$tags,
            'paths'=>$paths,
        ];
        if (!empty($config['swagger_options'])){
            if (
                !empty($config['swagger_options']) &&
                !empty($config['swagger_options']['components'])
            ){
                $config['swagger_options']['components']['schemas'] = $this->definitions;
            }else{
                $json['components'] = [
                    'schemas'=>$this->definitions
                ];
            }
            return array_merge($config['swagger_options'],$json);
        }
        $json['components'] = [
            'schemas'=>$this->definitions
        ];
        return $json;
    }


}
