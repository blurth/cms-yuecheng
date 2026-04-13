<?php

return [
    "store_dir" => 'uploads',       # 文件的存储路径
    "single_limit" => 1024 * 1024 * 2, # 单个文件的大小限制，默认2m
    "total_limit"=> 1024 * 1024 * 20, # 所有文件的大小限制，默认20m
    "nums" => 10,                      # 文件数量限制，默认10
    "include" => [],                   # 文件后缀名的排除项，默认排除[]，即允许所有类型的文件上传
    "exclude" => []  ,                 # 文件后缀名的包括项
    "host" => 'https://yuecheng.jixiangjiaoyu.com/'                   # 文件后缀名的包括项
];
